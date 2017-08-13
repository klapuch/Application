<?php
declare(strict_types = 1);
namespace Klapuch\Application;

final class SuitedPage extends Page {
	private const FIELD = 'content-type';
	private const WEB = ['text/html', 'application/x-www-form-urlencoded'];

	public function __toString(): string {
		if (in_array($this->type($this->headers()), self::WEB)) {
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
		if (array_key_exists(self::FIELD, $headers))
			return $headers[self::FIELD];
		return self::WEB[0];
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