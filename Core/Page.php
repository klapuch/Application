<?php
declare(strict_types = 1);
namespace Klapuch\Application;

use Klapuch\Ini;
use Klapuch\Log;
use Klapuch\Routing;
use Klapuch\Uri;

abstract class Page {
	protected $configuration;
	protected $logs;
	protected $routes;
	protected $uri;

	final public function __construct(
		Ini\Source $configuration,
		Log\Logs $logs,
		Routing\Routes $routes,
		Uri\Uri $uri
	) {
		$this->configuration = $configuration;
		$this->logs = $logs;
		$this->routes = $routes;
		$this->uri = $uri;
	}

	final protected function target(Routing\Route $route): Request {
		$class = $route->resource();
		return new $class($this->uri, $this->logs, $this->configuration);
	}

	/**
	 * Content of the page
	 * @return string
	 */
	abstract public function __toString(): string;
}