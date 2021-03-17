<?php

namespace CanadaSatellite\SimpleAmqp\Api;

class AmqpClientFactory
{
	private $config;

	public function __construct(
		\CanadaSatellite\SimpleAmqp\Config\Config $config
	) {
		$this->config = $config;
	}

	public function getAmqpClient($queueName)
	{
		return new AmqpClient($this->config->getConnectionString(), $queueName);
	}
}