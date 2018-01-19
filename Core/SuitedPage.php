<?php
declare(strict_types = 1);
namespace Klapuch\Application;

use Klapuch\Configuration;
use Klapuch\Log;
use Klapuch\Output;
use Klapuch\Routing;
use Klapuch\Uri;

final class SuitedPage implements Output\Template {
	private $configuration;
	private $logs;
	private $routes;
	private $uri;

	public function __construct(
		Configuration\Source $configuration,
		Log\Logs $logs,
		Routing\Routes $routes,
		Uri\Uri $uri
	) {
		$this->configuration = $configuration;
		$this->logs = $logs;
		$this->routes = $routes;
		$this->uri = $uri;
	}

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
			$this->routes
		))->render($variables);
	}

	private function type(array $headers): string {
		if (array_key_exists(self::FIELD, $headers))
			return $headers[self::FIELD];
		return self::WEB[0];
	}
}