<?php
declare(strict_types = 1);

namespace Klapuch\Application;

/**
 * Well formatted header suited for HTTP request/response
 */
final class Header {
	private const EXCEPTIONS = [
		'Etag' => 'ETag',
	];

	/** @var string */
	private $field;

	public function __construct(string $field) {
		$this->field = $field;
	}

	public function field(): string {
		$field = implode(
			'-',
			array_map(
				static function(string $field): string {
					return ucfirst(strtolower($field));
				},
				explode('-', $this->field)
			)
		);
		return self::EXCEPTIONS[$field] ?? $field;
	}
}
