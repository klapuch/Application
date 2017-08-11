<?php
declare(strict_types = 1);
namespace Klapuch\Application;

use Klapuch\Output;

interface View {
	/**
	 * @param array $input
	 * @return \Klapuch\Output\Template
	 */
	public function template(array $input): Output\Template;
}