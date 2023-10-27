
# DOMTable

DOMTable is a lightweight PHP library that simplifies the process of creating tables with minimal code. It utilizes the Document Object Model (DOM) to generate HTML code, allowing you to easily display data in a table format. The library is designed to be simple, efficient, and requires no external dependencies.

## Features

- Create HTML tables from MySQL query results or arrays
- Customize table columns and their display titles
- Manipulate column values before displaying them in the table
- Option to print the table directly to the browser or obtain it as a string

## Installation

To use DOMTable in your PHP project, you can follow these steps:

1. Download the `DOMTable.php` repo as .zip file
2. Place the `DOMTable.php` file in your project directory.
3. Include the file in your PHP script using `require_once 'DOMTable.php';`.

## Usage

### Setting up a table

```php
use Ucscode\DOMTable\DOMTable;

// Instantiate DOMTable with table name
$table = new DOMTable("users");

// Set columns to display on the table
$table->setMultipleColumns([
    "id",
    "username",
    "email"
]);

// Fetch data from MySQL database
$mysql_result = $db->query("SELECT * FROM users");

// Populate the table with data
$table->setData($mysql_result);

// Generate the HTML code for the table
echo $table->build();
```

---

### Populating data with Array

Data may also be populated with array. Consider the example below:

```php
$data = [
    [
        'id' => 1,
        'username' => 'John',
        'email' => 'john@example.com',
    ],
    [
        'id' => 2,
        'username' => 'Jane',
        'email' => 'jane@example.com',
    ],
    // Add more rows as needed
];

$table->setData($data);
```

In this example, the `$data` is an array that contains multiple rows of data, where each row is represented as an associative array. Each key in the associative array corresponds to a column name, and the corresponding value represents the data for that column in the row. You can add as many rows as needed to populate the table with your desired data.

### Modifying column values

You can modify the values of the table columns before they are displayed using the `prepare()` method. The method accepts a callback function as an optional parameter, which allows you to manipulate the data.

```php
use Ucscode\DOMTable\DOMTableInterface;

$table->build(new class () implements DOMTableInterface 
{
    public function forEachItem(array $data): array
    {
        $data['username'] = 'updated username';
        $data['email'] = 'changed@email.com';
        return $data;
    }
});
```

## License

This project is licensed under the [MIT License](https://opensource.org/licenses/MIT).

## Contribution

Contributions to the DOMTable library are welcome! If you find any issues or have suggestions for improvements, please feel free to create an issue or submit a pull request on the [DOMTable GitHub repository](https://github.com/ucscode/domtable).

## Support

If you have any questions or need support regarding the usage of DOMTable, please [open a new issue](https://github.com/ucscode/domtable/issues) on the GitHub repository.

---

With DOMTable, creating tables in PHP becomes effortless, allowing you to focus on your data presentation without the hassle of manual HTML coding. Enjoy the simplicity and flexibility that DOMTable brings to your table generation tasks. Happy coding!
