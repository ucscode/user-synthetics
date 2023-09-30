<?php

final class UssTwigGlobalExtension {

    public string $jsElement;

    private $availMethods = [
        'getUrl',
        'keygen'
    ];

    public function __construct(
        private string $namespace
    ) {
        $uss = Uss::instance();
        $uss->addJsProperty('platform', UssEnum::PROJECT_NAME);
        $jsonElement = json_encode($uss->getJsProperty());
        $this->jsElement = base64_encode($jsonElement);
    }

    public function __call($name, $args) {
        if(!in_array($name, $this->availMethods)) {
            throw new \Exception("Permission to call `Uss::{$name}()` method within Twig Template is denied");
        }
        return call_user_func_array([Uss::instance(), $name], $args);
    }

    public function renderBlocks(string $name, int $indent = 1): ?string {
        $blockManager = UssTwigBlockManager::instance();
        $blocks = $blockManager->getBlocks($name);
        if(is_array($blocks)) {
            $indent = str_repeat("\t", abs($indent));
            return implode("\n{$indent}", $blocks);
        };
        return null;
    }

    # Get an option
    public function getOption(string $name): mixed
    {
        $uss = Uss::instance();
        return $uss->options->get($name);
    }


}