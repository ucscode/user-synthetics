<?php

/**
 * DOMTable
 *
 * A PHP Library that uses DOMDocument & Nodes to create custom HTML table
 * This library only creates "Table" Element and has no stylesheet.
 * The CSS of the table element should be created by the programmer utilizing this library
 *
 * @author ucscode
 * @since Dec 20, 2022;
 * @link https://github.com/ucscode
 *
*/

/**
 * DOMTable - Lightweight PHP library for generating HTML tables.
 *
 * DOMTable is a stand-alone library that utilizes the Document Object Model (DOM) to simplify the creation of HTML tables
 * in PHP projects. It provides an easy-to-use interface for defining table columns, populating
 * the table with data from MySQL results or arrays, and customizing the output.
 *
 * > This library only creates the HTML structure of the table element and does not
 * include any pre-defined styles or CSS. Technically, the created table will inherit the style of the current page.
 * The programmer utilizing this library is responsible
 * for creating their own CSS styles to customize the appearance of the table.
 *
 * ### Example:
 *
 * ```php
 * //Instantiate DOMTable with a table name
 * $domtable = new DOMTable("users");
 *
 * //Set the columns to display on the table
 * $domtable->columns(array(
 *     "id",
 *     "username",
 *     "email"
 * ));
 *
 * // Set the number of rows to display per page
 * $domtable->chunk(10);
 *
 * // Fetch data from MySQL database
 * $mysql_result = $db->query("SELECT * FROM users");
 *
 * // Populate the table with data
 * $domtable->data($mysql_result);
 *
 * // Generate the HTML code for the table
 * $table_html = $domtable->prepare();
 *
 * // Print or use the HTML table as needed
 * echo $table_html;
 * ```
 *
 * ---
 *
 * @package DOMTable
 * @version 1.1.4
 * @author ucscode
 * @license MIT License (https://opensource.org/licenses/MIT)
 * @link https://github.com/ucscode/domtable
 */
class DOMTable
{
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $tablename;

    /**
     * The DOM document object.
     *
     * @var DOMDocument
     */
    protected $doc;

    /**
     * The container element of the table.
     *
     * @var DOMElement
     */
    protected $container;

    /**
     * The table element.
     *
     * @var DOMElement
     */
    protected $table;

    /**
     * The number of rows to display per page.
     *
     * @var int
     */
    protected $chunks = 10;

    /**
     * The current page number.
     *
     * @var int
     */
    protected $page = 1;

    /**
     * The columns to display in the table.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * The data to populate the table.
     *
     * @var mixed
     */
    protected $data = null;

    /**
     * The rows of the table.
     *
     * @var DOMElement[]
     */
    protected $rows;

    /**
     * The total number of pages.
     *
     * @var int
     */
    protected $pages;

    /**
     * The text to display on empty table set.
     *
     * @var int
     */
    protected $emptystateText = 'No results found';

    /**
     * DOMTable Constructor
     *
     * Initializes a new instance of the DOMTable class.
     *
     * If a table name is provided, it is used as the element ID for the generated table.
     * If no table name is provided, a unique identifier is generated and used as the element ID.
     * The constructor sets up the DOMDocument object, configures the table structure,
     * and prepares the table for further manipulation and rendering.
     *
     * @param string|null $tablename The name of the table element (optional).
     *
     * @throws DOMException If an error occurs during DOMDocument initialization.
     */
    public function __construct(?string $tablename = null)
    {

        // table name as element id;
        $this->tablename = empty($tablename) ? ('_' . uniqid()) : $tablename;

        //libxml_use_internal_errors(true);

        // Create a PHP DomDocument;
        $this->doc = new DOMDocument('1.0', 'utf-8');

        $this->doc->preserveWhiteSpace = false;
        $this->doc->formatOutput = true;

        // Create a Table Element;
        $HTML_TABLE = "
			<div class='dt-container'>
				<!-- a good spot add features like search box, checkbox options etc -->
				<div class='table-responsive'>
					<table class='table' id='dt-{$this->tablename}'>
						<thead/>
						<tbody/>
						<tfoot/>
					</table>
				</div>
				<!-- a good spot to add features like nav button, footer label etc -->
			</div>
		";

        // Load The Table;
        $this->doc->loadHTML($HTML_TABLE);
        $this->container = $this->doc->getElementsByTagName('div')->item(0);
        $this->table = $this->container->getElementsByTagName('table')->item(0);

    }

