<?php

namespace CanadaSatellite\SimpleAmqp;

class Publisher
{
	private $clientFactory;

	public function __construct(
		\CanadaSatellite\SimpleAmqp\Api\AmqpClientFactory $clientFactory
	) {
		$this->clientFactory = $clientFactory;
	}

	public function publish($queueName, $message)
	{
		$client = $this->clientFactory->getAmqpClient($queueName);
		$client->createQueue($queueName);
		
		$client->publish($message);
	}
}
