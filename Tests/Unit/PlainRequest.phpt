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

final class PlainRequest extends \Tester\TestCase {
	public function testNoHeadersWithoutError() {
		Assert::same([], (new Application\PlainRequest())->headers());
	}

	public function testTakingHttpHeadersToSingleCaseFormat() {
		$_SERVER['HTTP_ACCEPT'] = 'abc';
		$_SERVER['HTTP_HoST'] = 'def';
		$_SERVER['HTTP_content_TYPE'] = 'ghi';
		$_SERVER['FOO_BAR'] = 'foo';
		Assert::same(
			['Accept' => 'abc', 'Host' => 'def', 'Content-Type' => 'ghi'],
			(new Application\PlainRequest())->headers()
		);
	}
}

(new PlainRequest())->run();