<?php

/**
 * This function traverses through all the files in the specified directory (including subdirectories)
 * and extracts localization strings using the __() function.
 * It stores the localization strings and their corresponding translations in an array.
 * Finally, it converts the array to a PHP code representation and writes it to a
 * file named 'localization_strings.php'. currently file stored in public folder of the project
 */


use Nwidart\Modules\Facades\Module;

function extractLocalizationStrings($directory, $moduleName)
{
    // Get all files recursively in the specified directory
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

    // Initialize an array to store the extracted localization strings
    $localizationStrings = [];

    // Iterate over each file in the directory
    foreach ($files as $file) {
        // Skip directories
        if ($file->isDir()) {
            continue;
        }

        // Read the contents of the file
        $contents = file_get_contents($file->getPathname());


        // Extract localization strings excluding . (dot) using regular expression pattern
        preg_match_all('/__\([\'"](.+?)[\'"]\)/', $contents, $matches);

        // // Extract localization strings including . (dot) using regular expression pattern
        // preg_match_all('/__\([\'"]([^\'"]+)[\'"]\)/', $contents, $matches);

        // // Extract localization strings of trans method including . (dot) using regular expression pattern
        // preg_match_all('/trans\([\'"]([^\'"]+)[\'"]\)/', $contents, $matches);

        // If any localization strings are found, store them in the array
        if (!empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $localizationStrings[$match] = __($match);
            }
        }
    }

    // Convert the array to a PHP code representation
    $phpArray = "<?php\n\nreturn " . var_export($localizationStrings, true) . ";\n";

    // Write the PHP code to a file named 'localization_strings.php'
    file_put_contents($moduleName . '-localization_strings.php', $phpArray);
}

$modules = Module::all();

// Loop through the modules
foreach ($modules as $module) {
    $moduleName = $module->getName();
    $modulePath = $module->getPath();
    $moduleResourcePath = '';

    // Now you can use $moduleName, $modulePath, and $moduleResourcePath as needed
    echo "Module Name: $moduleName\n" . "<br />";
    echo "Module Path: $modulePath\n" . "<br />";
    $resouncePath = $modulePath . '/resources/views'; // for views
    // $resouncePath = $modulePath . '/app'; // for app
    if (is_dir($resouncePath)) {
        $moduleResourcePath = $resouncePath;
    }
    echo "Module Resource Path: $moduleResourcePath" . "<br />";
    extractLocalizationStrings($moduleResourcePath, $moduleName);
}

return;
