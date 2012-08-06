<?php

namespace SuperPack;

/**
 * A class tracking Javascript dependencies and rendering that markup
 */
class JsPack extends Pack {

	/**
	 * Render styles in the head tag.
	 * NOTE: IE-specific scripts will be rendered FIRST
	 * @return string
	 */
	public function renderHead() {
		return $this->renderIe() . "\n" . parent::renderHead();
	}

	/**
	 * Render a single style
	 *
	 * @param string $entity
	 * @return string
	 */
	protected function renderEntity($entity) {
		if (static::isPath($entity)) {
			return <<<MARKUP
<script src="$entity"></script>
MARKUP;
		} else if ($this->isMarkup($entity)) {
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
