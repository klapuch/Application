<?php
declare(strict_types = 1);

namespace Klapuch\Application;

use Klapuch\Application;
use Klapuch\Output;

final class EmptyResponse implements Application\Response {
	public function body(): Output\Format {
		return new Output\EmptyFormat();
	}

	public function headers(): array {
		return [
			'Content-Type' => 'text/plain',
			'Content-Length' => 0,
		];
	}

	public function status(): int {
		return 204;
	}
}
