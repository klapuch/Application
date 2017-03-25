<?php
declare(strict_types = 1);
namespace Klapuch\Application;

final class SessionExtension implements Extension {
	private const TIMER = '_timer',
		ELAPSE = 20;
	private $settings;

	public function __construct(array $settings) {
		$this->settings = $settings;
	}

	public function improve(): void {
		session_start($this->settings);
		if (isset($_SESSION[self::TIMER]) && (time() - $_SESSION[self::TIMER]) > self::ELAPSE) {
			$_SESSION[self::TIMER] = time();
			session_regenerate_id(true);
		} elseif (!isset($_SESSION[self::TIMER]))
			$_SESSION[self::TIMER] = time();
	}
}