<?php

namespace SuperPack;

use singleton\Singleton;

/**
 * A base class for static package management
 */
abstract class Pack extends Singleton {

	/**
	 * @var array
	 */
	public $packs = array();

	/**
	 * @var array
	 */
	public $forIe = array();

	/**
	 * @var array
	 */
	public $forHead	= array();

	/**
	 * @var array
	 */
	public $forBody	= array();

	/**
	 * @var array
	 */
	public $forDomReady	= array();

	/**
	 * Register a package as available for inclusion using one of the incl* methods
	 *
	 * @param string $name		A name for the package
	 * @param array $code		An array of paths references code snippets or files
	 */
	public function register($name, $code) {
		if (isset($this->packs[$name])) {
			// merge
			$this->packs[$name]	= array_merge($this->packs[$name], $code);
		} else {
			$this->packs[$name]	= $code;
		}
	}

	/**
	 * Include a code snippet, pack name, or path to be rendered in the head tag
	 * @param array|string $codePackOrPath
	 * @return void
	 */
	public function inclHead($codePackOrPath) {
		$this->forHead[]	= $codePackOrPath;
	}
	
	/**
	 * Include a code snippet, pack name, or path to be rendered after the opening body tag
	 * @param array|string $codePackOrPath
	 * @return void
	 */
	public function inclBody($codePackOrPath) {
		$this->forBody[]	= $codePackOrPath;
	}

	/**
	 * Include a code snippet, pack name, or path to be rendered before the closing body tag
	 * @param array|string $codePackOrPath
	 * @return void
	 */
	public function inclDomReady($codePackOrPath) {
		$this->forDomReady[]	= $codePackOrPath;
	}

	/**
	 * Include code that will be executed only for a version of Internet Explorer.
	 *
	 * @link http://msdn.microsoft.com/en-us/library/ms537512(v=vs.85).aspx
	 *
	 * @param array|string $codePackOrPath
	 * @param int $minVer			min version you want to render this script for (inclusive)
	 * @param int $maxVer			max version you want to render this script for (inclusive)
	 */
	public function inclIE($codePackOrPath, $minVer, $maxVer) {
		$minVer2	= $minVer;
		$maxVer2	= $maxVer;

		$minVer		= min($minVer2, $maxVer2);
		$maxVer		= max($minVer2, $maxVer2);

		$this->forIe[]	= compact('minVer', 'maxVer', 'codePackOrPath');
	}

	/**
	 * Render packages included to be emitted in the head tag
	 * @return string
	 */
	public function renderHead() {
		return $this->renderPackages($this->forHead);
	}

	/**
	 * Render packages included to be emitted right after opening body tag
	 * @return string
	 */
	public function renderBody() {
		return $this->renderPackages($this->forBody);
	}

	/**
	 * Render packages included to be emitted right before closing body tag
	 * @return string
	 */
	public function renderDomReady() {
		return $this->renderPackages($this->forDomReady);
	}

	/**
	 * Render packages included to be emitted and excuted for IE only
	 * @return string
	 */
	public function renderIe() {
		$ieScripts = array();
		foreach ($this->forIe as $instruction) {
			/**
			 * @var int $minVer
			 * @var int $maxVer
			 * @var array|string $codePackOrPath
			 */
			extract($instruction);

			$ieScripts[] = <<<OPEN
<!--[if (gte IE $minVer)&(lte IE $maxVer)]>
OPEN;
			$ieScripts[] = $this->renderEntity($codePackOrPath);
			$ieScripts[] = <<<CLOSE
<![endif]-->
CLOSE;
		}

		return implode("\n", $ieScripts);
	}

	/**
	 * Reset Pack to base state.
	 */
	public function reset() {
		$this->packs = array();
		$this->forIe = array();
		$this->forHead	= array();
		$this->forBody	= array();
		$this->forDomReady	= array();
	}

	/**
	 * Is a string a script tag or something else that should be emitted to the page directly?
	 * @param string $string
	 * @return bool
	 */
	protected function isMarkup($string) {
		return mb_substr($string, 0, 1) === '<';
	}

	/**
	 * Determine if a string is actually a path, relative or absolute
	 * @param string $string
	 * @return bool
	 */
	protected function isPath($string) {
		if (mb_strlen($string) > 500) {
			// likely a script
			return false;
		}

		$slashPos = mb_stripos($string, '/');
		if (is_int($slashPos) && mb_stripos($string, '/') < 30) {
			// slash is one of the first 30 chars
			return true;
		}

		return false;
	}

	/**
	 * Given an array packages, render markup to include the packages
	 *
	 * @param array $packages
	 * @return string
	 */
	protected function renderPackages(array $packages) {
		$buff	= array();

		foreach ($packages as $codePackOrPath) {
			if (array_key_exists($codePackOrPath, $this->packs)) {
				// a registered package
				foreach ($this->packs[$codePackOrPath] as $src) {
					$buff[] = $this->renderEntity($src);
				}
			} else {
				// a single entity
				$codeOrPath = $codePackOrPath;
				$buff[] = $this->renderEntity($codeOrPath);
			}
		}

		return implode("\n", $buff);
	}

	/**
	 * Given a single entity, render markup to include the entity
	 *
	 * @abstract
	 * @param string $entity
	 * @return string
	 */
	protected abstract function renderEntity($entity);
}