    /**
     * Magic getter method.
     *
     * Retrieves the value of a specific protected property when accessed as an object property.
     *
     * @param string $name The name of the property to retrieve.
     *
     * @return mixed|null The value of the property if it exists, or null if the property does not exist.
     */
    public function __get($name)
    {
        if(property_exists($this, $name)) {
            return $this->{$name};
        }
    }

    /**
     * Set the columns to be displayed in the table.
     *
     * Defines the columns to be rendered in the table. Accepts an array of column names or keys.
     * If the array keys are numeric, they will represent both the column key and the display title.
     * If the array keys are non-numeric, they will represent the column keys, and the array values will be the display titles.
     *
     * @param array $columns An array of column names or keys.
     * @param bool  $tfoot   Optional. Indicates if the columns should also be made available for the table footer (tfoot). Default is `false`.
     *
     * @return void
     */
    public function columns(array $columns, bool $tfoot = false)
    {
        $this->columns[0] = [];
        foreach($columns as $key => $value) {
            if(is_numeric($key)) {
                $key = $value;
            }
            $this->columns[0][$key] = $value;
        }
        $this->columns[1] = $tfoot;
    }

    /**
     * Set the data to populate the table.
     *
     * Sets the data to populate the table with. Accepts either an `array` or an instance of `mysqli_result`.
     * If an array is provided, each element represents a row of data. If an instance of `mysqli_result` is provided,
     * the data will be fetched from the result set.
     *
     * @param array|mysqli_result $data The data to populate the table with.
     *
     * @throws Exception If the argument is not an array or an instance of mysqli_result.
     *
     * @return void
     */
    public function data($data)
    {
        if(!($data instanceof mysqli_result) && !is_array($data)) {
            throw new Exception(__CLASS__ . "::" . __FUNCTION__ . "() argument must be an Array or an instance of Mysqli_Result");
        }
        $this->data = $data;
        $this->calculate();
    }

    /**
     * Set the number of rows to display per page (chunk).
     *
     * This method allows you to specify the number of rows that should be displayed per page in the table. The chunk represents a pagination feature where the data is divided into smaller chunks for easier navigation and readability.
     *
     * @param int $rows The number of rows to display per page.
     *
     * @return void
     */
    public function chunk(int $rows)
    {
        $rows = abs($rows);
        if(!$rows) {
            return;
        }
        $this->chunks = $rows;
        $this->calculate();
    }

    /**
     * Set the current page for the table.
     *
     * This method allows you to specify the current page for the table. It is used in conjunction with the chunk method to navigate between different pages of the table.
     *
     * @param int $page The current page number.
     *
     * @return void
     */
    public function page(int $page)
    {
        $page = abs($page);
        if(!$page) {
            return;
        }
        $this->page = $page;
    }

    /**
     * Calculate the number of rows and pages for the table.
     *
     * This method calculates the total number of rows and pages for the table based on the provided data and chunk size. It is automatically called when setting the data or chunk size, and does not need to be called explicitly.
     *
     * @return void
     * @ignore
     */
    protected function calculate()
    {
        if(empty($this->data) || empty($this->chunks)) {
            return;
        }
        $this->rows = is_array($this->data) ? count($this->data) : $this->data->num_rows;
        $this->pages = ceil($this->rows / $this->chunks);
    }

