<?php

namespace Uss\Component\Kernel\System;

use mysqli;
use mysqli_sql_exception;
use Ucscode\Pairs\Pairs;
use Ucscode\UssElement\UssElement;
use Uss\Component\Block\Block;
use Uss\Component\Block\BlockManager;
use Uss\Component\Kernel\UssImmutable;
use Uss\Component\Kernel\Uss;

final class Prime
{
    public function __construct(protected Uss $uss)
    {
        $this->registerSystemUnitBlocks();
    }

    public function getMysqliInstance(): ?mysqli
    {
        $mysqli = null;

        if(filter_var($_ENV['DB_ENABLED'], FILTER_VALIDATE_BOOLEAN)) {
            try {
                $mysqli = @new mysqli(
                    $_ENV['DB_HOST'],
                    $_ENV['DB_USERNAME'],
                    $_ENV['DB_PASSWORD'],
                    $_ENV['DB_NAME'],
                    $_ENV['DB_PORT']
                );
            } catch(mysqli_sql_exception $e) {
                $this->uss->render('@Uss/db.error.html.twig', [
                    'error' => $e->getMessage(),
                    'url' => UssImmutable::PROJECT_GITHUB_REPOSITORY,
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
                $options = new Pairs($mysqli, $_ENV['DB_PREFIX'] . "options");
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
        foreach(ResourcePathMapper::BLOCK_VENDORS as $blockName => $resource) {

            $block = BlockManager::instance()->getBlock($blockName);

            foreach($resource as $name => $file) {

                $link = $this->uss->pathToUrl(UssImmutable::ASSETS_DIR . '/' . $file);

                switch($blockName) {
                    case ResourcePathMapper::HEAD_RESOURCE:
                        $node = new UssElement(UssElement::NODE_LINK);
                        $node->setAttribute("rel", "stylesheet");
                        $node->setAttribute("href", $link);
                        break;
                    default:
                        $node = new UssElement(UssElement::NODE_SCRIPT);
                        $node->setAttribute("type", "text/javascript");
                        $node->setAttribute("src", $link);
                }

                $node->setAttribute('data-name', $name);
                $block->addContent($name, $node->getHTML(true));
            }
        };
    }

    protected function registerSystemUnitBlocks(): void
    {
        $blocks = [
            ResourcePathMapper::HEAD_RESOURCE,
            ResourcePathMapper::HEAD_JAVASCRIPT,
            ResourcePathMapper::BODY_JAVASCRIPT, 
        ];

        foreach($blocks as $blockName) {
            BlockManager::instance()->addBlock($blockName, new Block(true));
        };
    }
};
