<?php
declare(strict_types = 1);

namespace Klapuch\Application;

use Klapuch\Output;

final class FakeRequest implements Request {
	/** @var \Klapuch\Output\Format|null */
	private $body;

	/** @var mixed[]|null */
	private $headers;

	public function __construct(?Output\Format $body = null, ?array $headers = null) {
		$this->body = $body;
		$this->headers = $headers;
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
}
