<?php
declare(strict_types = 1);
namespace Klapuch\Application;

use Klapuch\Output;

/**
 * Request as it is - without modifications
 */
final class PlainRequest implements Request {
	public function body(): Output\Format {
		return new class implements Output\Format {
			public function serialization(): string {
				return file_get_contents('php://input');
			}

			public function with($tag, $content = null): Output\Format {
				throw new \Exception('With is not allowed.');
			}

			public function adjusted($tag, callable $adjustment): Output\Format {
				throw new \Exception('Adjusting is not allowed.');
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
		return array_combine(
			array_map(
				[$this, 'unify'],
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
				)
			),
			$headers
		);
	}

	// @codingStandardsIgnoreStart Used by array_map
	private function unify(string $field): string {
		return implode(
			'-',
			array_map(
				function(string $field): string {
					return ucfirst(strtolower($field));
				},
				explode('-', $field)
			)
		);
	}
	// @codingStandardsIgnoreEnd
}