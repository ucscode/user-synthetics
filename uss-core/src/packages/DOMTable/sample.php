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

require_once '../UssElement/UssElementInterface.php';
require_once '../UssElement/AbstractUssElementNodeList.php';
require_once '../UssElement/AbstractUssElementParser.php';
require_once '../UssElement/UssElement.php';

require_once 'DOMTableInterface.php';
require_once 'AbstractDOMTable.php';
require_once 'DOMTable.php';

use Ucscode\DOMTable\DOMTableInterface;
use Ucscode\DOMTable\DOMTable;

$table = new DOMTable('tablename');

$table->setMultipleColumns([
    "checkbox" => "<input type='checkbox'>",
    "balance",
    "email",
    "firstname" => "First Name",
    "food" => "The Food",
    "username" => 'Username',
    'id' => 'User ID'
]);

$table->setDisplayFooter(true);

try {

    $mysqli = @new mysqli('localhost', 'root', '', 'db_name');

    $result = @$mysqli->query("SELECT * FROM `tablename`");

    $table->setData($result);

} catch(Exception $e) {

    // Your exception

};

# ---- [ data as "Array" Example ] ----

$Array = [

    array(
        'username' => 'ucscode',
        'firstname' => 'Uchenna',
        'id' => 1,
        "email" => "uche23mail@gmail.com",
        "food" => "Beans & Plantain",
        "balance" => 50000
    ),

    array(
        'username' => 'elsie',
        'firstname' => 'Elizabeth',
        'id' => 3,
        "email" => "elsie@gmail.com",
        "food" => "Ice Cream",
        "balance" => 21500
    ),

    array(
        'username' => 'jonny',
        'firstname' => 'John',
        'id' => 6,
        "email" => "jonny@hotmail.com",
        "food" => "bread",
        "balance" => 540
    ),

    array(
        'username' => 'media',
        'firstname' => 'Socialist',
        'id' => 2,
        "email" => "media@protonmail.com",
        "food" => "followers",
        "balance" => 4950
    )

];

$table->setData($Array);

//

$table->setRowsPerPage(3);
$table->setCurrentPage($_GET['page'] ?: 1); // The current page

$result = $table->build(new class () implements DOMTableInterface {
    protected string $checkbox = "<input type='checkbox' name='username' value='%s'>";

    public function forEachItem(array $data): array
    {
        $data['checkbox'] = sprintf($this->checkbox, $data['username']);

        if($data['username'] === 'jonny') {
            $data['username'] = 'Jonny Upgraded';
        }

        $data['balance'] = number_format($data['balance'], 2);

        return $data;
    }
});

$table->getTableContainerElement()->addAttributeValue('class', 'my-class');

echo "<h1>Custom Version</h1>";
echo $result;

$page = $table->getNextPage() ?? $table->getPrevPage();

?>

		<div class='pager'>
			<a href='?page=<?php echo $page ?>'>
				Move to page <?php echo $page; ?>
			</a>
		</div>
		
	</body>
	
</html>



<?php

$table = new DOMTable('tablename');

$table->setMultipleColumns(array(
    "id",
    "name" => "Full Name"
));

$table->setData(array(
    array( "id" => 1, "name" => "My name" ),
    array( "id" => 1, "name" => "Your name" ),
));

$table->setRowsPerPage(5);
$table->setCurrentPage(1);
$tableHTML = $table->build();

//echo $tableHTML;

?>