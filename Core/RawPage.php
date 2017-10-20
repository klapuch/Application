<?php
declare(strict_types = 1);
namespace Klapuch\Application;

use Klapuch\Internal;
use Klapuch\Log;

final class RawPage extends Page {
	public function render(array $variables = []): string {
		try {
			$configuration = $this->configuration->read();
			(new Internal\CombinedExtension(
				new Internal\InternationalExtension('Europe/Prague'),
				new Internal\IniSetExtension($configuration['INI']),
				new Internal\HeaderExtension($configuration['HEADERS'])
			))->improve();
			return current($this->routes->matches())->render($variables);
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
			exit;
		}
	}
}