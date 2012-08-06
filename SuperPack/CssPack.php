<?php

namespace SuperPack;

/**
 * A class tracking CSS dependencies and rendering that markup
 */
class CssPack extends Pack {

	/**
	 * Render styles in the head tag.
	 * NOTE: IE-specific style overrides will be rendered LAST
	 * @return string
	 */
	public function renderHead() {
		return parent::renderHead() . "\n" . $this->renderIe();
	}

	/**
	 * Render a single script
	 *
	 * @param string $entity
	 * @return string
	 */
	protected function renderEntity($entity) {
		if (static::isPath($entity)) {
			return <<<MARKUP
<link href="$entity" rel="stylesheet">
MARKUP;
		} else if ($this->isMarkup($entity)) {
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
