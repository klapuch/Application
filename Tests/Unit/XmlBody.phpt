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

final class XmlBody extends \Tester\TestCase {
	public function testPassingWithValidXml() {
		Assert::same(
			'<?xml version="1.0" encoding="utf-8"?>
<foo/>
',
			(new Application\XmlBody(
				new class implements Application\Body {
					public function data(): string {
						return '<?xml version="1.0" encoding="utf-8"?><foo/>';
					}
				}
			))->data()
		);
	}

	public function testThrowingOnInvalidXml() {
		$ex = Assert::exception(function() {
			(new Application\XmlBody(
				new class implements Application\Body {
					public function data(): string {
						return 'abc';
					}
				}
			))->data();
		}, \UnexpectedValueException::class, 'XML document is not valid');
		Assert::same("Start tag expected, '<' not found", $ex->getPrevious()->getMessage());
	}

	public function testEnablingOldStateOfErrors() {
		$switch = libxml_use_internal_errors();
		Assert::exception(function() {
			(new Application\XmlBody(
				new class implements Application\Body {
					public function data(): string {
						return 'abc';
					}
				}
			))->data();
		}, \Throwable::class);
		Assert::same($switch, libxml_use_internal_errors());
	}
}

(new XmlBody())->run();