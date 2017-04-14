<?php
declare(strict_types = 1);
namespace Klapuch\Application;

interface Request {
	/**
	 * Response from the request
	 * @param array $parameters
	 * @return \Klapuch\Application\Response
	 */
	public function response(array $parameters): Response;
}