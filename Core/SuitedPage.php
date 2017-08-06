<?php
declare(strict_types = 1);
namespace Klapuch\Application;

final class SuitedPage extends Page {
	private const FIELD = 'content-type';
	private const DEFAULT_TYPE = 'html';

	public function __toString(): string {
		if ($this->type($this->headers()) === self::DEFAULT_TYPE) {
			return (string) new HtmlPage(
				$this->configuration,
				$this->logs,
				$this->routes,
				$this->uri
			);
		}
		return (string) new RawPage(
			$this->configuration,
			$this->logs,
			$this->routes,
			$this->uri
		);
	}

	private function type(array $headers): string {
		if (array_key_exists(self::FIELD, $headers)) {
			preg_match('~^\w+/(\w+)~', $headers[self::FIELD], $matches);
			return $matches[1] ?? self::DEFAULT_TYPE;
		}
		return self::DEFAULT_TYPE;
	}

	private function headers(): array {
		$headers = [];
		foreach ($_SERVER as $name => $value) {
			if (substr($name, 0, 5) === 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return array_change_key_case($headers, CASE_LOWER);
	}
}