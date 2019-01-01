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
final class EmptyResponseTest extends \Tester\TestCase {
	public function testNoResponseCode(): void {
		Assert::same(204, (new Application\EmptyResponse())->status());
	}
}

(new EmptyResponseTest())->run();
