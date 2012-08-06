<?php

namespace SuperPack;

/**
 * A class tracking Javascript dependencies and rendering that markup
 */
class JsPack extends Pack {

	/**
	 * Render styles in the head tag.
	 * NOTE: IE-specific scripts will be rendered FIRST
	 * @static
	 * @return string
	 */
	public static function renderHead() {
		return static::renderIe() . "\n" . parent::renderHead();
	}

	/**
	 * Render a single style
	 *
	 * @static
	 * @param string $entity
	 * @return string
	 */
	protected static function renderEntity($entity) {
		if (filter_var($entity, FILTER_VALIDATE_URL)) {
			return <<<MARKUP
<script src="$entity"></script>
MARKUP;
		} else if (static::isMarkup($entity)) {
			// some kind of markup
			return $entity;
		} else {
			// plain-text script
			return <<<MARKUP
<script>
$entity
</script>
MARKUP;
		}
	}
}
