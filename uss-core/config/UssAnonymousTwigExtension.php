<?php
/**
 * User Synthetics Twig Extension
 *
 * Within twig is a global variable referenced as `Uss`
 * The variable methods are accessed directly (E.G Uss.manageeBlock())
 * while the properties are access from the `attr` array (E.G Uss.attr.properyName).
 * All of which are managed by this anonymous class
 */
 
return new class ($this, $ussTwigBlockManager) {

    use SingletonTrait;

    public array $attr = [];

    public function __construct(
        private Uss $ussInstance, 
        public UssTwigBlockManager $twigBlockManager
    )
    {
        $this->ussInstance->console('platform', PROJECT_NAME);
        $this->attr['console64'] = base64_encode(json_encode($ussInstance->console()));
    }

    # Equivalent to call_user_func
    public function call()
    {
        $args = func_get_args();
        $callback = array_shift($args);
        $result = call_user_func_array($callback, $args);
        return $result;
    }

    # Convert Filesystem To Url
    public function toUrl(string $absolutePath, bool $hidebase = false)
    {
        return Core::url($absolutePath, $hidebase);
    }

    # Get an option
    public function getOption(string $name) {
        return $this->ussInstance->options->get($name);
    }

};
