<?php
declare(strict_types = 1);
namespace Klapuch\Application;

interface Body {
	/**
	 * Data in the body
	 * @return string
	 */
	public function data(): string;
}