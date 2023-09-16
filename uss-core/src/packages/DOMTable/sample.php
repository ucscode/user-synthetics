<!doctype html>
<html>

	<head>
		<meta name='viewport' content='width=device-width, initial-scale=1'>
		<style>
			.table-responsive {
				overflow: auto;
			}
			table {
				width: 100%;
				border-collapse: collapse;
			}
			table th, table td {
				border: 1px solid gray;
				padding: 12px;
				text-transform: capitalize;
			}
			.dt-container .dt-empty {
				font-size: 1.6rem;
				padding: 20px;
				text-transform: uppercase;
				text-align: center;
			}
			h1 {
				margin-top: 4rem;
			}
			.my-table-class tr:nth-child(odd) td {
				background-color: #e9e9e9;
			}
			.my-table-class tr:hover td {
				background-color: #ffefef;
				cursor: pointer;
			}
			.pager {
				margin-top: 2rem;
				text-align: center;
			}
		</style>
	</head>
	
	<body>
	
<?php

    require_once 'DOMTable.php';

$table = new DOMTable('tablename');


/*
    $__columns = array(

        "column_key-1" => "text to display-1",

        "column_key-2" => "text to display-2",

        "serve_as_both_key_and_text_to_display",

        "column_key-4" => "text to display-4"

    );

    $duplicate_in_tfoot = true | false;

    $table->columns( $__columns, $duplicate_in_tfoot );

*/

$table->columns(array(

    "checkbox" => "<input type='checkbox'>",
    "balance",
    "email",
    "firstname" => "First Name",
    "food" => "The Food",
    "username" => 'Username',
    'id' => 'User ID'

), true); # true = add column to <tfoot/> (:default = false);



/*

    $table->data( $data_to_display_on_tbody );

    The data to display on <tbody/> can either be:

    - 1: Array;

    - 2: MYSQLI_RESULT;

*/



# ---- [ data as "MYSQLI_RESULT" Example ] ----

try {

    $mysqli = @new mysqli('localhost', 'root', '', 'db_name');

    $result = @$mysqli->query("SELECT * FROM `tablename`");

    $table->data($result);

} catch(Exception $e) {
};



# ---- [ data as "Array" Example ] ----

$Array = array(

    array( 'username' => 'ucscode', 'firstname' => 'Uchenna', 'id' => 1, "email" => "uche23mail@gmail.com", "food" => "Beans & Plantain", "balance" => 50000 ),

    array( 'username' => 'elsie', 'firstname' => 'Elizabeth', 'id' => 3, "email" => "elsie@gmail.com", "food" => "Ice Cream", "balance" => 21500 ),

    array( 'username' => 'jonny', 'firstname' => 'John', 'id' => 6, "email" => "jonny@hotmail.com", "food" => "bread", "balance" => 540 ),

    array( 'username' => 'media', 'firstname' => 'Socialist', 'id' => 2, "email" => "media@protonmail.com", "food" => "followers", "balance" => 4950 ),

    array( 'username' => 'james003', 'firstname' => 'Gabriel', 'id' => 9, "email" => "james@webmail.com", "food" => "unknown", "balance" => 5 ),

    array( 'username' => 'davie504', 'firstname' => 'David', 'id' => 4, "email" => "davie504@gmail.com", "food" => "Spagetti", "balance" => 0.00 ),

);

$table->data($Array);


/*

    The current page of the table should be specified

        -- DOMTable::$page = int; (default = 1);


    The rows_per_page of the table must also be specified

        -- DOMTable::$rows_per_page = int; (default = 10);

*/

$table->chunk(3); // Number of rows per page

$table->page($_GET['paged'] ?? 1); // The current page


/*

    Prepare the table for display;

    $param1 = function(){} | null;

    $table->prepare( $param1, (true | false) );

*/

$table->prepare(function ($data) {

    // default columns can be modified in the data;
    // custom columns can also be added to the data;

    $data['checkbox'] = "<input type='checkbox' name='username' value='{$data['username']}'>";

    if($data['username'] == 'jonny') {
        $data['username'] = 'Jonny Upgraded';
    }

    $data['balance'] = number_format($data['balance'], 2);

    return $data;

}, true); # true = print table in page;     false = return table as string (default);




/*
    [
        - You can also do some custom thing with the internal DOMDocument before printing the table ( through DOMTable::prepare() );
        - OR: prevent table from printing, then print it manually;
    ];
*/

$table->container->setAttribute('class', $table->container->getAttribute('class') . " my-table-class");

echo "<h1>Custom Version</h1>" . $table->doc->saveHTML($table->container);


?>

		<div class='pager'>
			<a href='?paged=<?php echo $the_page = ($table->page == 2) ? 1 : 2; ?>'>
				Move to page <?php echo $the_page; ?>
			</a>
		</div>
		
	</body>
	
</html>



<?php


    ## ------ [ SHORT SUMMARY OF HOW TO USE DOMTable ] ------

    // create table instance;

    $table = new DOMTable('tablename');


// set table column;

$table->columns(array(
    "id",
    "name" => "Full Name"
));


// set table data;

$table->data(array(
    array( "id" => 1, "name" => "My name" ),
    array( "id" => 1, "name" => "Your name" ),
));


// set table limit;

$table->chunk(5); // set max table rows per page;

$table->page(1); // define the current page;


// prepare the table;

$tableHTMLString = $table->prepare();


# echo $tableHTMLString;


?>