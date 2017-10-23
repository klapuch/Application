<?php
declare(strict_types = 1);
namespace Klapuch\Application;

use Klapuch\Ini;
use Klapuch\Internal;
use Klapuch\Log;
use Klapuch\Output;
use Klapuch\Routing;
use Klapuch\Uri;

final class HtmlPage implements Output\Template {
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

	public function render(array $variables = []): string {
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
			return current($this->routes->matches())->render(
				[
					'base_url' => $this->uri->reference(),
					'nonce' => $csp->nonce(),
				] + $variables
			);
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
			exit;
		}
	}
}