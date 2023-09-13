# Pairs Class

The Pairs class is a PHP library for creating and managing meta tables that store data using key-value pairs. It provides a convenient way to organize and retrieve data in a structured manner.

## Table of Contents
- [Introduction](#introduction)
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

## Introduction

The Pairs class allows you to create a meta table that consists of key-value pairs. Each row in the table represents a unique combination of key and reference ID, allowing you to store and retrieve data in a flexible and efficient manner. The class also supports linking the meta table to a parent table using foreign keys for data integrity and consistency.

## Features

- Create a meta table with key-value pairs
- Link the meta table to a parent table using foreign keys
- Add or update reference data based on key and reference ID
- Retrieve reference data based on key and reference ID
- Remove reference data based on key and reference ID
- Retrieve all data associated with a specific reference ID or matching a given pattern
- Utilizes the sQuery library for efficient SQL query generation.

## Requirements

- PHP 7.4 or higher
- MySQLi extension
- [sQuery](https://github.com/ucscode/squery) library (required for efficient query generation)

## Installation

1. Download the Pairs class from the ```GitHub repository```.
2. Extract the contents of the archive to your project directory.
3. Include the `pairs.php` file in your PHP script:

   ```php
   require_once 'path/to/sQuery.php';
   require_once 'path/to/pairs.php';
   ```

## Usage

1. Create an instance of the Pairs class by providing a MySQLi object and the name of the meta table:

```php
// Create a new instance of the MySQLi
$mysqli = new mysqli('localhost', 'username', 'password', 'database');

// Create a new instance of the Pairs class
$pairs = new Pairs($mysqli, 'meta_table');
```

2. You can optionally link a parent table to the meta table

```php
// Link the meta table to a parent table
$pairs->linkParentTable('parent_table', 'foreign_key_constraint', 'primary_key', 'CASCADE');
```

3. Use the various methods provided by the Pairs class to interact with the key-value data:

```php
// Add or update a reference data
$pairs->set('key', 'value', $ref);

// Get the value of a reference data
$value = $pairs->get('key', $ref);

// Remove a reference data
$pairs->remove('key', $ref);

// Get all data associated with a reference ID or matching a pattern
$data = $pairs->all($ref, 'pattern');
```

## Contributing

Contributions are welcome! If you find any issues or have suggestions for improvements, please open an issue or submit a pull request on the ```GitHub repository```.

## API Documentation

For detailed API documentation, please refer to the [API Reference](https://ucscode.me/?p=396).

## License

This project is licensed under the MIT License. See the ```LICENSE``` file for more information.
