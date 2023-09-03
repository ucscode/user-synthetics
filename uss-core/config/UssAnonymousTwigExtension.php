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

    public $twigBlockManager;

    public function __construct(Uss $ussInstance, UssTwigBlockManager $ussTwigBlockManager)
    {
        $this->twigBlockManager = $ussTwigBlockManager;
        $ussInstance->console('platform', PROJECT_NAME);
        $this->setAttr('console64', base64_encode(json_encode($ussInstance->console())));
    }

    private function setAttr(string $key, mixed $value)
    {
        $this->attr[$key] = $value;
    }

    /**
     * Equivalent to call_user_func
     */
    public function call()
    {
        $args = func_get_args();
        $callback = array_shift($args);
        $result = call_user_func_array($callback, $args);
        return $result;
    }

};
