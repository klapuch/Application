<?php
declare(strict_types = 1);
namespace Klapuch\Application;

use Klapuch\Application;
use Klapuch\Output;

/**
 * Well formatted for HTTP
 */
final class HttpResponse implements Application\Response {
	private $origin;

	public function __construct(Application\Response $origin) {
		$this->origin = $origin;
	}

	public function body(): Output\Format {
		return $this->origin->body();
	}

	public function headers(): array {
		return array_combine(
			array_map(
				function(string $field): string {
					return (new Header($field))->field();
				},
				array_keys($this->origin->headers())
			),
			$this->origin->headers()
		);
	}

	public function status(): int {
		return $this->origin->status();
	}
}