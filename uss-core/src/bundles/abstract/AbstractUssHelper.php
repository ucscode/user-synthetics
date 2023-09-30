<?php

use Ucscode\SQuery\SQuery;

abstract class AbstractUssHelper
{
    public const SANITIZE_ENTITIES = 1;
    public const SANITIZE_SQL = 2;

    /**
     * Take a value and sanitize it
     *
     * if the value is iterable, the leaf values will be sanitized
     */
    public function sanitize(mixed $data, int $alpha = self::SANITIZE_ENTITIES|self::SANITIZE_SQL): mixed {
        if(is_iterable($data)) {
            foreach($data as $key => $value) {
                $key = $this->sanitize($key, $alpha);
                $value = $this->sanitize($value, $alpha);
                if(is_object($data)) {
                    $data->{$key} = $value;
                } else {
                    $data[$key] = $value;
                };
            }
        } else {
            if(!is_bool($data) && !is_null($data)) {
                $data = trim($data);
                if($alpha & self::SANITIZE_ENTITIES) {
                    $data = htmlentities($data, ENT_QUOTES|ENT_SUBSTITUTE);
                };
                if($alpha & self::SANITIZE_SQL) {
                    if(isset($this->mysqli) && $this->mysqli instanceof \mysqli) {
                        $data = $this->mysqli->real_escape_string($data);
                    };
                };
            }
        };
        return $data;
    }

    /**
     * Generate URL from absolute filesystem path.
     *
     * @param string $pathname The pathname to be converted in the URL.
     * @param bool $hidebase Whether to hide the URL base or not. Default is `false`.
     * @return string The generated URL
     */
    public function getUrl(string $pathname, bool $hidebase = false): string
    {
        $pathname = $this->slash($pathname); // Necessary in windows OS 
        $port = $_SERVER['SERVER_PORT'];
        $scheme = ($_SERVER['REQUEST_SCHEME'] ?? ($port != '80' ? 'https' : 'http'));

        // Set port number visibility: To avoid broken links when port forwarding is used
        $visiblePort = !in_array($port, ['80', '443']) ? ":{$port}" : null;

        // Create the URL: Base will not be hidden if port number is not invisible
        $relativePath = preg_replace("~^{$_SERVER['DOCUMENT_ROOT']}~i", '', $pathname);

        if(!$hidebase || $visiblePort) {
            $url = $scheme . "://" . $_SERVER['SERVER_NAME'] . "{$visiblePort}" . $relativePath;
        } else {
            $url = $relativePath;
        }

        return $url;
    }

    /**
     * Convert array to HTML Attributes
     *
     * @param array $array The array containing the key-value pairs to be converted.
     * @param bool $singleQuote Whether to use single quotes for attribute values. Default is `false`.
     * @return string The HTML attribute string
     */
    public function arrayToHtmlAttrs(array $array, bool $singleQuote = false): string
    {
        return implode(" ", array_map(function ($key, $value) use ($singleQuote) {
            if(is_array($value)) {
                $value = json_encode($value);
            }
            $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $quote = $singleQuote ? "'" : '"';
            return "{$key}={$quote}{$value}{$quote}";
        }, array_keys($array), array_values($array)));
    }


