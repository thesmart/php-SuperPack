<?php
/**
 * Generate some markup. This is not a unit test
 */
namespace SuperPack;

require_once 'bootstrap.php';

// create instances of JS and CSS package managers
global $JS_PACK, $CSS_PACK;
JsPack::setInstance($JS_PACK = new JsPack());
CssPack::setInstance($CSS_PACK = new CssPack());

// build a large pack for all of your common JavaScript
define('JS_BASE', 'JS_BASE');
$JS_PACK->register(JS_BASE, array(
	'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.js',
	'/js/ender/node_modules/underscore/underscore.js',
	'/js/ender/node_modules/backbone/backbone.js',
	'/js/bootstrap/bootstrap-button.js',
	'/js/bootstrap/bootstrap-collapse.js',
	'/js/bootstrap/bootstrap-modal.js',
	'/js/bootstrap/bootstrap-alert.js',
	'/js/bootstrap/bootstrap-carousel.js',
	'/js/bootstrap/bootstrap-tooltip.js',
));

// create additional packs for supplemental pages
define('JS_PROFILE', 'JS_PROFILE');
$JS_PACK->register(JS_PROFILE, array(
	'<script>alert("Hello, dear user.");</script>',
	'alert("this also works.");'
));

$JS_PACK->inclHead(JS_BASE);
$JS_PACK->inclHead(JS_PROFILE);

foreach (array('alert', 'me', 'thrice') as $msg) {
	$JS_PACK->inclDomReady(sprintf("alert(%s)", json_encode($msg)));
}

$JS_PACK->inclIE('/js/html5.js', 6, 8);
// ideally, this would be included in a package, too
//$JS_PACK->inclIE(JS_IE_FIXES);

define('CSS_BASE', 'CSS_BASE');
$CSS_PACK->register(CSS_BASE, array(
	'/less/bootstrap.css'
));

// ...later during run-time
$CSS_PACK->inclHead(CSS_BASE);

?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?=$CSS_PACK->renderHead()?>
<?=$JS_PACK->renderHead()?>
</head>
<body>
<?=$CSS_PACK->renderBody()?>
<?=$JS_PACK->renderBody()?>
	Hello World.
<?=$CSS_PACK->renderDomReady()?>
<?=$JS_PACK->renderDomReady()?>
</body>
</html>