    /**
     * Initialize the data for the current page.
     *
     * This method initializes the data for the current page based on the specified chunk size and page number. It retrieves the relevant data from the data source (array or MySQLi result set) and applies any provided modification function to each data item.
     *
     * @param callable $func The modification function to apply to each data item. This function should accept a single parameter (the data item) and return the modified data item.
     * @return array The initialized data for the current page.
     * @throws Exception If the page or chunks are not valid integers.
     * @ignore
     */
    protected function init_data($func)
    {

        if(!is_numeric($this->page)) {
            throw new exception(__CLASS__ . "::\$page is not a valid interger [number]");
        } elseif(!is_numeric($this->chunks)) {
            throw new exception(__CLASS__ . "::\$chunks is not a valid interger [number]");
        }

        $begin = ($this->page - 1) * $this->chunks;

        // process for data as Array;

        if(is_array($this->data)) {
            $result = array_slice($this->data, $begin, $this->chunks);
            foreach($result as $key => $data) {
                $result[$key] = $this->modify_data($data, $func);
            }
        } else {
            $result = array();
            $this->data->data_seek($begin);
            while($data = $this->data->fetch_assoc()) {
                if(count($result) == $this->chunks) {
                    break;
                }
                $result[] = $this->modify_data($data, $func);
            };
        };

        return $result;

    }

    /**
     * Modify the data item by filling in missing columns and applying the modification function.
     *
     * This method is used to modify a data item by filling in any missing columns with null values and applying the provided modification function if available.
     *
     * @param array $data The data item to modify.
     * @param callable|null $func The modification function to apply to the data item. This function should accept a single parameter (the data item) and return the modified data item.
     * @return array The modified data item.
     * @ignore
     */
    protected function modify_data($data, $func)
    {
        $missing_columns = array_diff(array_keys($this->columns[0]), array_keys($data));
        if(!empty($missing_columns)) {
            foreach($missing_columns as $key) {
                $data[$key] = null;
            }
        };
        $new_data = !$func ? $data : ($func($data) ?? $data);
        if(!is_array($new_data)) {
            $new_data = $data;
        }
        return $new_data;
    }

    /**
     * Extend the table body with rows of data.
     *
     * This method takes an array of data items and extends the table body by creating rows for each data item.
     * It ensures that all columns in the data items are accounted for and creates the corresponding table cells in each row.
     *
     * @param array $result The array of data items to populate the table body with.
     * @return DOMElement The table body element.
     * @ignore
     */
    protected function extend_tbody(array $result)
    {
        foreach($result as $data) {
            $undefined_key = array_diff(array_keys($data), array_keys($this->columns[0]));
            if(!empty($undefined_key)) {
                foreach($undefined_key as $key) {
                    unset($data[$key]);
                }
            };
            $data = array_merge($this->columns[0], $data);
            $this->create_row(array_values($data));
        };
        return $this->table->getElementsByTagName('tbody')->item(0);
    }

    /**
     * Display a "No results found" message.
     *
     * This method creates and appends a DIV element with a "No results found" message to indicate that there are no results to display in the table.
     * @ignore
     */
    protected function complain()
    {
        // Create DIV;
        $div = $this->doc->createElement('div');
        $div->setAttribute('class', 'dt-empty');
        # $div->setAttribute('align', 'center');
        // Create SPAN & appendTo DIV;
        $span = $this->doc->createElement('span');
        $div->appendChild($span);
        // Create TEXT & appendTo SPAN;
        $textNode = $this->doc->createTextNode($this->emptystateText);
        $span->appendChild($textNode);
        $this->table->parentNode->parentNode->appendChild($div);
    }

