<?php

namespace SuperPack;

/**
 * A class tracking CSS dependencies and rendering that markup
 */
class CssPack extends Pack {

	/**
	 * Render styles in the head tag.
	 * NOTE: IE-specific style overrides will be rendered LAST
	 * @static
	 * @return string
	 */
	public static function renderHead() {
		return parent::renderHead() . "\n" . static::renderIe();
	}

	/**
	 * Render a single script
	 *
	 * @static
	 * @param string $entity
	 * @return string
	 */
	protected static function renderEntity($entity) {
		if (filter_var($entity, FILTER_VALIDATE_URL)) {
			return <<<MARKUP
<link href="$entity" rel="stylesheet">
MARKUP;
		} else if (static::isMarkup($entity)) {
			// some kind of markup
			return $entity;
		} else {
			// plain-text
			return <<<MARKUP
<style>
$entity
</style>
MARKUP;
		}
	}
}
