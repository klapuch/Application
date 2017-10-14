<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Klapuch\Application\Unit;

use Klapuch\Application;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class HttpResponse extends \Tester\TestCase {
	public function testHeadersWithFirstUpperAndRestLower() {
		Assert::same(
			['Accept' => 'text/plain'],
			(new Application\HttpResponse(
				new Application\FakeResponse(null, ['aCCept' => 'text/plain'])
			))->headers()
		);
	}

	public function testHeadersAsTwoSeparateWords() {
		Assert::same(
			['Content-Type' => 'text/html'],
			(new Application\HttpResponse(
				new Application\FakeResponse(null, ['content-tYpE' => 'text/html'])
			))->headers()
		);
	}
}

(new HttpResponse())->run();