    /**
     * Create and append a table row (`tr`) element with the specified columns.
     *
     * This method creates a table row element (`tr`) and appends it to the specified parent element (`tbody` by default).
     * The columns are provided as an array, and each column value is added as a table cell (`td`) element within the row.
     *
     * @param array  $columns   The column values to be added as table cells.
     * @param string $type      The type of element to create for each column (default is 'td').
     * @param string $appendTo  The ID or tag name of the parent element to which the row should be appended (default is 'tbody').
     *
     * @return bool  `true` if the row was created and appended successfully, `false` otherwise.
     * @ignore
     */
    protected function create_row($columns, $type = 'td', $appendTo = 'tbody')
    {
        $approved = !empty(array_filter($columns, function ($value) {
            return !is_null($value);
        }));
        if($approved) {
            # create a <tr/> element;
            $tr = $this->doc->createElement('tr');
            foreach($columns as $value) {
                $tx = $this->doc->createElement($type);
                $this->innerHTML($tx, $value);
                $tr->appendChild($tx);
            };
            $this->table->getElementsByTagName($appendTo)->item(0)->appendChild($tr);
        };
        return $approved;
    }

    /**
     * Set the inner HTML content of an element.
     *
     * This method sets the inner HTML content of the specified element.
     * The provided HTML string is parsed and loaded into a temporary `DOMDocument`.
     * The inner HTML content is then imported and appended to the target element, replacing any existing content.
     *
     * This `innerHTML()` method provides an advantage by allowing you to change the inner HTML content of an element
     * that is not directly part of the DOMTable structure. It is a versatile method that can be used to manipulate
     * the inner HTML of any DOMElement within the DOMDocument, even if it is not specifically related to the DOMTable instance.
     *
     * @param DOMElement $el          The target element to set the inner HTML content for.
     * @param string     $innerHTML   The HTML string to set as the inner content of the element.
     *
     * @return DOMElement  The modified element with the updated inner HTML content.
     */
    public function innerHTML(&$el, ?string $innerHTML = null)
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = true;
        $innerHTML = preg_replace("/&(?!\S+;)/", "&amp;", $innerHTML);
        $dom->loadHTML("<div>{$innerHTML}</div>");
        $div = $this->doc->importNode($dom->getElementsByTagName('body')->item(0)->firstChild, true);
        while($el->firstChild) {
            $el->removeChild($el->firstChild);
        }
        while($div->firstChild) {
            $el->appendChild($div->firstChild);
        }
        return $el;
    }

    /**
     * Prepare and generate the HTML code for the table.
     *
     * This method prepares and generates the HTML code for the table based on the defined columns and data.
     * It allows for optional modification of the data through a callback function before displaying.
     *
     * The generated HTML code can be either printed directly to the output or returned as a string, depending on the value of the `$print` parameter.
     * If the `DOMTable::$columns` property is empty or no data has been supplied through the `DOMTable::data()` method, an exception will be thrown.
     * The method returns the generated HTML code or `null` if the `$print` parameter is `true`.
     *
     * @param callable|null $func   Optional. A function to modify the data before displaying. Default is `null`.
     * @param bool          $print  Optional. Determines whether to print the HTML code directly or return it as a string. Default is `false`.
     *
     * @throws Exception if the $columns property is empty or if no data has been supplied through the data() method.
     *
     * @return string|null The generated HTML code or null if `$print` is `true`.
     */
    public function prepare(?callable $func = null, bool $print = false)
    {

        if(empty($this->columns[0])) {
            throw new Exception(__CLASS__ . "::\$columns is required to process table");
        } elseif(is_null($this->data)) {
            throw new Exception("No data was supplied through " . __CLASS__ . "::data() method");
        };

        // create a new row for <thead/>
        $this->create_row(array_values($this->columns[0]), 'th', 'thead');

        // prepare the <tr/> data passed by user : incase of modifications
        $result = $this->init_data($func);

        // extend data in <tbody/>
        $tbody = $this->extend_tbody($result);

        if(empty($this->columns[1]) || !$tbody->hasChildNodes()) {
            $this->table->removeChild($this->table->getElementsByTagName('tfoot')->item(0));
        } else {
            // create a new row for <tfoot/>: if applicable;
            $this->create_row(array_values($this->columns[0]), 'th', 'tfoot');
        };

        // add data to <tbody/>
        if(!$tbody->hasChildNodes()) {
            $this->complain();
        }

        $table = $this->doc->saveHTML($this->container);
        return print_r($table, !$print);

    }


}
