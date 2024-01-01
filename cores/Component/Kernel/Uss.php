<?php

namespace Uss\Component\Kernel;

use Uss\Component\Kernel\Abstract\AbstractUss;
use Uss\Component\Kernel\System\Extension;
use Uss\Component\Trait\SingletonTrait;

final class Uss extends AbstractUss
{
    use SingletonTrait;

    /**
     * Render A Twig Template
     *
     * @param string $templateFile: Reference to the twig template.
     * @param array $variables:     A list of variables that will be passed to the template
     * @param bool $return          Whether to return or print the output
     */
    public function render(string $templateFile, array $variables = [], bool $return = false): ?string
    {
        $this->twigEnvironment->addGlobal(self::NAMESPACE, new Extension($this));
        $variables += $this->twigContext;
        $result = $this->twigEnvironment->render($templateFile, $variables);
        return $return ? $result : call_user_func(function () use ($result) {
            print($result);
            die();
        });
    }

    /**
     * Retrieve URL request path segments.
     *
     * @param int|null $index - index of the segment to retrieve. If not provided, returns the entire array of segments.
     * @return array|string|null
     */
    public function getUrlSegments(?int $index = null): array|string|null
    {
        $path = explode("?", $_SERVER['REQUEST_URI'])[0] ?? '';
        $path = str_replace($this->slash(ROOT_DIR), '', $this->slash($_SERVER['DOCUMENT_ROOT']) . $path);
        $request = array_values(array_filter(array_map('trim', explode("/", $path))));
        return !is_null($index) ? ($request[$index] ?? null) : $request;
    }

    /**
     * Generate a one-time security token.
     *
     * @param string $input The secret input used to generate the token. Defaults to '1' if not provided.
     * @param string|null $token The token to verify. If not provided, a new token is generated.
     * @return string|bool If no token is provided, returns a one-time security token. If a token is provided, returns a `boolean` indicating whether the token is valid.
     */
    public function nonce($input = '1', ?string $receivedNonce = null): string|bool
    {
        $secretKey = UssImmutable::SECRET_KEY . md5($_SESSION['USSID']);
        $algorithm = 'ripemd160';
        $salt = bin2hex(random_bytes(3));
        $dataToHash = $input . $salt . $secretKey;
        $nonce = hash_hmac($algorithm, $dataToHash, $secretKey);

        if($receivedNonce !== null) {
            $token = explode(':', $receivedNonce);
            if(count($token) === 2) {
                list($expectedNonce, $expectedSalt) = $token;
                $computedNonce = hash_hmac($algorithm, $input . $expectedSalt . $secretKey, $secretKey);
                return hash_equals($computedNonce, $expectedNonce);
            }
            return false;
        }

        return $nonce . ':' . $salt;
    }

    /**
     * Terminate the script and print a JSON response.
     *
     * @param bool|int|null $status   The status of the response.
     * @param string|null   $message  The optional message associated with the response.
     * @param array         $data     Additional data to include in the response.
     */
    public function terminate(bool|int|null $status, ?string $message = null, array $data = []): void
    {
        $response = [
            "status" => $status,
            "message" => $message,
            "data" => $data
        ];
        exit(json_encode($response, JSON_PRETTY_PRINT));
    }

    /**
     * Explode a content by a seperator and rejoin the filtered value
     */
    public function filterContext(string|array $path, string $divider = '/'): string
    {
        if(is_array($path)) {
            $path = implode($divider, $path);
        };
        $explosion = array_filter(array_map('trim', explode("/", $path)));
        return implode("/", $explosion);
    }

    /**
    * Convert a namespace path to file system path or URL
    */
   public function getTemplateSchema(?string $templatePath = Uss::NAMESPACE, Enumerator $enum = Enumerator::FILE_SYSTEM, int $index = 0): string
   {
       $templatePath = $this->filterContext($templatePath);
       if(!preg_match('/^@\w+/i', $templatePath)) {
           $templatePath = '@' . Uss::NAMESPACE . '/' . $templatePath;
       }

       $context = explode("/", $templatePath);
       $namespace = str_replace('@', '', array_shift($context));
       $filesystem = $this->filesystemLoader->getPaths($namespace)[$index] ?? null;
       $prefix = '';

       if($filesystem) {
           $prefix = match($enum) {
               Enumerator::FILE_SYSTEM => $filesystem,
               Enumerator::THEME => "@{$namespace}",
               default => $this->pathToUrl($filesystem)
           };
       }

       return $prefix . '/' . $this->filterContext(implode('/', $context));
   }
};
