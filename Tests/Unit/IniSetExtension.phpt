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

final class IniSetExtension extends Tester\TestCase {
	public function testConvertingIntegerToString() {
		Assert::same('1', ini_get('display_errors'));
		(new Application\IniSetExtension(['display_errors' => 0]))->improve();
		Assert::same('0', ini_get('display_errors'));
	}
}


(new IniSetExtension())->run();