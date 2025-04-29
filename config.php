<?php
return [
  'files' => [
    'path/to/input.dump.sql' => 'path/to/output.dump.sql',
  ],
  'replacements' => [
    'https://example.com/' => 'https://staging.example.com/',
    '/var/www/example.com/' => '/var/www/staging.example.com/',
  ],
];
