<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Klapuch\Application\Unit;

use Klapuch\Application;
use Klapuch\Output;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
final class FriendlyRequestTest extends \Tester\TestCase {
	public function testRethrowingWithSameCodeAndChainedPrevious(): void {
		$e = Assert::exception(static function () {
			(new Application\FriendlyRequest(
				new class implements Application\Request {
					public function body(): Output\Format {
						throw new \UnexpectedValueException('Foo', 402);
					}

					public function headers(): array {
					}
				},
				'This is cool'
			))->body()->serialization();
		}, \UnexpectedValueException::class, 'This is cool', 402);
		Assert::type(\UnexpectedValueException::class, $e->getPrevious());
		Assert::same('Foo', $e->getPrevious()->getMessage());
		Assert::same(402, $e->getPrevious()->getCode());
	}
}

(new FriendlyRequestTest())->run();
