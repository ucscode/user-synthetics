<?php
# Avoid Error Display
defined('ROOT_DIR') or die;
?>
<!doctype html>
<html>
<head>
	
	<?php

        # Pass variables from PHP to JavaScript environment
        $console = base64_encode(json_encode((object)$this->console));
echo "<script>const Uss = JSON.parse(atob('{$console}'));</script>\n";

# Before Default Scripts
Events::instance()->exec('@head:before');

# Print Default Scripts
echo self::include_libraries('head', $exclib, $inclib) . "\n";

# After Default Scripts
Events::instance()->exec('@head:after');

# Set Body Attributes;
$bodyAttrs = Core::array_to_html_attrs(Uss::instance()->$global['body.attrs']);

?>

</head>

<body <?php echo $bodyAttrs; ?>>
	
    <?php
    # Before Body Content
    Events::instance()->exec("@body:before");
?>
