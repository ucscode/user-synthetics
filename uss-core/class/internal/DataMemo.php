<?php
/**
 * A Lightweight File-Based Storage Solution for Object Properties
 *
 * The `DataMemo` class is a stand-alone, independent class that provides a lightweight, file-based storage solution
 * for persisting object properties in `JSON` format.
 * It is designed to emulate a data memory or mini-database, allowing you to easily store, retrieve, and transport data
 * between sessions or projects. Unlike PHP's default `stdClass`, DataMemo ensures that object properties are not lost when
 * the project ends by saving them to a file for future use.
 *
 * DataMemo is particularly useful in scenarios where you want to maintain data continuity without relying on external
 * services or overburdening your primary database. It can be leveraged for various purposes, such as tracking website
 * traffic, storing application configuration, caching frequently accessed data, or any other lightweight data storage
 * requirements.
 *
 * #### Usage Example:
 * ```php
 * $filePath = "/path/to/file.json";
 * $dataMemo = new DataMemo($filePath);
 * $dataMemo->author = 'ucscode';
 * $dataMemo->package = 'utils.datamemo';
 * $dataMemo->save();
 * ```
 *
 * The constructor of DataMemo accepts a string argument representing the file path where the data will be stored
 * or retrieved. If the file contains a valid JSON data, DataMemo will load that data into the object. However, if the
 * file is not empty but does not contain valid JSON, an exception will be thrown to indicate the issue.
 *
 * @package YourPackage
 * @version 2.1.1
 * @author ucscode
 * @license MIT License (https://opensource.org/licenses/MIT)
 * @link https://github.com/ucscode/datamemo
 */
class DataMemo
{
    /** @ignore */
    protected $caller;

    /** @ignore */
    protected $file;

    /** @ignore */
    protected $memo;

    /** @ignore */
    protected $__NULL = null;

    /** @ignore */
    protected $pretty;

    /** @ignore */
    protected $delete;

    /** @ignore */
    protected $error;

    /**
     * DataMemo constructor.
     *
     * Initializes a new instance of the DataMemo class.
     *
     * @param string $file The path to the file used for storing the data.
     *
     * @throws Exception When the file path is missing or invalid.
     * @throws Exception When the JSON data in the file is malformed or not convertible to an array.
     */
    public function __construct(string $file)
    {

        # Get where the file was instantiated

        $key = array_search(__FUNCTION__, array_column(debug_backtrace(), 'function'));

        $this->caller = (debug_backtrace())[$key]['file'];


        /*
            File name is required for datamemo to get or save data!
            Absence of file name will throw an exception;
        */

        if(empty($file)) {
            throw new Exception("Missing file path in argument 1");
        }


        # Save the file for future references

        $this->file = $this->slash($this->abspath($file));

        if(empty($this->file)) {
            throw new Exception("Invalid file path specified in argument 1. Try passing an absolute path instead");
        }


        # Configure datamemo

        if(!is_file($this->file)) {
            $this->memo = array();
        } else {

            $contents = trim(file_get_contents($this->file));

            if(empty($contents)) {
                $this->memo = array();
            } else {

                $contents = json_decode($contents, true);
                $reference = __class__ . "::" . __function__ . "('{$file}'); ";

                if(json_last_error()) {
                    throw new Exception($reference . " {JSON-ERROR}: " . json_last_error_msg());
                } else {
                    if(!is_array($contents)) {
                        throw new Exception($reference . "{JSON-WARNING}: Data in '{$file}' must be convertible to an Array");
                    } else {
                        $this->memo = $contents;
                    }
                }

            };

        };

    }

    /**
     * Resolves the absolute path for a given path relative to the location where the `DataMemo` object was instantiated.
     *
     * This method takes a path and resolves it to an absolute path based on the location where the `DataMemo` object was instantiated. It handles both relative and absolute paths and ensures the correct directory separator is used.
     *
     * @param string $path The path to resolve to an absolute path.
     * @return string|false The absolute path of the given path, or `false` if the path could not be resolved.
     * @ignore
     */
    protected function abspath(string $path)
    {

        $absolutes = explode(DIRECTORY_SEPARATOR, dirname($this->caller));

        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, trim($path));

        if(substr($path, 0, 1) === DIRECTORY_SEPARATOR) {
            $absolutes = [$absolutes[0]];
        }

