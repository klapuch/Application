<?php
declare(strict_types = 1);
namespace Klapuch\Application;

final class RawHeaderExtension implements Extension {
	private $headers;

	public function __construct(array $headers) {
		$this->headers = $headers;
	}

	public function improve(): void {
		foreach ($this->headers as $header)
			header($header);
	}
}