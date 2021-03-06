<?php
declare(strict_types = 1);

namespace Klapuch\Application;

use Klapuch\Application;
use Klapuch\Output;

final class PlainResponse implements Application\Response {
	private const OK = 200;

	/** @var \Klapuch\Output\Format */
	private $format;

	/** @var mixed[] */
	private $headers;

	/** @var int */
	private $status;

	public function __construct(
		Output\Format $format,
		array $headers = [],
		int $status = self::OK
	) {
		$this->format = $format;
		$this->headers = $headers;
		$this->status = $status;
	}

	public function body(): Output\Format {
		return $this->format;
	}

	public function headers(): array {
		return $this->headers;
	}

	public function status(): int {
		return $this->status;
	}
}
