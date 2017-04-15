<?php
declare(strict_types = 1);
namespace Klapuch\Application;

interface Page {
	/**
	 * The page
	 * @return string
	 */
	public function __toString(): string;
}