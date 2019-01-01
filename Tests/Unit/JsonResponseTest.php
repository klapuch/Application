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

/**
 * @testCase
 */
final class JsonResponseTest extends \Tester\TestCase {
	public function testAddingJsonHeader(): void {
		Assert::same(
			['Content-Type' => 'application/json; charset=utf8'],
			(new Application\JsonResponse(new Application\FakeResponse(null, [])))->headers()
		);
	}

	public function testJsonHeaderWithPriority(): void {
		Assert::same(
			['Content-Type' => 'application/json; charset=utf8'],
			(new Application\JsonResponse(
				new Application\FakeResponse(null, ['Content-Type' => 'xx'])
			))->headers()
		);
	}

	public function testAddingOtherHeaders(): void {
		Assert::same(
			['Content-Type' => 'application/json; charset=utf8', 'Accept' => 'text/xml'],
			(new Application\JsonResponse(
				new Application\FakeResponse(null, ['Accept' => 'text/xml'])
			))->headers()
		);
	}
}

(new JsonResponseTest())->run();
