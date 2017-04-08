<?php
declare(strict_types = 1);
namespace Klapuch\Application;

use Klapuch\Csrf;
use Klapuch\Ini;
use Klapuch\Log;
use Klapuch\Output;
use Klapuch\UI;
use Klapuch\Uri;

abstract class Page {
	/** @var mixed[] */
	protected $configuration;
	/** @var \Klapuch\Uri\Uri */
	protected $url;
	/** @var \Klapuch\Log\Logs */
	protected $logs;
	/** @var \Klapuch\Csrf\Csrf */
	protected $csrf;

	public function __construct(
		Uri\Uri $url,
		Log\Logs $logs,
		Ini\Ini $configuration
	) {
		$this->url = $url;
		$this->logs = $logs;
		$this->configuration = $configuration->read();
		$this->csrf = new Csrf\StoredCsrf($_SESSION, $_POST, $_GET);
	}

	public function startup(): void {
	}

	/**
	 * Flash message to the page
	 * @param string $content
	 * @param string $type
	 * @return void
	 */
	final protected function flashMessage(string $content, string $type): void {
		(new UI\PersistentFlashMessage($_SESSION))->flash($content, $type);
	}

	/**
	 * Redirect relatively to the given url
	 * @param string $url
	 * @param int $code
	 * @return void
	 */
	final protected function redirect(string $url, int $code = 200): void {
		header(sprintf('Location: %s/%s', $this->url->reference(), $url), true, $code);
		exit;
	}

	/**
	 * Protect against CSRF
	 * @throws \Exception
	 */
	final protected function protect(): void {
		if ($this->csrf->abused())
			throw new \Exception('Timeout');
	}

	/**
	 * Log the exception
	 * @param \Throwable $ex
	 * @return void
	 */
	final protected function log(\Throwable $ex): void {
		$this->logs->put(
			new Log\PrettyLog(
				$ex,
				new Log\PrettySeverity(
					new Log\JustifiedSeverity(Log\Severity::ERROR)
				)
			)
		);
	}

	abstract public function render(array $parameters): Output\Format;

	abstract public function template(array $parameters): array;
}