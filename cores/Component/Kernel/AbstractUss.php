<?php

namespace Uss\Component\Kernel;

use Ucscode\Pairs\Pairs;
use Ucscode\SQuery\SQuery;
use Uss\Component\Database;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use Ucscode\SQuery\Condition;
use Uss\Component\Kernel\Prime as KernelPrime;

abstract class AbstractUss implements UssInterface
{
    public readonly FilesystemLoader $filesystemLoader;
    public readonly Environment $twigEnvironment;
    public readonly ?Pairs $options;
    public readonly ?\mysqli $mysqli;

    public array $localStorage; // Basic local storage
    public array $jsStorage; // Send data to JavaScript scope
    public array $templateStorage; // Send global variable to Twig template

    protected function __construct(bool $kernel = false)
    {
        $this->localStorage = [
            'icon' => $this->abspathToUrl(UssImmutable::ASSETS_DIR . '/images/origin.png'),
            'title' => UssImmutable::PROJECT_NAME,
            'headline' => "Modular PHP Framework for Customizable Platforms",
            'description' => "Empowering Web Developers with a Modular PHP Framework for Customizable and Extensible Web Platforms.",
        ];
        $this->jsStorage = [];
        $this->templateStorage = [];
        
        $this->filesystemLoader = new FilesystemLoader([UssImmutable::TEMPLATES_DIR]);
        $this->filesystemLoader->addPath(UssImmutable::TEMPLATES_DIR, self::NAMESPACE);
        $this->twigEnvironment = new Environment($this->filesystemLoader, [
            'debug' => UssImmutable::DEBUG
        ]);
        $this->twigEnvironment->addExtension(new DebugExtension());

        $kernelPrime = new KernelPrime($this);
        
        if($kernel) {
            $this->mysqli = $kernelPrime->getMysqliInstance();
            $this->options = $kernelPrime->getPairsInstance($this->mysqli);
            $kernelPrime->createSession('USSID', 'USSCLIENTID');
            $kernelPrime->loadHTMLResource();
        }
    }

    /**
     * @method fetchData
     */
    public function fetchItem(string $table, string|array $value, $column = 'id'): ?array
    {
        $state = is_array($value) ? $value : [$column => $value];
        $condition = new Condition();
        
        foreach($state as $key => $input) {
            $condition->add($key, $input);
        }
    
        $squery = (new SQuery())
            ->select()
            ->from($table)
            ->where($condition);

        $SQL = $squery->build();
        $result = $this->mysqli->query($SQL);

        return $result->fetch_assoc();
    }

