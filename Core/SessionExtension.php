<?php
declare(strict_types = 1);
namespace Klapuch\Application;

final class SessionExtension implements Extension {
	private const TIMER = 'timer',
		ELAPSE = 20;
	private $sessions;
	private $settings;

	public function __construct(array &$sessions, array $settings) {
		$this->sessions = &$sessions;
		$this->settings = $settings;
	}

	public function improve(): void {
		session_start($this->settings);
		if (isset($this->sessions[self::TIMER]) && (time() - $this->sessions[self::TIMER]) > self::ELAPSE) {
			$this->sessions[self::TIMER] = time();
			session_regenerate_id(true);
		} elseif (!isset($this->sessions[self::TIMER]))
			$this->sessions[self::TIMER] = time();
	}
}