<?php
declare(strict_types = 1);
namespace Klapuch\Application;

use Klapuch\Configuration;
use Klapuch\Internal;
use Klapuch\Log;
use Klapuch\Output;
use Klapuch\Routing;

final class RawPage implements Output\Template {
	private $configuration;
	private $logs;
	private $routes;

	public function __construct(
		Configuration\Source $configuration,
		Log\Logs $logs,
		Routing\Routes $routes
	) {
		$this->configuration = $configuration;
		$this->logs = $logs;
		$this->routes = $routes;
	}

	public function render(array $variables = []): string {
		try {
			$configuration = $this->configuration->read();
			(new Internal\CombinedExtension(
				new Internal\InternationalExtension('Europe/Prague'),
				new Internal\IniSetExtension($configuration['INI'] ?? []),
				new Internal\HeaderExtension($configuration['HEADERS'] ?? [])
			))->improve();
			return current($this->routes->matches())->render($variables);
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