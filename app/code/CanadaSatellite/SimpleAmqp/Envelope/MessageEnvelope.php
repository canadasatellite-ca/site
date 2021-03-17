<?php

namespace CanadaSatellite\SimpleAmqp\Envelope;

class MessageEnvelope
{
	private $amqpMessage;
	private $body;

	public function __construct($amqpMessage, $body)
	{
		$this->amqpMessage = $amqpMessage;
		$this->body = $body;
	}

	public function getAmqpMessage()
	{
		return $this->amqpMessage;
	}

	public function getBody()
	{
		return $this->body;
	}
}
