<?php

namespace CanadaSatellite\SimpleAmqp\Api;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class AmqpClient
{
	/**
	 * @var CanadaSatellite\SimpleAmqp\Config\ConnectionString
	 */
	private $connectionString;

	/**
	 * @var string
	 */
	private $queueName;

	/**
	 * @var AMQPStreamConnection
	 */
	private $connection;

	/**
	 * @var AMQPChannel
	 */
	private $channel;

	/**
	 * @param CanadaSatellite\SimpleAmqp\Config\ConnectionString $connectionString
	 * @param string $queueName
	 */
	public function __construct(
		$connectionString,
		$queueName
	) {
		$this->connectionString = $connectionString;
		$this->queueName = $queueName;
	}

    public function __destruct()
    {
        $this->closeConnection();
    }

	public function publish($message)
	{
		$channel = $this->getChannel();

		$message = new AMQPMessage(
			json_encode($message),
			array(
				'content_type' => 'application/json', 
				'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
			)
		);

		$channel->basic_publish($message, $this->queueName);
	}

	public function get()
	{
		$message = $this->peek();
		if ($message === null) {
			return null;
		}

		return $message;
	}

	public function ack($message)
	{
		$channel = $this->getChannel();
		$channel->basic_ack($message->delivery_info['delivery_tag']);
	}

	public function reject($message)
	{
		$channel = $this->getChannel();
		$channel->basic_reject($message->delivery_info['delivery_tag']);
	}

	public function createQueue()
	{
		$channel = $this->getChannel();
		
		$queue = $this->queueName;

		// Create non passive, durable non exclusive queue with no auto delete
		$channel->queue_declare($queue, false, true, false, false);
            
        // Create direct exchange, non passive, durable and with no auto delete
        $channel->exchange_declare($queue, 'direct', false, true, false);
        
        // Bind queue to exchange
        $channel->queue_bind($queue, $queue);
	}

	private function peek()
	{
        $channel = $this->getChannel();
        
        // TODO: Handle 'AMQPProtocolChannelException'

        $message = $channel->basic_get($this->queueName);
        if ($message === null) {
            return null;
        }

        return $message;
	}

	private function getChannel()
	{
		if ($this->connection === null) {
			$connectionString = $this->connectionString;

			$this->connection = new AMQPStreamConnection(
                $connectionString->getHost(),
                $connectionString->getPort(),
                $connectionString->getUsername(),
                $connectionString->getPassword(),
                $connectionString->getVirtualhost()
            );
		}

		if ($this->channel === null) {
			$this->channel = $this->connection->channel();
		}

		return $this->channel;
	}

	private function closeConnection()
	{
		if ($this->channel !== null) {
			$this->channel->close();
			$this->channel = null;
		}
		if ($this->connection !== null) {
			$this->connection->close();
			$this->connection = null;
		}
	}
}
