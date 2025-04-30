# WP DB Migration

Convert WordPress database dump files. For domain migration or staging environment migration.

## Explanation

The WordPress database contains a wide range of environment-related information, such as host URLs, image URLs, and image paths. When you want to copy or migrate a WordPress environment, it is necessary to replace these URLs and paths.

Typically, this replacement is done by opening the exported SQL file in a text editor and performing a string replacement. However, this approach introduces a significant issue.

Some plugins store serialized PHP classes or PHP arrays as strings in the database. These serialized strings have a format like the following:

`s:14:"www.example.jp";`

When a string is serialized, the character count is also recorded. Therefore, it is not enough to simply replace the string; you must also ensure that the character count matches the new string. If the character count is incorrect, plugins may encounter a serialize error and fail to recognize the data properly when loading it.

This program is designed to solve these problems by performing string replacements while ensuring that serialized data remains valid.

## Usage
1. edit `config.php` file
2. run `php main.php`

### Notice

If replacements in config.php is left empty, no replacement will be performed and only the length of characters in the serialized PHP string will be normalized.
