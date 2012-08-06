<?php

namespace SuperPack;

/**
 * A base class for static package management
 */
abstract class Pack {

	/**
	 * @var array
	 */
	public static $packs = array();

	/**
	 * @var array
	 */
	public static $forIe = array();

	/**
	 * @var array
	 */
	public static $forHead	= array();

	/**
	 * @var array
	 */
	public static $forBody	= array();

	/**
	 * @var array
	 */
	public static $forDomReady	= array();

	/**
	 * Register a package as available for inclusion using one of the incl* methods
	 *
	 * @static
	 * @param string $name		A name for the package
	 * @param array $code		An array of paths references code snippets or files
	 */
	public static function register($name, $code) {
		if (isset(static::$packs[$name])) {
			// merge
			static::$packs[$name]	= array_merge(static::$packs[$name], $code);
		} else {
			static::$packs[$name]	= $code;
		}
	}

	/**
	 * Include a code snippet, pack name, or path to be rendered in the head tag
	 * @param array|string $codePackOrPath
	 * @return void
	 */
	public static function inclHead($codePackOrPath) {
		static::$forHead[]	= $codePackOrPath;
	}
	
	/**
	 * Include a code snippet, pack name, or path to be rendered after the opening body tag
	 * @param array|string $codePackOrPath
	 * @return void
	 */
	public static function inclBody($codePackOrPath) {
		static::$forBody[]	= $codePackOrPath;
	}

	/**
	 * Include a code snippet, pack name, or path to be rendered before the closing body tag
	 * @param array|string $codePackOrPath
	 * @return void
	 */
	public static function inclDomReady($codePackOrPath) {
		static::$forDomReady[]	= $codePackOrPath;
	}

	/**
	 * Include code that will be executed only for a version of Internet Explorer.
	 *
	 * @link http://msdn.microsoft.com/en-us/library/ms537512(v=vs.85).aspx
	 *
	 * @static
	 * @param array|string $codePackOrPath
	 * @param int $minVer			min version you want to render this script for (inclusive)
	 * @param int $maxVer			max version you want to render this script for (inclusive)
	 */
	public static function inclIE($codePackOrPath, $minVer, $maxVer) {
		$minVer2	= $minVer;
		$maxVer2	= $maxVer;

		$minVer		= min($minVer2, $maxVer2);
		$maxVer		= max($minVer2, $maxVer2);

		static::$forIe[]	= compact('minVer', 'maxVer', 'codePackOrPath');
	}

	/**
	 * Render packages included to be emitted in the head tag
	 * @static
	 * @return string
	 */
	public static function renderHead() {
		return static::renderPackages(static::$forHead);
	}

	/**
	 * Render packages included to be emitted right after opening body tag
	 * @static
	 * @return string
	 */
	public static function renderBody() {
		return static::renderPackages(static::$forBody);
	}

	/**
	 * Render packages included to be emitted right before closing body tag
	 * @static
	 * @return string
	 */
	public static function renderDomReady() {
		return static::renderPackages(static::$forDomReady);
	}

	/**
	 * Render packages included to be emitted and excuted for IE only
	 * @static
	 * @return string
	 */
	public static function renderIe() {
		$ieScripts = array();
		foreach (static::$forIe as $instruction) {
			/**
			 * @var int $minVer
			 * @var int $maxVer
			 * @var array|string $codePackOrPath
			 */
			extract($instruction);

			$ieScripts[] = <<<OPEN
<!--[if (gte IE $minVer)&(lte IE $maxVer)]>
OPEN;
			$ieScripts[] = static::renderEntity($codePackOrPath);
			$ieScripts[] = <<<CLOSE
<![endif]-->
CLOSE;
		}

		return implode("\n", $ieScripts);
	}

	/**
	 * Reset Pack to base state.
	 * @static
	 */
	public static function reset() {
		static::$packs = array();
		static::$forIe = array();
		static::$forHead	= array();
		static::$forBody	= array();
		static::$forDomReady	= array();
	}

	/**
	 * Is a string a script tag or something else that should be emitted to the page directly?
	 * @static
	 * @param string $string
	 * @return bool
	 */
	protected static function isMarkup($string) {
		return mb_substr($string, 0, 1) === '<';
	}

	/**
	 * Given an array packages, render markup to include the packages
	 *
	 * @static
	 * @param array $packages
	 * @return string
	 */
	protected static function renderPackages(array $packages) {
		$buff	= array();

		foreach ($packages as $codePackOrPath) {
			if (array_key_exists($codePackOrPath, static::$packs)) {
				// a registered package
				foreach (static::$packs[$codePackOrPath] as $src) {
					$buff[] = static::renderEntity($src);
				}
			} else {
				// a single entity
				$codeOrPath = $codePackOrPath;
				$buff[] = static::renderEntity($codeOrPath);
			}
		}

		return implode("\n", $buff);
	}

	/**
	 * Given a single entity, render markup to include the entity
	 *
	 * @static
	 * @abstract
	 * @param string $entity
	 * @return string
	 */
	protected abstract static function renderEntity($entity);
}
