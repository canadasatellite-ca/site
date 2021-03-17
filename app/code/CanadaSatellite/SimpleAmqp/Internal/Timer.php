<?php

namespace CanadaSatellite\SimpleAmqp\Internal;

class Timer
{
	/**
	 * @var int
	 */
	private $timeout;

	/**
	 * @var float
	 */
	private $startedAt;

	/**
	 * @param int $timeout In milliseconds.
	 */
	public function __construct($timeout)
	{
		$this->timeout = $timeout;
	}

	public function start()
	{
		$this->startedAt = $this->getNowInMicroseconds();
	}

	public function restart()
	{
		$this->start();
	}

	public function isExpired()
	{
		if (!$this->isStarted()) {
			throw new \LogicException("Timer not started.");
		}

		$now = $this->getNowInMicroseconds();
		$delta = $now - $this->startedAt;
		$deltaInMilliseconds = $this->convertMicrosecondsToMilliseconds($delta);

		return $deltaInMilliseconds > $this->timeout;
	}

	private function getNowInMicroseconds()
	{
		return microtime(true);
	}

	private function convertMicrosecondsToMilliseconds($microseconds)
	{
		return $microseconds * 1000;
	}

	private function isStarted()
	{
		return isset($this->startedAt);
	}
}