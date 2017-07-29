<?php
declare(strict_types = 1);
namespace Klapuch\Application;

use Klapuch\Ini;
use Klapuch\Internal;
use Klapuch\Log;
use Klapuch\Routing;
use Klapuch\Uri;

final class HtmlPage implements Page {
	private $configuration;
	private $logs;
	private $routes;
	private $uri;

	public function __construct(
		Ini\Source $configuration,
		Log\Logs $logs,
		Routing\Routes $routes,
		Uri\Uri $uri
	) {
		$this->configuration = $configuration;
		$this->logs = $logs;
		$this->routes = $routes;
		$this->uri = $uri;
	}

	private function target(Routing\Route $route): Request {
		$class = $route->resource();
		return new $class($this->uri, $this->logs, $this->configuration);
	}

	public function __toString(): string {
		try {
			$configuration = $this->configuration->read();
			$csp = new Internal\CspHeader($configuration['CSP']);
			(new Internal\CombinedExtension(
				new Internal\InternationalExtension('Europe/Prague'),
				new Internal\IniSetExtension($configuration['INI']),
				new Internal\SessionExtension($configuration['SESSIONS']),
				new Internal\CookieExtension($configuration['PROPRIETARY_SESSIONS']),
				new Internal\HeaderExtension($configuration['HEADERS']),
				new Internal\RawHeaderExtension([$csp])
			))->improve();
			$route = $this->routes->match($this->uri);
			return $this->target(
				$route
			)->response(
				$_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $route->parameters()
			)->render(
				[
					'base_url' => $this->uri->reference(),
					'nonce' => $csp->nonce(),
				]
			);
		} catch (\Throwable $ex) {
			if (isset($configuration['RUNTIME']['debug']) && $configuration['RUNTIME']['debug'] === true)
				throw $ex;
			$this->logs->put(
				new Log\PrettyLog(
					$ex,
					new Log\PrettySeverity(
						new Log\JustifiedSeverity(Log\Severity::ERROR)
					)
				)
			);
			http_response_code(500);
			header(sprintf('Location: %s/error', $this->uri->reference()));
			exit;
		}
	}
}