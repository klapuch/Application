<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 * @httpCode any
 */
namespace Klapuch\Application\Unit;

use Klapuch\Application;
use Klapuch\Output;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class RawTemplate extends \Tester\TestCase {
	public function testRenderingWithinHeaders() {
		Assert::same(
			'<foo>FOO</foo>',
			(new Application\RawTemplate(
				new Application\FakeResponse(
					new Output\FakeFormat('<foo>FOO</foo>'),
					['test' => 'foo'],
					200
				)
			))->render()
		);
		Assert::same('test:foo', headers_list()[1]);
	}
}

(new RawTemplate())->run();