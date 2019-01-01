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
				return (string) file_get_contents('php://input');
			}

			/**
			 * @param mixed $tag
			 * @param mixed|null $content
			 * @return \Klapuch\Output\Format
			 */
			public function with($tag, $content = null): Output\Format {
				throw new \Exception('With is not allowed.');
			}

			/**
			 * @param mixed $tag
			 * @param callable $adjustment
			 * @return \Klapuch\Output\Format
			 */
			public function adjusted($tag, callable $adjustment): Output\Format {
				throw new \Exception('Adjusting is not allowed.');
			}
		};
	}

	public function headers(): array {
		static $prefix = 'HTTP_';
		$headers = array_filter(
			$_SERVER,
			static function(string $name) use ($prefix): bool {
				return (bool) preg_match(sprintf('~^%s.+~', $prefix), $name);
			},
			ARRAY_FILTER_USE_KEY
		);
		return (array) array_combine(
			array_map(
				static function(string $field): string {
					return (new Header($field))->field();
				},
				array_map(
					static function(string $field) use ($prefix): string {
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
}
