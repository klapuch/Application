<?php
declare(strict_types = 1);
namespace Klapuch\Application;

use Klapuch\Output;

interface Response {
	/**
	 * Body of the response in arbitrary format
	 * @return \Klapuch\Output\Format
	 */
	public function body(): Output\Format;
}