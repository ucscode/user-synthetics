# SQuery

SQL Syntax Generator for PHP

# Introduction

Orignally made for User Synthetics, SQuery is an independent PHP library that simplifies the process of generating SQL syntax for your database queries. With sQuery, you can build SQL queries using an intuitive and fluent interface, reducing the need to write SQL directly. It supports various SQL operations like `SELECT`, `FROM`, `JOIN`, `WHERE`, `GROUP BY`, and more.

The SQuery class provides a simplified and efficient way to interact with databases in PHP, specifically by easing the CRUD (Create, Read, Update, Delete) operations.

## Features

- SQuery ensures the proper ordering of your SQL syntax, even if the methods are not called in order.
- SQuery supports nearly all functions related to SQL keywords.
- Build SQL queries in a structured and readable manner.
- Supports common SQL operations such as `SELECT`, `FROM`, `JOIN`, `WHERE`, `GROUP BY`, `ORDER BY`, and `LIMIT`.
- Generate complex queries with ease by chaining methods.
- Designed to improve code maintainability and reduce the risk of SQL injection.

## Requirements

- PHP 8.0 or higher.
- A compatible database (e.g., MySQL, MariaDB) and appropriate database extensions (e.g., MySQLi, PDO) to execute the generated queries.

## Installation

```php
git clone https://github.com/ucscode/SQuery
```

## Usage

Include the `SQuery` class in your PHP file:

```php
require_once 'SQueryInterface.php';
require_once 'AbstractSQuery.php';
require_once 'SQuery.php';

use Ucscode\SQuery\SQuery;
```
   
### SELECT SQL EXAMPLE

```php
$query = (new SQuery())->select("u.username")
   ->from("tablename", "u")
   ->where("u.username", "ucscode")
   ->and("product", "model")
   ->and("title", "%gold%", "LIKE")
   ->or('location', '^new', 'RLIKE')
   ->limit(2)
   ->groupBy('u.id DESC')
   ->getQuery();
```

### INSERT SQL EXAMPLE

```php
$data = array(
   'column1' => 'value1', 
   'column2' => 'value2'
);

$query = (new SQuery())->insert('tablename', $data);
```

### UPDATE SQL EXAMPLE

```php
$data = array(
   'column1' => 'value1', 
   'column2' => 'value2'
);

$query = (new SQuery())
   ->update('tablename', $data)
   ->where('tablename.col', '1);
```

#### SETTING ADDITIONAL DATA

To the `INSERT` and `UPDATE`, you can set additional data by using the `set` method

```php
$query
   ->set("key", "value")
   ->set("anotherKey", "anotherValue");
```
### DELETE SQL EXAMPLE

5. To generate a DELETE query, use the `delete()` method:

```php
$query = (new SQuery())
   ->delete('tablename')
   ->where('id', 5);
```

## Warning!

It is important to note that the SQuery library does not automatically sanitize user input. When using the library, it is crucial to sanitize any user-supplied data before passing it as input to the SQuery methods.

## Note

Please note that the `SQuery` class only generates SQL query strings; it does not execute them against a database. To execute these queries, you would need to establish a database connection and use appropriate methods from the MySQLi or PDO libraries.

## License

This project is licensed under the [MIT License](LICENSE).
