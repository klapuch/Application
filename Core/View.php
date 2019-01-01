<?php
declare(strict_types = 1);

namespace Klapuch\Application;

interface View {
	/**
	 * @param array $input
	 * @return \Klapuch\Application\Response
	 */
	public function response(array $input): Response;
}
