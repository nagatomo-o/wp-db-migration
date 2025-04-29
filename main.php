<?php

/**
 * Unescapes a database escaped string
 *
 * @param string $str Escaped string
 * @return string Unescaped string
 */
function db_unescape_string(string $str): string
{
  return str_replace(
    ['\\0', '\\z', '\\r', '\\n', '\\"', '\\\'', '\\\\'],
    ["\x00", "\x16", "\r", "\n", '"', '\'', '\\'],
    $str
  );
}

/**
 * Escapes a string for a database
 *
 * @param string $str String to escape
 * @return string Escaped string
 */
function db_escape_string(string $str): string
{
  return str_replace(
    ['\\', '\'', '"', "\r", "\n", "\x16", "\x00"],
    ['\\\\', '\\\'', '\\"', '\\r', '\\n', '\\z', '\\0'],
    $str
  );
}

// Get config
$config = require __DIR__ . '/config.php';

// Check if the replacements value in config.php is empty
if (empty($config['replacements'])) {
  die('The replacements value in config.php is empty');
}

// Get keywords and replacements
$keywords = array_keys($config['replacements']);
$replacements = array_values($config['replacements']);

// Loop through the files
foreach ($config['files'] as $input_file_path => $output_file_path) {

  // Check if the input file exists
  if (!file_exists($input_file_path)) {
    die("{$input_file_path} does not exist.");
  }

  // Check if the input and output files are the same
  if ($input_file_path == $output_file_path) {
    die("{$input_file_path} and {$output_file_path} are the same file.");
  }

  // Check if the output file exists and delete it if it does
  if (file_exists($output_file_path)) {
    unlink($output_file_path);
  }

  // Open files
  $input_file = fopen($input_file_path, "r") or die("Could not open file: {$input_file_path}");
  $output_file = fopen($output_file_path, "w") or die("Could not open file: {$output_file_path}");

  // Create a pattern to match PHP string objects
  $pattern = '/' . implode('|', [
    's:\d+:\\\".*?\\\";',
    ...array_map(fn($keyword) => preg_quote($keyword, '/'), $keywords)
  ]) . '/';

  // Read file line by line
  while (!feof($input_file)) {

    // Read one line
    $line = fgets($input_file);

    // Replace PHP string objects
    $line = preg_replace_callback($pattern, function ($matches) use ($keywords, $replacements): string {

      // If the string is a PHP string object
      if (str_starts_with($matches[0], 's:')) {
        // Unserialize the string
        $str = @unserialize(db_unescape_string($matches[0]));

        // Check if the string is unserialized successfully
        if ($str === false)
          die("Unserialize Error {$matches[0]}");

        // Replace the keywords with the replacements
        $str = str_replace($keywords, $replacements, $str);

        // Serialize the string
        return db_escape_string(serialize($str));
      }

      // Replace other strings
      return str_replace($keywords, $replacements, $matches[0]);
    }, $line);

    // Write one line
    fwrite($output_file, $line);
  }
  // Close files
  fclose($input_file);
  fclose($output_file);
}
echo "Done\n";
