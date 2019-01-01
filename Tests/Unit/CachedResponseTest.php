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
final class CachedResponseTest extends \Tester\TestCase {
	use Application\TestCase\Mockery;

	public function testMultipleCallsWithSingleExecution(): void {
		$origin = $this->mock(Application\Response::class);
		$origin->shouldReceive('body')->once();
		$origin->shouldReceive('headers')->once();
		$origin->shouldReceive('status')->once();
		$response = new Application\CachedResponse($origin);
		Assert::equal($response->body(), $response->body());
		Assert::equal($response->headers(), $response->headers());
		Assert::equal($response->status(), $response->status());
	}
}

(new CachedResponseTest())->run();
