<?php

namespace Uss\Component\Kernel;

use Uss\Component\Trait\SingletonTrait;

final class Uss extends AbstractUss
{
    use SingletonTrait;

    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * Render A Twig Template
     *
     * @param string $templateFile: Reference to the twig template.
     * @param array $variables:     A list of variables that will be passed to the template
     *
     * @return void
     */
    public function render(string $templateFile, array $variables = [], $return = false): ?string
    {
        $variables += $this->globalTwigOptions;
        $this->twigEnvironment->addGlobal(self::NAMESPACE, new UssTwigExtension($this));
        $result = $this->twigEnvironment->render($templateFile, $variables);
        return $return ? $result : call_user_func(function () use ($result) {
            print($result);
            die();
        });
    }

    /**
     * Retrieve URL request path segments.
     *
     * @param int|null $index       Optional: index of the segment to retrieve. If not provided, returns the entire array of segments.
     * @return array|string|null    The array of URL path segments if no index is provided, the segment at the specified index, or `null` if the index is out of range or the request string is not set.
     */
    public function splitUri(?int $index = null): array|string|null
    {
        $documentRoot = $this->slash($_SERVER['DOCUMENT_ROOT']);
        $projectRoot = $this->slash(ROOT_DIR);
        $requestUri = explode("?", $_SERVER['REQUEST_URI']);
        $path = $requestUri[0] ?? '';
        $path = str_replace($projectRoot, '', $documentRoot . $path);
        $request = array_values(array_filter(array_map('trim', explode("/", $path))));
        return is_numeric($index) ? ($request[$index] ?? null) : $request;
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

        if($receivedNonce === null) {
            return $nonce . ':' . $salt;
        } else {
            $token = explode(':', $receivedNonce);
            if(count($token) === 2) {
                list($expectedNonce, $expectedSalt) = $token;
                $computedNonce = hash_hmac($algorithm, $input . $expectedSalt . $secretKey, $secretKey);
                return hash_equals($computedNonce, $expectedNonce);
            } else {
                return false;
            }
        }
    }

};
