<?php
declare(strict_types = 1);
namespace Klapuch\Application;

use Klapuch\Output;

/**
 * Fake
 */
final class FakeResponse implements Response {
	private $body;
	private $headers;

	public function __construct(Output\Format $body = null, array $headers = null) {
		$this->body = $body;
		$this->headers = $headers;
	}

	public function body(): Output\Format {
		return $this->body;
	}

	public function headers(): array {
		return $this->headers;
	}
}