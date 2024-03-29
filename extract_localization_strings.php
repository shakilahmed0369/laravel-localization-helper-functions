<?php

/**
 * This function traverses through all the files in the specified directory (including subdirectories)
 * and extracts localization strings using the __() function.
 * It stores the localization strings and their corresponding translations in an array.
 * Finally, it converts the array to a PHP code representation and writes it to a
 * file named 'localization_strings.php'. currently file stored in public folder of the project
 */


function extractLocalizationStrings($directory)
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
    file_put_contents('localization_strings.php', $phpArray);
}

// Call the function with the resource_path directory path as an argument
// extractLocalizationStrings(resource_path('views'));
// Call the function with the app_path directory path as an argument
// extractLocalizationStrings(app_path());