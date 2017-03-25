<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Klapuch\Application\Unit;

use Klapuch\Application;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class InternationalExtension extends Tester\TestCase {
	public function testSettingAllowedTimezone() {
		Assert::same('Europe/Prague', date_default_timezone_get());
		(new Application\InternationalExtension('Europe/Berlin'))->improve();
		Assert::same('Europe/Berlin', date_default_timezone_get());
	}

	/**
	 * @throws \InvalidArgumentException Timezone "Foo" is invalid
	 */
	public function testThrowingOnUnknownTimezone() {
		(new Application\InternationalExtension('Foo'))->improve();
	}
}


(new InternationalExtension())->run();