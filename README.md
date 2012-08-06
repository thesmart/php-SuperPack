SuperPack by John Smart ([@thesmart](https://github.com/thesmart))
=============

A package management tool that optimizes CSS and JavaScript download times

Organize your static files into packs (packages). Packs can be developed and debugged while in development mode. Once
put into production mode, packs are run through Closure Compiler.  This optimizes the payload while preserving script
execution order.

TODO List
---------

 * Create script to upload files to Amazon S3, Akamai, and other popular CDNs

Example
-------

Start by creating your package managers

	// create instances of JS and CSS package managers
	global $JS_PACK, $CSS_PACK;
	JsPack::setInstance($JS_PACK = new JsPack());
	CssPack::setInstance($CSS_PACK = new CssPack());

Then, register your packages.  Packages can consist of:

 1. Relative paths
 1. Absolute paths
 1. Plain-text scripts
 1. Markup

Defining your packages once in index.html.  Note that it is best to create separate packages for separate pages in your site

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

During run-time, you can declare a dependency on a package easily:

	$JS_PACK->inclHead(JS_BASE);
	$JS_PACK->inclHead(JS_PROFILE);

You can also inject scripts directly:

	foreach (array('alert', 'me', 'thrice') as $msg) {
		$JS_PACK->inclDomReady(sprintf("alert(%s)", json_encode($msg)));
	}

Internet Explorer hacks can also be managed with version control:

	$JS_PACK->inclIE('/js/html5.js', 6, 8);
	// ideally, this would be included in a package, too
	//$JS_PACK->inclIE(JS_IE_FIXES);

Everything supported by JsPack is also support for CSS:

	define('CSS_BASE', 'CSS_BASE');
	$CSS_PACK->register(CSS_BASE, array(
		'/less/bootstrap.css'
	));

	// ...later during run-time
	$CSS_PACK->inclHead(CSS_BASE);

Render the scripts into your markup templates:

	<? global $JS_PACK, $CSS_PACK; ?>
	<!DOCTYPE html>
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

The output might look like this:

	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="/less/bootstrap.css" rel="stylesheet">
	<!--[if (gte IE 6)&(lte IE 8)]>
	<script src="/js/html5.js"></script>
	<![endif]-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.js"></script>
	<script src="/js/ender/node_modules/underscore/underscore.js"></script>
	<script src="/js/ender/node_modules/backbone/backbone.js"></script>
	<script src="/js/bootstrap/bootstrap-button.js"></script>
	<script src="/js/bootstrap/bootstrap-collapse.js"></script>
	<script src="/js/bootstrap/bootstrap-modal.js"></script>
	<script src="/js/bootstrap/bootstrap-alert.js"></script>
	<script src="/js/bootstrap/bootstrap-carousel.js"></script>
	<script src="/js/bootstrap/bootstrap-tooltip.js"></script>
	<script>alert("Hello, dear user.");</script>
	<script>
	alert("this also works.");
	</script></head>
	<body>
		Hello World.
	<script>
	alert("alert")
	</script>
	<script>
	alert("me")
	</script>
	<script>
	alert("thrice")
	</script></body>
	</html>

Like this project?
------------------

Check out my others.
[@thesmart](https://twitter.com/thesmart)