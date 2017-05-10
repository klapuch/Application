<?php
declare(strict_types = 1);
namespace Klapuch\Application;

use Klapuch\Ini;
use Klapuch\Internal;
use Klapuch\Log;
use Klapuch\Output;
use Klapuch\Routing;
use Klapuch\Uri;

final class HtmlPage implements Page {
	private const HEADERS = [
		'Content-Type' => 'text/html; charset=utf8;',
	];
	private $configuration;
	private $logs;
	private $routes;
	private $uri;
	private $method;
	private $request;

	public function __construct(
		Ini\Source $configuration,
		Log\Logs $logs,
		Routing\Routes $routes,
		Uri\Uri $uri,
		string $method,
		array $request
	) {
		$this->configuration = $configuration;
		$this->logs = $logs;
		$this->routes = $routes;
		$this->uri = $uri;
		$this->method = $method;
		$this->request = $request;
	}

	private function target(Routing\Route $route, array $configuration): Request {
		$class = (new Routing\MappedRoute(
			$route,
			$configuration['MAPPING']['namespace'],
			$configuration['MAPPING']['resolution']
		))->resource();
		return new $class($this->uri, $this->logs, $this->configuration);
	}

	private function content(Routing\Route $route, Request $target, array $configuration, Internal\CspHeader $csp): string {
		$xsl = sprintf(
			'%s/../%s/templates/%s.xsl',
			$configuration['PATHS']['templates'],
			$route->resource(),
			$route->action()
		);
		$this->sendHeaders($target->response($route->parameters())->headers());
		return (new Output\XsltTemplate(
			$xsl,
			$target->response($route->parameters())->body()
		))->render(['base_url' => $this->uri->reference(), 'nonce' => $csp->nonce()]);
	}

	private function interact(Routing\Route $route, Request $target): void {
		$submit = 'submit' . $route->action();
		if ($this->method === 'POST' && method_exists($target, $submit)) {
			$response = $target->$submit($this->request, $route->parameters());
			$this->sendHeaders($response->headers());
		}
	}

	private function sendHeaders(array $headers): void {
		$headers = self::HEADERS + $headers;
		(new Internal\HeaderExtension($headers))->improve();
		if (array_key_exists('location', array_change_key_case($headers, CASE_LOWER)))
			exit;
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
			$target = $this->target($route, $configuration);
			$this->interact($route, $target);
			return $this->content($route, $target, $configuration, $csp);
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