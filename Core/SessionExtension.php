<?php
declare(strict_types = 1);
namespace Klapuch\Application;

final class SessionExtension implements Extension {
	private const TIMER = '_timer',
		DEFAULT_BREAK = 20;
	private const PROPRIETARIES = ['SameSite'];
	private $settings;
	private $break;

	public function __construct(array $settings, int $break = self::DEFAULT_BREAK) {
		$this->settings = $settings;
		$this->break = $break;
	}

	public function improve(): void {
		if (session_status() === PHP_SESSION_NONE)
			session_start($this->native($this->settings));
		header($this->raw($this->settings));
		if ($this->elapsed($this->break))
			session_regenerate_id(true);
		$_SESSION[self::TIMER] = time();
	}

	private function elapsed(int $break): bool {
		return isset($_SESSION[self::TIMER])
		&& (time() - $_SESSION[self::TIMER]) > $break;
	}

	/**
	 * Just the native and supported php setting
	 * @param array $settings
	 * @return array
	 */
	private function native(array $settings): array {
		return array_diff_ukey(
			$settings,
			array_flip(self::PROPRIETARIES),
			'strcasecmp'
		);
	}

	/**
	 * The raw cookie header
	 * @param array $settings
	 * @return string
	 */
	private function raw(array $settings): string {
		$matches = array_intersect_ukey(
			array_flip(self::PROPRIETARIES),
			$this->settings,
			'strcasecmp'
		);
		$headers = array_combine(
			array_flip($matches),
			array_intersect_ukey($this->settings, $matches, 'strcasecmp')
		);
		return sprintf(
			'%s; %s',
			current(preg_grep('~^Set-Cookie: ~', headers_list())),
			implode(
				';',
				array_map(
					function(string $field, string $value): string {
						return sprintf('%s=%s', $field, $value);
					},
					array_keys($headers),
					$headers
				)
			)
		);
	}
}