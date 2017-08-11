<?php
declare(strict_types = 1);
namespace Klapuch\Application;

use Klapuch\Output;

interface Request {
	/**
	 * Body of the response in arbitrary format
	 * @return \Klapuch\Output\Format
	 */
	public function body(): Output\Format;

	/**
	 * Response headers in key(Field) => value(Value) format
	 * @return array
	 */
	public function headers(): array;
}