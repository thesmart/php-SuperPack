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

Start by naming and registering your packages at the beginning of your index.php. Packages can consist of:

 1. Relative paths
 1. Absolute paths
 1. Plain-text scripts
 1. Markup

	// build a large pack for all of your common JavaScript
	define('JS_BASE', 'JS_BASE');
	JsPack::register(JS_BASE, array(
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
	JsPack::register(JS_PROFILE, array(
		'<script>alert("Hello, dear user.");</script>',
		'alert("this also works.");'
	));

During run-time, you can declare a dependency on a package easily:

	JsPack::inclHead(JS_BASE);
	JsPack::inclHead(JS_PROFILE);

You can also use include scripts that are NOT part of a package, though this is **not recommended**:

	foreach (array('alert', 'me', 'thrice') as $msg) {
		JsPack::inclDomReady(sprintf("alert(%s)", json_encode($msg)));
	}

Internet Explorer hacks are also supported:

	JsPack::inclIE('/js/html5.js', 6, 8);
	// ideally, this would be included in a package, too
	//JsPack::inclIE(JS_IE_FIXES);

All of this also works for CSS:

	define('CSS_BASE', 'CSS_BASE');
	CssPack::register(CSS_BASE, array(
		'/less/bootstrap.css'
	));

	// ...later during run-time
	CssPack::inclHead(CSS_BASE);

The output looks like this:




Like this project?
------------------

Check out my others.
[@thesmart](https://twitter.com/thesmart)