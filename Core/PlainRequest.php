<?php
declare(strict_types = 1);
namespace Klapuch\Application;

/**
 * Request as it is - without modifications
 */
final class PlainRequest implements Request {
	public function body(): Body {
		return new class implements Body {
			public function data(): string {
				return file_get_contents('php://input');
			}
		};
	}

	public function headers(): array {
		static $prefix = 'HTTP_';
		$headers = array_filter(
			$_SERVER,
			function(string $name) use ($prefix): bool {
				return (bool) preg_match(sprintf('~^%s.+~', $prefix), $name);
			},
			ARRAY_FILTER_USE_KEY
		);
		return array_change_key_case(
			array_combine(
				array_map(
					function(string $field) use ($prefix): string {
						return str_replace(
							' ',
							'-',
							str_replace(
								'_',
								' ',
								substr($field, strlen($prefix))
							)
						);
					},
					array_keys($headers)
				),
				$headers
			),
			CASE_LOWER
		);
	}
}