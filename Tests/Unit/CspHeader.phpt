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

final class CspHeader extends Tester\TestCase {
	public function testNoHeaderOnNoPassedDirectives() {
		Assert::same('', (string) new Application\CspHeader([]));
	}

	/**
	 * @dataProvider directives
	 */
	public function testFormattedDirectives(string $header, array $directives) {
		Assert::same(
			'Content-Security-Policy: ' . $header,
			(string) new Application\CspHeader($directives)
		);
	}

	protected function directives(): array {
		// expected => given
		return [
			["default-src 'self'", ['default-src' => "'self'"]],
			["script-src 'self' 'unsafe-inline'", ['script-src' => "'self' 'unsafe-inline'"]],
			["default-src 'self'; child-src 'none'", ['default-src' => "'self'", 'child-src' => "'none'"]],
		];
	}

	public function testGeneratingNonceJustOnce() {
		$csp = new Application\CspHeader([]);
		Assert::true(strlen($csp->nonce()) === 24);
		Assert::same($csp->nonce(), $csp->nonce());
	}

	public function testPassingNonce() {
		$csp = new Application\CspHeader(['sctipt-src' => 'nonce']);
		Assert::contains('nonce-' . $csp->nonce(), (string) $csp);
	}
}


(new CspHeader())->run();