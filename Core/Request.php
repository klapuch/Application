<?php
declare(strict_types = 1);
namespace Klapuch\Application;

interface Request {
	/**
	 * Body itself
	 * @return \Klapuch\Application\Body
	 */
	public function body(): Body;

	/**
	 * Headers serialized to array
	 * @return string[]
	 */
	public function headers(): array;
}