<?php
declare(strict_types = 1);
namespace Klapuch\Application;

use Klapuch\Output;

interface Request {
	/**
	 * Response from the request
	 * @param array $parameters
	 * @return \Klapuch\Output\Template
	 */
	public function response(array $parameters): Output\Template;
}