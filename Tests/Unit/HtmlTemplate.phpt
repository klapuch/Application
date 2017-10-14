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
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class HtmlTemplate extends \Tester\TestCase {
	public function testRenderingWithinPassedHeaders() {
		(new Application\HtmlTemplate(
			new Application\FakeResponse(
				new Output\FakeFormat('<foo>FOO</foo>'),
				['test' => 'foo'],
				200
			),
			Tester\FileMock::create(
				'<?xml version="1.0" encoding="utf-8"?>
				<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"></xsl:stylesheet>', 'xsl')
		))->render([]);
		Assert::same('Content-Type:text/html; charset=utf8;', headers_list()[1]);
		Assert::same('Test:foo', headers_list()[2]);
	}

	public function testPassingRendering() {
		Assert::same(
'<?xml version="1.0"?>
FOO
',
			(new Application\HtmlTemplate(
				new Application\FakeResponse(
					new Output\FakeFormat('<foo>FOO</foo>'),
					['test' => 'foo'],
					200
				),
				Tester\FileMock::create(
					'<?xml version="1.0" encoding="utf-8"?>
					<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
					</xsl:stylesheet>', 'xsl')
			))->render([])
		);
	}

	public function testDenyingOverwritingDefaultHeaders() {
		(new Application\HtmlTemplate(
			new Application\FakeResponse(
				new Output\FakeFormat('<foo>FOO</foo>'),
				['Content-Type' => 'foo'],
				200
			),
			Tester\FileMock::create(
				'<?xml version="1.0" encoding="utf-8"?>
				<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"></xsl:stylesheet>', 'xsl')
		))->render([]);
		Assert::count(2, headers_list());
		Assert::same('Content-Type:text/html; charset=utf8;', headers_list()[1]);
	}

	public function testExitingAfterRedirect() {
		ob_start();
		(new Application\HtmlTemplate(
			new Application\FakeResponse(
				new Output\FakeFormat('<foo>FOO</foo>'),
				['Location' => 'https://www.google.com'],
				200
			),
			Tester\FileMock::create(
				'<?xml version="1.0" encoding="utf-8"?>
				<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"></xsl:stylesheet>', 'xsl'),
			$exit = false
		))->render([]);
		Assert::same('Exited', ob_get_clean());
		Assert::same(302, http_response_code());
		Assert::same('Location:https://www.google.com', headers_list()[2]);
	}

	public function testAllowingAnyLocationCase() {
		ob_start();
		(new Application\HtmlTemplate(
			new Application\FakeResponse(
				new Output\FakeFormat('<foo>FOO</foo>'),
				['lOcAtion' => 'https://www.google.com'],
				200
			),
			Tester\FileMock::create(
				'<?xml version="1.0" encoding="utf-8"?>
				<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"></xsl:stylesheet>', 'xsl'),
			$exit = false
		))->render([]);
		Assert::same('Exited', ob_get_clean());
	}
}

(new HtmlTemplate())->run();