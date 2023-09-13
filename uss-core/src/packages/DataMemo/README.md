
# DataMemo

datamemo is a PHP library that provides a simple and efficient way to store and retrieve data in JSON format. It acts as a data memory, allowing you to save your data as JSON in a file and easily access it when needed. The library is designed to be lightweight and independent, with no external dependencies.

## Why use datamemo?

- **Save and retrieve data**: datamemo allows you to store your data as JSON in a file, making it easy to persist and retrieve information across sessions.
- **NoSQL-like functionality**: datamemo acts as a NoSQL-like data storage, providing a convenient way to work with structured data without the need for a full-fledged database.
- **JSON format compatibility**: The data saved by datamemo is stored in JSON format, which is widely accepted and can be easily used across different platforms and technologies.
- **Seamless integration**: By using datamemo, you can create APIs or data models that can be accessed by other web technologies, allowing for easy data exchange and utilization in various projects.

## Features

- **Save data as JSON**: Store your data as JSON in a file for persistence.
- Automatic data retrieval: When initializing a `datamemo` instance with a file path, the saved data is automatically loaded into the instance, making it readily accessible.
- **Easy data manipulation**: Access and modify the saved data using property syntax.
- Independent and lightweight: datamemo has no external dependencies and is designed to be a standalone library.
- **Clear and delete data**: Clear the entire set of data or delete the associated file when necessary.
- **Error handling**: Retrieve the last error that occurred during the execution of datamemo.

## Installation

1. Download the `DataMemo.php` file.
2. Include the `DataMemo.php` file in your PHP project:

```php
require_once 'path/to/DataMemo.php';
```

## Getting Started

To start using datamemo, follow these simple steps:

1. Create a new `DataMemo` instance by providing the path to the JSON file where you want to store your data:

```php
$file = 'path/to/file.json';
$data = new DataMemo($file);
```

2. Set your desired data using property syntax:

```php
$data->name = 'John Doe';
$data->age = 25;
```

3. Print the formatted JSON data:

```php
$data->pretty_print();
```

4. Save the data to the file:

```php
$data->save();
```

5. Retrieve the data when needed:

```php
$file = 'path/to/file.json';
$data = new DataMemo($file);

echo $data->name; // Output: John Doe
echo $data->age; // Output: 25
```

For more advanced usage, refer to the API reference and the comments within the `DataMemo` class.

## API Reference

For detailed information on the methods available in the `DataMemo` class, please refer to this [Article](https://ucscode.me/?p=357).

## Contributions

Contributions, bug reports, and feature requests are welcome! Please feel free to open issues or submit pull requests.

## License

DataMemo is released under the [MIT License](https://opensource.org/license/mit/).