    /**
     * Generate a Random Key
     *
     * @param int $length The length of the key to be generated. Default is 10.
     * @param bool $use_spec_char Whether to include special characters in the key. Default is `false`.
     * @return string The generated random key.
     */
    public function keygen($length = 10, bool $use_special_char = false): string
    {
        $data = range(0, 9);
        foreach([range('a', 'z'), range('A', 'Z')] as $array) {
            foreach($array as $value) {
                $data[] = $value;
            };
        };
        if($use_special_char) {
            $special = ['!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '[', ']', '{', '}', '/', ':', '.', ';', '|', '>', '~', '_', '-'];
            foreach($special as $char) {
                $data[] = $char;
            }
        };
        $key = '';
        for($x = 0; $x < $length; $x++) {
            shuffle($data);
            $key .= $data[0];
        };
        return $key;
    }


    /**
     * Replace %{variables} in a String
     *
     * @param string $string The string containing variables to replace.
     * @param array $data An associative array with variable-value pairs.
     * @return string The modified string with variables replaced by their values.
     */
    public function replaceVar(string $string, array $data): string
    {
        $chars = 'a-z0-9_\-\.\$\(\)\[\]:;@#';
        $new_string = preg_replace_callback("~%(?:\\\\)*\{([$chars]+)\}~i", function ($match) use ($data) {
            /**
             * Return escaped tags
             */
            if(substr($match[0], 0, 2) != '%{') {
                return ('%' . substr($match[0], 2));
            }
            /**
             * Initiate replacement
             */
            $key = $match[1];
            return $data[ $key ] ?? null;
        }, $string);
        return $new_string;
    }

    /**
     * Get Regular Expression for a Specific Pattern
     *
     * @param string $name The name of the pattern for which to retrieve the regular expression.
     * @param bool $strict (Optional) Determines if the regular expression should be strict, i.e., match the entire string. Default is `false`.
     * @return string|null The regular expression pattern for the specified pattern name, or `null` if the pattern name is not recognized.
     */
    public function regex(string $name, $strict = false): string
    {
        if($strict) {
            $BEGIN = '^';
            $END = '$';
        } else {
            $BEGIN = $END = null;
        }
        
        return match(strtoupper($name)) {

            'EMAIL' => '/' . $BEGIN . '(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))' . $END . '/',

            "URL" => "/{$BEGIN}(?:https?:\/\/)?(?:[\w.-]+(?:(?:\.[\w\.-]+)+)|(?:localhost(:\d{1,4})?\/))[\w\-\._~:\/?#[\]@!\$&'\(\)\*\+,;=.%]+{$END}/i",

            "NUMBER" => "/{$BEGIN}\-?\d+(?:\.\d+)?{$END}/",

            "DATE" => "/{$BEGIN}(0[1-9]|[1-2][0-9]|3[0-1])(?:\-|\/)(0[1-9]|1[0-2])(?:\-|\/)[0-9]{4}{$END}/i",

            "BTC" => "/{$BEGIN}[13][a-km-zA-HJ-NP-Z0-9]{26,33}{$END}/i",

            default => $name

        };

    }

    /**
     * Check if Namespace Exists
     *
     * @param string $namespace The namespace to check for existence.
     * @return bool `true` if the namespace exists, `false` otherwise.
     */
    public function namespaceExists($namespace): bool
    {
        // credit to stackoverflow
        $namespace .= '\\';
        foreach(get_declared_classes() as $classname) {
            if(strpos($classname, $namespace) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Calculate Elapsed Time
     *
     * Calculates the elapsed time between a given DateTime and the current time.
     * Example: "3 months, 1 week ago"
     *
     * @param DateTime|int|string $DateTime The DateTime object, timestamp string, or Unix timestamp
     * @param bool $full Determines the level of detail in the elapsed time string. 
     * @return string The elapsed time in a human-readable format.
     */
    public function elapse($DateTime, bool $full = false): string
    {
        $Now = new DateTime("now");

        if($DateTime instanceof DateTime) {
            // Object;
            $Time = $DateTime;
        } elseif(!is_numeric($DateTime)) {
            // Timestamp String
            $Time = new DateTime($DateTime);
        } else {
            // Unix Timestamp;
            $Time = (new DateTime("now"))->setTimestamp($DateTime);
        }

        $diff = $Now->diff($Time);
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second'
        );

        foreach($string as $k => &$v) {
            if($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        };

        if(!$full) {
            $string = array_slice($string, 0, 1);
            if($full === false && $string) {
                $string = array_values($string);
                preg_match("/\d+\s\w/i", $string[0], $match);
                return str_replace(" ", '', $match[0]);
            }
        }

        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    /**
     * Get the availabe columns of a table
     *
     * This method scans a table in the database schema and return all available columns associated with the table
     *
     * @param string $tableName: The name of the table to retrive the columns
     * @return array: A list of all the columns
     */
    public function getTableColumns(string $tableName): array
    {
        $columns = [];

        $SQL = (new SQuery())
            ->select('column_name')
            ->from('information_schema.columns')
            ->where('table_schema', DB_NAME)
            ->and('table_name', $tableName);

        $result = Uss::instance()->mysqli->query($SQL);
        
        if($result->num_rows) {
            while($column = $result->fetch_assoc()) {
                $value = $column['column_name'];
                $columns[$value] = $value;
            }
        };

        return $columns;
    }

    public function mysqli_result_to_array(\mysqli_result $result): array
    {
        $data = [];
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        };
        return $data;
    }

    /**
     * Check if User-Agent is a Robot
     *
     * This method checks if the User-Agent string provided by the client is associated with a robot or crawler.
     * > Please note that User-Agent information can be easily spoofed, so the result may not always be accurate.
     *
     * @return bool Returns true if the User-Agent is likely a robot, false otherwise.
     */
    public function userAgentIsRobot(): bool
    {
        if(isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT'])) {
            $bot_regex = '/abacho|accona|AddThis|AdsBot|ahoy|AhrefsBot|AISearchBot|alexa|altavista|anthill|appie|applebot|arale|araneo|AraybOt|ariadne|arks|aspseek|ATN_Worldwide|Atomz|baiduspider|baidu|bbot|bingbot|bing|Bjaaland|BlackWidow|BotLink|bot|boxseabot|bspider|calif|CCBot|ChinaClaw|christcrawler|CMC\/0\.01|combine|confuzzledbot|contaxe|CoolBot|cosmos|crawler|crawlpaper|crawl|curl|cusco|cyberspyder|cydralspider|dataprovider|digger|DIIbot|DotBot|downloadexpress|DragonBot|DuckDuckBot|dwcp|EasouSpider|ebiness|ecollector|elfinbot|esculapio|ESI|esther|eStyle|Ezooms|facebookexternalhit|facebook|facebot|fastcrawler|FatBot|FDSE|FELIX IDE|fetch|fido|find|Firefly|fouineur|Freecrawl|froogle|gammaSpider|gazz|gcreep|geona|Getterrobo-Plus|get|girafabot|golem|googlebot|\-google|grabber|GrabNet|griffon|Gromit|gulliver|gulper|hambot|havIndex|hotwired|htdig|HTTrack|ia_archiver|iajabot|IDBot|Informant|InfoSeek|InfoSpiders|INGRID\/0\.1|inktomi|inspectorwww|Internet Cruiser Robot|irobot|Iron33|JBot|jcrawler|Jeeves|jobo|KDD\-Explorer|KIT\-Fireball|ko_yappo_robot|label\-grabber|larbin|legs|libwww-perl|linkedin|Linkidator|linkwalker|Lockon|logo_gif_crawler|Lycos|m2e|majesticsEO|marvin|mattie|mediafox|mediapartners|MerzScope|MindCrawler|MJ12bot|mod_pagespeed|moget|Motor|msnbot|muncher|muninn|MuscatFerret|MwdSearch|NationalDirectory|naverbot|NEC\-MeshExplorer|NetcraftSurveyAgent|NetScoop|NetSeer|newscan\-online|nil|none|Nutch|ObjectsSearch|Occam|openstat.ru\/Bot|packrat|pageboy|ParaSite|patric|pegasus|perlcrawler|phpdig|piltdownman|Pimptrain|pingdom|pinterest|pjspider|PlumtreeWebAccessor|PortalBSpider|psbot|rambler|Raven|RHCS|RixBot|roadrunner|Robbie|robi|RoboCrawl|robofox|Scooter|Scrubby|Search\-AU|searchprocess|search|SemrushBot|Senrigan|seznambot|Shagseeker|sharp\-info\-agent|sift|SimBot|Site Valet|SiteSucker|skymob|SLCrawler\/2\.0|slurp|snooper|solbot|speedy|spider_monkey|SpiderBot\/1\.0|spiderline|spider|suke|tach_bw|TechBOT|TechnoratiSnoop|templeton|teoma|titin|topiclink|twitterbot|twitter|UdmSearch|Ukonline|UnwindFetchor|URL_Spider_SQL|urlck|urlresolver|Valkyrie libwww\-perl|verticrawl|Victoria|void\-bot|Voyager|VWbot_K|wapspider|WebBandit\/1\.0|webcatcher|WebCopier|WebFindBot|WebLeacher|WebMechanic|WebMoose|webquest|webreaper|webspider|webs|WebWalker|WebZip|wget|whowhere|winona|wlm|WOLP|woriobot|WWWC|XGET|xing|yahoo|YandexBot|YandexMobileBot|yandex|yeti|Zeus/i';
            return !!preg_match($bot_regex, $_SERVER['HTTP_USER_AGENT']);
        } else {
            return true;
        }
    }

    /**
     * Convert HTML Named Entities to XML Entities
     *
     * This method converts HTML named entities to XML entities.
     *
     * <!-- Note: The conversion relies on the "xhtml-entities.json" file, which contains the mapping of HTML named entities
     * to their corresponding XML entities. Make sure the "xhtml-entities.json" file is present in the same directory as
     * the "core.php" file for accurate conversion. -->
     *
     * @param string $string The input string to convert.
     * @return string|false The converted string with HTML named entities replaced by XML entities, or `false` if the
     * conversion fails.
     */
    public function xmlEntities(string $string = '')
    {
        /**
         * The xhtml-entities.json file was modified after being retreived from W3C github library on Github
         * View link - https://github.com/w3c/html/blob/master/entities.json
         */
        $file = UssEnum::JSON_DIR . '/xhtml-entities.json';
        if(!is_file($file)) {
            return false;
        }

        # Convert to JSON and validate the conversion;
        $content = json_decode(file_get_contents($file), true);
        if(json_last_error()) {
            return false;
        }

        $string = preg_replace("/&(?!(?:\w+|\#\d+);)/i", '&amp;', $string);
        
        // Combine the name and the Entities;
        $entities = array_map(function ($value) {
            return "&#{$value};";
        }, array_column($content, 'entity'));

        $XMLEntities = array_combine(array_keys($content), $entities);
        // Replace HTML named entites in the string;
        return strtr($string, $XMLEntities);
    }

    /**
     * Get the absolute path based on the provided path.
     *
     * This method resolves the absolute path based on the provided path by considering the file that called this method.
     * It handles relative paths, parent directory references (`..`), and system-specific directory separators.
     *
     * The behavior of this method is similar to that of PHP `realpath()` function, except that it does not return `false` if the path does not exist.
     *
     * @param string $path The path to resolve.
     * @return string|false The absolute path if successful, or `false` if unable to retrieve the absolute path.
     */
    public function absPath(string $path)
    {
        $debug = debug_backtrace();
        $key = array_search(__FUNCTION__, array_column($debug, 'function'));

        if($debug[$key]['class'] != __CLASS__) {
            return false;
        }
        
        // The absolute directory of the file that called this method!
        $absolutes = explode(DIRECTORY_SEPARATOR, dirname($debug[$key]['file']));

        // Use default system directory separator
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, trim($path));

        // Split path by separator and get different parts of the string
        $parts = array_values(array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen'));

        // Retrieve absolute path if "$path" starts with "/";
        if(substr($path, 0, 1) === DIRECTORY_SEPARATOR) {
            $absolutes = [$absolutes[0]];
        }

        // Check if "$path" is already absolute;
        if($absolutes[0] === ($parts[0] ?? null)) {
            // This empty array will be refilled with the absolute data from "$parts" variable
            $absolutes = [];
        };

        // Create an absolute path;
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

        $absolutes = array_values($absolutes);

        if(!empty($absolutes[0]) && empty($absolutes[1])) {
            $absolutes[] = '';
        }

        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }

    /**
     * Check if the given path is an absolute path.
     *
     * @param string $path The path to check.
     * @return bool `true` if the path is an absolute path, `false` otherwise.
     */
    public function isAbsolutePath(string $path): bool
    {
        // Check for wrapper path (e.g. file://)
        if (preg_match('#^[a-z][a-z\d+.-]*://#i', $path)) {
            return true;
        }

        // Check for Windows absolute path (e.g. C:\)
        if (preg_match('#^[a-z]:(?:\\\\|/)#i', $path)) {
            return true;
        }

        // Check for Unix/Linux absolute path (e.g. /)
        if (preg_match('#^/|~/#', $path)) {
            return true;
        }

        return false;
    }    
    
    /**
     * Replaces backslashes with forward slashes in a given string.
     */
    protected function slash(?string $PATH)
    {
        return str_replace("\\", "/", $PATH);
    }

}
