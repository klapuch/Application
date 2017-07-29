<?php
declare(strict_types = 1);
namespace Klapuch\Application;

use Klapuch\Internal;
use Klapuch\Output;

/**
 * Template form common web application
 */
final class HtmlTemplate implements Output\Template {
	private const HEADERS = [
		'Content-Type' => 'text/html; charset=utf8;',
	];
	private $response;
	private $xsl;
	private $exit;

	public function __construct(Response $response, string $xsl = '', bool $exit = true) {
		$this->response = $response;
		$this->xsl = $xsl;
		$this->exit = $exit;
	}

	public function render(array $variables = []): string {
		$this->sendHeaders($this->response->headers());
		return (new Output\XsltTemplate(
			$this->xsl,
			$this->response->body()
		))->render($variables);
	}

	/**
	 * Send the headers
	 */
	private function sendHeaders(array $headers): void {
		$headers = self::HEADERS + $headers;
		(new Internal\HeaderExtension($headers))->improve();
		if (array_key_exists('location', array_change_key_case($headers, CASE_LOWER))) {
			if ($this->exit === true) {
				exit;
			} else {
				echo 'Exited';
			}
		}
	}
}