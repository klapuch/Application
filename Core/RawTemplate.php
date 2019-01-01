<?php
declare(strict_types = 1);

namespace Klapuch\Application;

use Klapuch\Internal;
use Klapuch\Output;

/**
 * Raw template
 */
final class RawTemplate implements Output\Template {
	/** @var \Klapuch\Application\Response */
	private $response;

	public function __construct(Response $response) {
		$this->response = $response;
	}

	public function render(array $variables = []): string {
		http_response_code($this->response->status());
		(new Internal\HeaderExtension((new HttpResponse($this->response))->headers()))->improve();
		return $this->response->body()->serialization();
	}
}
