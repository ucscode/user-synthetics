<?php

namespace Uss\Component\Kernel\Interface;

use DateTimeInterface;
use Uss\Component\Kernel\Resource\Enumerator;

interface UssFrameworkInterface
{
    /**
     * Render A Twig Template
     *
     * @param string    $templateFile   Reference to the twig template.
     * @param array     $variables:     A list of variables that will be passed to the template
     * @param bool      $return         Whether to return or print the output
     */
    public function render(string $templatePath, array $variables, bool $return = false): ?string;

    /**
    * Terminate the script and print a JSON response.
    *
    * @param bool|int|null $status   The status of the response.
    * @param string|null   $message  The optional message associated with the response.
    * @param mixed         $data     Additional data to include in the response.
    */
    public function terminate(bool|int|null $status, ?string $message, mixed $data = []): void;

    /**
     * Retrieve URL request path segments.
     *
     * @param int|null  $index  index of the segment to retrieve.
     *                          If not provided, returns the entire array of segments.
     */
    public function getUrlSegments(?int $index): array|string|null;

    /**
     * Generate a one-time security token.
     *
     * @param string        $input      The secret input used to generate the token. Defaults to '1' if not provided.
     * @param string|null   $token      The token to verify. If not provided, a new token is generated.
     *
     * @return string|bool  If no token is provided, returns a one-time security token.
     *                      If a token is provided, returns a `boolean` indicating whether the token is valid.
     */
    public function nonce($input, ?string $receivedNonce): string|bool;

    /**
    * Generate URL from an absolute filesystem path.
    *
    * @param string $pathname   The pathname to be converted in the URL.
    * @param bool   $hidebase   Whether to hide the URL base or not.
    */
    public function pathToUrl(string $pathname, bool $hidebase): string;

    /**
     * Resolve and retrieve the schema for a namespaced template path.
     *
     * @param string|null   $templatePath   The namespaced path of the template (e.g., "@namespace/path/to/template.html.twig").
     * @param Enumerator    $enum           The enumerator indicating the desired output type:
     *                                      Enumerator::URL or Enumerator::FILE_SYSTEM.
     * @param int           $index          The index of the selected filesystem path when multiple are available.
     *                                      Expects 0 as default.
     *
     * @return string       The resolved schema for the specified template path.
     */
    public function getTemplateSchema(?string $templatePath, Enumerator $enum, int $index): string;

    /**
     * Generate a Random Key
     *
     * @param int       $length         The length of the key to be generated. Default is 10.
     * @param bool      $use_spec_char  Whether to include special characters in the key. Default is `false`.
     *
     * @return string   The generated random key.
     */
    public function keygen(int $length, bool $use_special_char): string;

    /**
     * Clean and normalize a path or array of path segments.
     *
     * @param string|array  $path       The path or array of path segments to be filtered and normalized.
     * @param string        $divider    The separator used to implode the array of path segments. Defaults to '/'
     *
     * @return string       The cleaned and normalized path.
     */
    public function filterContext(string|array $path, string $seperator = '/'): string;

    /**
     * Convert array to HTML Attributes
     *
     * @param array     $array          The array containing the key-value pairs to be converted.
     * @param bool      $singleQuote    Whether to use single quotes for attribute values. Default is `false`.
     *
     * @return string   The HTML attribute string
     */
    public function arrayToHtmlAttrs(array $array, bool $singleQuote = false): string;

    /**
     * Calculate Elapsed Time (E.g 3 days ago)
     *
     * @param DateTimeInterface|int|string  $time       A DateTimeInterface object, timestamp string, or Unix timestamp
     * @param bool                          $verbose    Determines the level of detail in the time output.
     *
     * @return string                       The elapsed time in a human-readable format.
     */
    public function relativeTime(DateTimeInterface|int|string $time, bool $verbose = false): string;

    /**
     * Check if the given path is an absolute path.
     *
     * @param string $path The path to check.
     *
     * @return bool
     */
    public function isAbsolutePath(string $path): bool;
}
