<?php
declare(strict_types = 1);

namespace Klapuch\Application;

use Klapuch\Output;

/**
 * Fake
 */
final class FakeResponse implements Response {
	/** @var \Klapuch\Output\Format|null */
	private $body;

	/** @var mixed[]|null */
	private $headers;

	/** @var int|null */
	private $status;

	public function __construct(?Output\Format $body = null, ?array $headers = null, ?int $status = null) {
		$this->body = $body;
		$this->headers = $headers;
		$this->status = $status;
	}

	public function body(): Output\Format {
		return $this->body;
	}

	/**
	 * @return string[]
	 */
	public function headers(): array {
		return $this->headers;
	}

	public function status(): int {
		return $this->status;
	}
}