        $parts = array_values(array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen'));

        if($absolutes[0] === $parts[0]) {
            $absolutes = [];
        }

        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            }
            if ('..' == $part) {
                array_pop($absolutes);
                if(empty($absolutes)) {
                    return false;
                }
            } else {
                $absolutes[] = $part;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $absolutes);

    }

    /**
     * Replace Backslashes with Forward Slashes
     *
     * Replaces backslashes with forward slashes in the given path.
     *
     * @param mixed $path The path to process.
     * @return mixed The processed path with backslashes replaced by forward slashes.
     * @ignore
     */
    protected function slash($path)
    {
        if(!$path) {
            return;
        }
        $path = str_replace("\\", "/", $path);
        if(substr($path, -1) == '/') {
            $path = substr($path, 0, -1);
        }
        return $path;
    }

    /**
     * Debug Information Method
     *
     * Returns an array representation of the DataMemo object for debugging purposes.
     *
     * @return array An array representation of the DataMemo object.
     * @ignore
     */
    public function __debuginfo()
    {
        return (array)$this->memo;
    }

    /**
     * Magic Getter Method
     *
     * Retrieves the value of a specific property from the DataMemo object.
     *
     * @param string $key The key of the property to retrieve.
     * @return mixed The value of the requested property.
     * @throws Exception When the requested property does not exist.
     * @ignore
     */
    public function &__get($key)
    {
        if(!array_key_exists($key, $this->memo)) {
            throw new exception("Undefined index $key");
        };
        return $this->memo[$key];
    }

    /**
     * Magic Setter Method
     *
     * Sets the value of a specific property in the DataMemo object.
     *
     * @param string $key The key of the property to set.
     * @param mixed $value The value to assign to the property.
     * @return void
     * @ignore
     */
    public function __set($key, $value)
    {
        $this->memo[$key] = & $value;
    }

    /**
     * Magic Isset Method
     *
     * Checks if a specific property is set in the DataMemo object.
     *
     * @param string $key The key of the property to check.
     * @return bool True if the property is set, false otherwise.
     * @ignore
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->memo);
    }

    /**
     * Magic Unset Method
     *
     * Unsets a specific property in the DataMemo object.
     *
     * @param string $key The key of the property to unset.
     * @return void
     * @ignore
     */
    public function __unset($key)
    {
        if(array_key_exists($key, $this->memo)) {
            unset($this->memo[$key]);
        }
    }

    /**
     * Delete DataMemo File
     *
     * Sets the value of the delete property, which determines whether the DataMemo file should be deleted when the class is being destructed.
     *
     * @param bool $default The default value for the delete property. Default is `true`.
     * @return void
     */
    public function delete(bool $default = true)
    {
        $this->delete = $default;
    }

    /**
     * Pretty Print the JSON Output
     *
     * Sets the value of the `pretty` property, which determines whether the JSON data should be formatted with indentation and line breaks for readability.
     *
     * @param bool $pretty The flag indicating whether to enable pretty printing. Default is `true`.
     * @return void
     */
    public function pretty_print(bool $pretty = true)
    {
        $this->pretty = ($pretty) ? JSON_PRETTY_PRINT : null;
    }

    /**
     * Clears all data stored in the DataMemo object.
     *
     * This method removes all properties stored in the DataMemo object,
     * resulting in an empty data set. This will reset the DataMemo Properties,
     * effectively clearing all previously stored data.
     *
     * @return void
     */
    public function clear()
    {
        $this->memo = array();
    }

    /**
     * Retrieves the file path for storing data in the DataMemo object.
     *
     * This method retrieves the file path specified in the DataMemo object
     * and ensures that the directory for the file exists. If the directory does not
     * exist, it will be created using the `mkdir()` function.
     *
     * @return string The file path for storing data in the DataMemo object.
     * @ignore
     */
    protected function getFile()
    {
        $dir = dirname($this->file);
        if(!is_dir($dir)) {
            mkdir($dir);
        }
        return $this->file;
    }

    /**
     * Saves the data stored in the DataMemo object to a file.
     *
     * This method encodes the data stored in the DataMemo object into JSON format.
     * The encoded data is then written to the file specified in the DataMemo constructor.
     * If the encoding process fails, an error message is stored in the `error` property.
     *
     * @return bool Returns `true` if the data is successfully saved to the file, or `false` if an error occurs.
     */
    public function save()
    {

        $data = json_encode($this->memo, $this->pretty);

        // if encoding fails;

        if(!$data) {

            return !($this->error = json_last_error_msg());
        } else {
            return !!file_put_contents($this->getFile(), $data, LOCK_EX);
        }

    }

    /**
     * Destructor method for the DataMemo object.
     *
     * This method is automatically called when the DataMemo object is destroyed or goes out of scope.
     * It performs cleanup tasks, such as deleting the associated file if the `delete` option is enabled
     * and the file exists.
     *
     * @return void
     */
    public function __destruct()
    {
        $file = $this->getFile();
        if($this->delete && file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Retrieve the data stored in the DataMemo object.
     *
     * This method returns the data stored in the DataMemo object as an array.
     *
     * @return array The data stored in the DataMemo object.
     */
    public function data()
    {
        return $this->memo;
    }

    /**
     * Check if a specific value exists in the DataMemo object.
     *
     * This method checks if a given value exists in the data stored in the DataMemo object.
     *
     * @param mixed $value The value to check for existence.
     * @return bool `true` if the value exists, `false` otherwise.
     */
    public function value_exists($value)
    {
        return in_array($value, $this->memo);
    }

    /**
     * Add a new value to the end of the DataMemo object.
     *
     * This method adds a value to the end of the data stored in the DataMemo object. The value is assigned
     * with an index that is one greater than the highest existing index in the data.
     *
     * @param mixed $value The value to add to the DataMemo object.
     * @return void
     */
    public function push($value)
    {
        $index = empty($this->memo) ? 0 : max(array_keys($this->memo));
        if(!is_numeric($index)) {
            $index = count($this->memo);
        } else {
            $index = (int)$index + 1;
        }
        $this->memo[ $index ] = $value;
    }

    /**
     * Get the last error message encountered during data manipulation.
     *
     * This method returns the last error message that was encountered during data manipulation operations
     * such as encoding JSON data or writing to the file. If no error occurred, the method will return null.
     *
     * @return string|null The last error message, or `null` if no error occurred.
     */
    public function error()
    {
        return $this->error;
    }

}
