<?php

/**
 * This extension is a minified version of Uss class for twig
 * It provides only limited properties and methods from the Uss class to the twig template
 */
final class UssTwigGlobalExtension {

    public string $jsElement;

    public array $globals;

    public function __construct(
        private string $namespace
    ) {
        $uss = Uss::instance();
        $uss->addJsProperty('platform', UssEnum::PROJECT_NAME);
        $jsonElement = json_encode($uss->getJsProperty());
        $this->jsElement = base64_encode($jsonElement);
        $this->globals = Uss::$globals;
    }
    
    /**
     * Uss Methods
     */
    public function getUrl(string $path, bool $base = false): string 
    {
        return Uss::instance()->getUrl($path, $base);
    }

    public function keygen(int $length = 10, bool $use_spec_chars = false): string 
    {
        return Uss::instance()->keygen($length);
    }

    public function elapse($time, bool $full = false): string 
    {
        return Uss::instance()->elapse($time, $full);
    }

    /**
     * Self Methods
     */
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
        return Uss::instance()->options->get($name);
    }

}