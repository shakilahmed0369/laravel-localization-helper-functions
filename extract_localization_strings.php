<?php

function extractLocalizationStrings(array $directories)
{
    // Initialize an array to store the extracted localization strings
    $localizationStrings = [];

    // Iterate over each directory in the array
    foreach ($directories as $directory) {
        // Get all files recursively in the specified directory
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        // Iterate over each file in the directory
        foreach ($files as $file) {
            // Skip directories
            if ($file->isDir()) {
                continue;
            }

            // Read the contents of the file
            $contents = file_get_contents($file->getPathname());

            // Extract localization strings using regular expression pattern
            preg_match_all('/__\([\'"](.+?)[\'"]\)/', $contents, $matches);

            // If any localization strings are found, store them in the array
            if (!empty($matches[1])) {
                foreach ($matches[1] as $match) {
                    $localizationStrings[$match] = __($match);
                }
            }

            // Extract localization strings of trans method including . (dot) using regular expression pattern
            preg_match_all('/trans\([\'"]([^\'"]+)[\'"]\)/', $contents, $matches);

            // If any localization strings are found, store them in the array
            if (!empty($matches[1])) {
                foreach ($matches[1] as $match) {
                    $localizationStrings[$match] = trans($match);
                }
            }
        }
    }

    // Convert the array to a JSON representation
    $json = json_encode($localizationStrings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // Ensure the 'lang' directory exists
    if (!file_exists('lang')) {
        mkdir('lang', 0777, true);
    }

    // Write the JSON object to a file named 'test.json' in the 'lang' folder
    file_put_contents(lang_path().'/test.json', $json);
}

extractLocalizationStrings([resource_path('views'), base_path('Modules'), base_path('app')]);
