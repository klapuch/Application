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

final class Header extends \Tester\TestCase {
	public function testFieldWithFirstUpperRestLower() {
		Assert::same('Accept', (new Application\Header('aCCept'))->field());
	}

	public function testTwoWordHeader() {
		Assert::same('Content-Type', (new Application\Header('content-type'))->field());
	}

	public function testExceptions() {
		Assert::same('ETag', (new Application\Header('etag'))->field());
	}
}

(new Header())->run();