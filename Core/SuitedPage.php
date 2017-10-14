<?php
declare(strict_types = 1);
namespace Klapuch\Application;

final class SuitedPage extends Page {
	private const FIELD = 'Content-Type';
	private const WEB = ['text/html', 'application/x-www-form-urlencoded'];

	public function render(array $variables = []): string {
		if (in_array($this->type((new PlainRequest())->headers()), self::WEB)) {
			return (new HtmlPage(
				$this->configuration,
				$this->logs,
				$this->routes,
				$this->uri
			))->render($variables);
		}
		return (new RawPage(
			$this->configuration,
			$this->logs,
			$this->routes,
			$this->uri
		))->render($variables);
	}

	private function type(array $headers): string {
		if (array_key_exists(self::FIELD, $headers))
			return $headers[self::FIELD];
		return self::WEB[0];
	}
}