    /**
     * @method sanitize
     */
    public function sanitize(mixed $data, bool $sqlEscape = false): mixed
    {
        if(is_iterable($data)) {
            foreach($data as $key => $value) {
                $key = htmlentities($key);
                $value = $this->sanitize($value, $sqlEscape);
                is_object($data) ? $data->{$key} = $value : $data[$key] = $value;
            }
        } else {
            $data = !$sqlEscape ? 
            htmlentities($data) : 
            ($this->mysqli ? $this->mysqli->real_escape_string($data) : addslashes($data));
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
    public function abspathToUrl(string $pathname, bool $hidebase = false): string
    {
        $pathname = $this->slash($pathname); // Necessary in windows OS
        $port = $_SERVER['SERVER_PORT'];
        $scheme = ($_SERVER['REQUEST_SCHEME'] ?? ($port != 80 ? 'https' : 'http'));
        $viewPort = !in_array($port, ['80', '443']) ? ":{$port}" : null;
        $requestUri = preg_replace("~^{$_SERVER['DOCUMENT_ROOT']}~i", '', $pathname);

        if(!$hidebase || $viewPort) {
            $url = $scheme . "://" . $_SERVER['SERVER_NAME'] . "{$viewPort}" . $requestUri;
        } else {
            $url = $requestUri;
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
    public function keygen(int $length = 10, bool $use_special_char = false): string
    {
        $data = [...range(0, 9), ...range('a', 'z'), ...range('A', 'Z')];
        if($use_special_char) {
            $special = ['!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '[', ']', '{', '}', '/', ':', '.', ';', '|', '>', '~', '_', '-'];
            $data = [...$data, ...$special];
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
        return preg_replace_callback("~%(?:\\\\)*\{([$chars]+)\}~i", function ($match) use ($data) {
            if(substr($match[0], 0, 2) != '%{') {
                return ('%' . substr($match[0], 2));
            }
            $key = $match[1];
            return $data[ $key ] ?? null;
        }, $string);
    }

    /**
     * Calculate Elapsed Time (Moments Ago)
     *
     * @param DateTime|int|string $DateTime The DateTime object, timestamp string, or Unix timestamp
     * @param bool $full Determines the level of detail in the elapsed time string.
     * @return string The elapsed time in a human-readable format.
     */
    public function relativeTime($time, bool $verbose = false): string
    {
        if(!($time instanceof \DateTime) && !($time instanceof \DateTimeImmutable)) {
            if(!is_numeric($time)) {
                $time = new \DateTime($time);
            } else {
                $time = (new \DateTime())->setTimestamp($time);
            }
        }

        $interval = (new \DateTime())->diff($time);

        $formats = [
            'y' => 'year',
            'm' => 'month',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second'
        ];

        $result = 'Just Now';

        foreach($formats as $key => $unit) {
            $value = $interval->{$key};
            if($value > 0) {
                if(!$verbose) {
                    $unit = substr($unit, 0, 1);
                } else {
                    $unit = ' ' . $unit;
                    if($value > 1) {
                        $unit .= "s";
                    }
                }
                $result = $interval->format("%{$key}{$unit} ago");
                break;
            }
        };

        return $result;
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

        $squery = (new SQuery())
            ->select('COLUMN_NAME')
            ->from('information_schema.COLUMNS')
            ->where(
                (new Condition())
                    ->add('TABLE_SCHEMA', Database::NAME)
                    ->and('TABLE_NAME', $tableName)
            )
            ->orderBy('ORDINAL_POSITION', null);
        
        $SQL = $squery->build();
        $result = Uss::instance()->mysqli->query($SQL);

        if($result->num_rows) {
            while($column = $result->fetch_assoc()) {
                $value = $column['column_name'] ?? $column['COLUMN_NAME'];
                $columns[$value] = $value;
            }
        };

        return $columns;
    }

    /**
     * Converts a mysqli_result object to an associative array.
     *
     * @param \mysqli_result $result The mysqli_result object to convert.
     * @param callable|null $mapper Optional. A callback function to apply to each row before adding it to the result. The callback should accept a value and a key as its arguments.
     * @return array The resulting associative array.
     */
    public function mysqliResultToArray(\mysqli_result $result, ?callable $mapper = null): array
    {
        $data = [];
        while($row = $result->fetch_assoc()) {
            if($mapper) {
                $array_keys = array_keys($row);
                $row = array_combine(
                    $array_keys,
                    array_map($mapper, $row, $array_keys)
                );
            }
            $data[] = $row;
        };
        return $data;
    }

    /**
     * Check if Agent is a Robot
     * @return bool Returns true if the User-Agent is likely a robot, false otherwise.
     */
    public function isRobot(string $agent): bool
    {
        if(!empty($agent)) {
            $robotRegex = '/abacho|accona|AddThis|AdsBot|ahoy|AhrefsBot|AISearchBot|alexa|altavista|anthill|appie|applebot|arale|araneo|AraybOt|ariadne|arks|aspseek|ATN_Worldwide|Atomz|baiduspider|baidu|bbot|bingbot|bing|Bjaaland|BlackWidow|BotLink|bot|boxseabot|bspider|calif|CCBot|ChinaClaw|christcrawler|CMC\/0\.01|combine|confuzzledbot|contaxe|CoolBot|cosmos|crawler|crawlpaper|crawl|curl|cusco|cyberspyder|cydralspider|dataprovider|digger|DIIbot|DotBot|downloadexpress|DragonBot|DuckDuckBot|dwcp|EasouSpider|ebiness|ecollector|elfinbot|esculapio|ESI|esther|eStyle|Ezooms|facebookexternalhit|facebook|facebot|fastcrawler|FatBot|FDSE|FELIX IDE|fetch|fido|find|Firefly|fouineur|Freecrawl|froogle|gammaSpider|gazz|gcreep|geona|Getterrobo-Plus|get|girafabot|golem|googlebot|\-google|grabber|GrabNet|griffon|Gromit|gulliver|gulper|hambot|havIndex|hotwired|htdig|HTTrack|ia_archiver|iajabot|IDBot|Informant|InfoSeek|InfoSpiders|INGRID\/0\.1|inktomi|inspectorwww|Internet Cruiser Robot|irobot|Iron33|JBot|jcrawler|Jeeves|jobo|KDD\-Explorer|KIT\-Fireball|ko_yappo_robot|label\-grabber|larbin|legs|libwww-perl|linkedin|Linkidator|linkwalker|Lockon|logo_gif_crawler|Lycos|m2e|majesticsEO|marvin|mattie|mediafox|mediapartners|MerzScope|MindCrawler|MJ12bot|mod_pagespeed|moget|Motor|msnbot|muncher|muninn|MuscatFerret|MwdSearch|NationalDirectory|naverbot|NEC\-MeshExplorer|NetcraftSurveyAgent|NetScoop|NetSeer|newscan\-online|nil|none|Nutch|ObjectsSearch|Occam|openstat.ru\/Bot|packrat|pageboy|ParaSite|patric|pegasus|perlcrawler|phpdig|piltdownman|Pimptrain|pingdom|pinterest|pjspider|PlumtreeWebAccessor|PortalBSpider|psbot|rambler|Raven|RHCS|RixBot|roadrunner|Robbie|robi|RoboCrawl|robofox|Scooter|Scrubby|Search\-AU|searchprocess|search|SemrushBot|Senrigan|seznambot|Shagseeker|sharp\-info\-agent|sift|SimBot|Site Valet|SiteSucker|skymob|SLCrawler\/2\.0|slurp|snooper|solbot|speedy|spider_monkey|SpiderBot\/1\.0|spiderline|spider|suke|tach_bw|TechBOT|TechnoratiSnoop|templeton|teoma|titin|topiclink|twitterbot|twitter|UdmSearch|Ukonline|UnwindFetchor|URL_Spider_SQL|urlck|urlresolver|Valkyrie libwww\-perl|verticrawl|Victoria|void\-bot|Voyager|VWbot_K|wapspider|WebBandit\/1\.0|webcatcher|WebCopier|WebFindBot|WebLeacher|WebMechanic|WebMoose|webquest|webreaper|webspider|webs|WebWalker|WebZip|wget|whowhere|winona|wlm|WOLP|woriobot|WWWC|XGET|xing|yahoo|YandexBot|YandexMobileBot|yandex|yeti|Zeus/i';
            return !!preg_match($robotRegex, $agent);
        } else {
            return true;
        }
    }

    /**
     * Check if the given path is an absolute path.
     *
     * @param string $path The path to check.
     * @return bool `true` if the path is an absolute path, `false` otherwise.
     */
    public function isAbsolutePath(string $path): bool
    {
        if (preg_match('#^[a-z][a-z\d+.-]*://#i', $path)) {
            return true; // wrapper path (e.g. file://)
        }
        if (preg_match('#^[a-z]:(?:\\\\|/)#i', $path)) {
            return true; // Windows absolute path (e.g. C:\)
        }
        if (preg_match('#^/|~/#', $path)) {
            return true; // Unix/Linux absolute path (e.g. /)
        }
        return false;
    }

    /**
     * Replaces backslashes with forward slashes in a given string.
     */
    protected function slash(?string $path): string
    {
        return str_replace("\\", "/", $path);
    }
}
