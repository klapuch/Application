<?php
declare(strict_types = 1);
namespace Klapuch\Application;

use Klapuch\Application;
use Klapuch\Ini;
use Klapuch\Log;
use Klapuch\Output;
use Klapuch\Routing;
use Klapuch\Uri;

final class Page {
	private $configuration;
	private $logs;
	private $routes;
	private $uri;
	private $method;
	private $request;

	public function __construct(
		Ini\Ini $configuration,
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

	private function content(Routing\Route $route, Request $target, array $configuration, CspHeader $csp): string {
		$xsl = sprintf(
			'%s/../%s/templates/%s.xsl',
			$configuration['PATHS']['templates'],
			$route->resource(),
			$route->action()
		);
		return (new Output\XsltTemplate(
			$xsl,
			$target->response($route->parameters())->body()
		))->render(['base_url' => $this->uri->reference(), 'nonce' => $csp->nonce()]);
	}

	private function interact(Routing\Route $route, Request $target): void {
		$submit = 'submit' . $route->action();
		if ($this->method === 'POST' && method_exists($target, $submit))
			$target->$submit($this->request, $route->parameters());
	}

	public function __toString(): string {
		try {
			$configuration = $this->configuration->read();
			$csp = new CspHeader($configuration['CSP']);
			(new Application\CombinedExtension(
				new Application\InternationalExtension('Europe/Prague'),
				new Application\IniSetExtension($configuration['INI']),
				new Application\SessionExtension($configuration['SESSIONS']),
				new Application\HeaderExtension($configuration['HEADERS']),
				new Application\RawHeaderExtension([$csp])
			))->improve();
			$route = $this->routes->match($this->uri);
			$target = $this->target($route, $configuration);
			$this->interact($route, $target);
			return $this->content($route, $target, $configuration, $csp);
		} catch (\Throwable $ex) {
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