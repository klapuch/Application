<?php
declare(strict_types = 1);
namespace Klapuch\Application;

final class SessionExtension implements Extension {
	private const TIMER = '_timer',
		DEFAULT_BREAK = 20;
	private $settings;
	private $break;

	public function __construct(array $settings, int $break = self::DEFAULT_BREAK) {
		$this->settings = $settings;
		$this->break = $break;
	}

	public function improve(): void {
		if (session_status() === PHP_SESSION_NONE)
			session_start($this->settings);
		if (isset($_SESSION[self::TIMER]) && (time() - $_SESSION[self::TIMER]) > $this->break) {
			$_SESSION[self::TIMER] = time();
			session_regenerate_id(true);
		} elseif (!isset($_SESSION[self::TIMER]))
			$_SESSION[self::TIMER] = time();
	}
}