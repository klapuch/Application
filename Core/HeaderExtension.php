<?php
declare(strict_types = 1);
namespace Klapuch\Application;

final class HeaderExtension implements Extension {
	private $headers;

	public function __construct(array $headers) {
		$this->headers = $headers;
	}

	public function improve(): void {
		foreach ($this->headers as $field => $value)
			header(sprintf('%s:%s', $field, $value));
	}
}