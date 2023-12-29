<?php

namespace Uss\Component\Kernel;

use mysqli;
use mysqli_sql_exception;
use Ucscode\Pairs\Pairs;
use Ucscode\UssElement\UssElement;
use Uss\Component\Kernel\UssImmutable;
use Uss\Component\Manager\BlockManager;
use Uss\Component\Database;

final class Prime
{
    public function __construct(protected Uss $uss)
    {
    }

    public function getMysqliInstance(): ?mysqli
    {
        $mysqli = null;
        if(Database::ENABLED) {
            try {
                $mysqli = @new mysqli(
                    Database::HOST,
                    Database::USERNAME,
                    Database::PASSWORD,
                    Database::NAME,
                    Database::PORT
                );
            } catch(mysqli_sql_exception $e) {
                $this->uss->render('@Uss/db.error.html.twig', [
                    'error' => $e->getMessage(),
                    'url' => UssImmutable::GITHUB_REPO,
                    'mail' => UssImmutable::AUTHOR_EMAIL
                ]);
                exit();
            };
        };
        return $mysqli;
    }

    public function getPairsInstance(?mysqli $mysqli = null): ?Pairs
    {
        $options = null;
        if($mysqli) {
            try {
                $options = new Pairs($mysqli, Database::PREFIX . "options");
            } catch(\Exception $e) {
                $this->uss->render('@Uss/error.html.twig', [
                    'subject' => "Library Error",
                    'message' => $e->getMessage()
                ]);
                exit();
            }
        }
        return $options;
    }

    public function createSession(string $sessionIndex, string $cookieIndex): void
    {
        if(empty(session_id())) {
            session_start();
        }

        if(empty($_SESSION[$sessionIndex])) {
            $_SESSION[$sessionIndex] = $this->uss->keygen(40, true);
        };

        if(empty($_COOKIE[$cookieIndex])) {
            $time = (new \DateTime())->add((new \DateInterval("P3M")));
            $_COOKIE[$cookieIndex] = uniqid($this->uss->keygen(7));
            setrawcookie($cookieIndex, $_COOKIE[$cookieIndex], $time->getTimestamp(), '/');
        };
    }

    /**
    * @ignore
    */
    public function loadHTMLResource(): void
    {
        $vendors = [
            'head_css' => [
                'bootstrap' => 'css/bootstrap.min.css',
                'bs-icon' => 'vendor/bootstrap-icons/bootstrap-icons.min.css',
                'animate' => 'css/animate.min.css',
                'glightbox' => "vendor/glightbox/glightbox.min.css",
                'izitoast' => 'vendor/izitoast/css/iziToast.min.css',
                'font-size' => "css/font-size.min.css",
                'main-css' => 'css/main.css'
            ],
            'body_js' => [
                'jquery' => 'js/jquery-3.7.1.min.js',
                'bootstrap' => 'js/bootstrap.bundle.min.js',
                'bootbox' => 'js/bootbox.all.min.js',
                'glightbox' => "vendor/glightbox/glightbox.min.js",
                'izitoast' => 'vendor/izitoast/js/iziToast.min.js',
                'notiflix-loading' => 'vendor/notiflix/notiflix-loading-aio-3.2.6.min.js',
                'notiflix-block' => 'vendor/notiflix/notiflix-block-aio-3.2.6.min.js',
                'main-js' => 'js/main.js'
            ]
        ];

        $blockManager = BlockManager::instance();

        foreach($vendors as $block => $contents) {

            $contents = array_map(function ($key, $value) {
                $type = explode(".", $value);
                $value = $this->uss->pathToUrl(UssImmutable::ASSETS_DIR . "/" . $value);
                if(strtolower(end($type)) === 'css') {
                    $element = (new UssElement(UssElement::NODE_LINK))
                        ->setAttribute('rel', 'stylesheet')
                        ->setAttribute('href', $value);
                } else {
                    $element = (new UssElement(UssElement::NODE_SCRIPT))
                        ->setAttribute('type', 'text/javascript')
                        ->setAttribute('src', $value);
                };
                return $element->getHTML();
            }, array_keys($contents), $contents);

            $blockManager->appendTo($block, $contents);

        };
    }
};
