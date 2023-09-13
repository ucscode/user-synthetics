# SQuery

SQuery is a simple PHP class that provides a convenient way to construct SQL queries. It offers static methods for generating SELECT, INSERT, UPDATE, and DELETE queries, making it easier to interact with databases in PHP.

The SQuery class provides a simplified and efficient way to interact with databases in PHP, specifically by easing the CRUD (Create, Read, Update, Delete) operations.

## Features

- Generate SELECT queries to retrieve data from a table.
- Generate INSERT queries to insert new records into a table.
- Generate UPDATE queries to update existing records in a table.
- Generate DELETE queries to delete records from a table.

## Requirements

- PHP 5.6 or higher.
- A compatible database (e.g., MySQL, MariaDB) and appropriate database extensions (e.g., MySQLi, PDO) to execute the generated queries.

## Usage

1. Include the `SQuery` class in your PHP file:

   ```php
   require_once 'SQuery.php';
   ```
   
<br/>

2. To generate a SELECT query, use the `select()` method:

   ```php
   $query = SQuery::select('tablename', 'condition');
   ```

   Replace `'tablename'` with the name of the table you want to select from, and `'condition'` with the desired condition for filtering the rows.

   You have the flexibility to provide a string or an array as the third parameter of the select method. This allows you to select a specific range of columns in the generated SQL select statement.
   
   ```php
   $query = SQuery::select( "tablename", null, array(
		"count.`name` as flash",
		"username as sender",
		"user.name",
		"count(user.id) as range"
	));
   ```
   Using aliases allow you to provide custom names for the selected columns in the generated SQL statement.\
   The result of executing the above code is:
   
   ```sql
   SELECT 
      `count`.`name` AS `flash`, 
      `username` AS `sender`, 
      `user`.`name`, 
      count(user.id) AS `range` 
   FROM `tablename` WHERE 1
   ```
   
<br/>

3. To generate an INSERT query, use the `insert()` method:

   ```php
   $data = array(
      'column1' => 'value1', 
      'column2' => 'value2'
   );
   $query = SQuery::insert('tablename', $data);
   ```

   Replace `'tablename'` with the name of the table you want to insert into, `$data` with an associative array containing the column-value pairs.\
   The result of executing the above code is:
   
   ```sql
   INSERT INTO `tablename` (`column1`, `column2`) VALUES ('value1', 'value2')
   ```

</br>

4. To generate an UPDATE query, use the `update()` method:

   ```php
   $data = array(
      'column1' => 'value1', 
      'column2' => 'value2'
   );
   $query = SQuery::update('tablename', $data, 1);
   ```

   Replace `'tablename'` with the name of the table you want to update, `$data` with an associative array containing the column-value pairs, `'condition'` with the condition for filtering the rows to be updated.\
   The result of executing the above code is:
   
   ```sql
   UPDATE `tablename` SET `column1` = 'value1', `column2` = 'value2' WHERE 1
   ```
   
<br/>

5. To generate a DELETE query, use the `delete()` method:

   ```php
   $query = SQuery::delete('tablename', 'id = 5');
   ```

   Replace `'tablename'` with the name of the table you want to delete from, and `'condition'` with the condition for filtering the rows to be deleted.\
   The result of executiing the above code is
   
   ```sql
   DELETE FROM `tablename` WHERE id = 5
   ```

<br/>

## Limitations

It's important to note that the SQuery class provided is designed to handle basic CRUD operations and may not support more advanced queries involving JOIN clauses or complex SQL statements.

While the class simplifies the construction of SELECT, INSERT, UPDATE, and DELETE queries for individual tables, it may not be suitable for scenarios where JOIN operations are required to combine data from multiple tables. JOIN queries typically involve more complex logic and require a different approach to handle the join conditions and table relationships.

For more advanced queries involving JOINs or complex SQL statements, it is advisable to utilize comprehensive database abstraction libraries or frameworks. If your requirements exceed the capabilities of the SQuery class, it is highly recommended that you create your own SQL query tailored to your specific needs.

However, for simple CRUD operations on individual tables, the SQuery class can still be a helpful tool to generate the basic SQL queries needed. It offers a convenient and concise way to construct queries without the need to write SQL statements manually.

## Warning

It is important to note that the SQuery library does not automatically sanitize user input. When using the library, it is crucial to sanitize any user-supplied data before passing it as input to the SQuery methods.

## Note

Please note that the `SQuery` class only generates SQL query strings; it does not execute them against a database. To execute these queries, you would need to establish a database connection and use appropriate methods from the MySQLi or PDO libraries.

## License

This project is licensed under the [MIT License](LICENSE).
