<?php

namespace CanadaSatellite\SimpleAmqp\Console;

use CanadaSatellite\AstIntegration\LogicProcessors\AstQueueProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use CanadaSatellite\SimpleAmqp\Api\AmqpClientFactory;
use CanadaSatellite\SimpleAmqp\Config\Config;
use CanadaSatellite\SimpleAmqp\Envelope\MessageEnvelope;
use CanadaSatellite\SimpleAmqp\Logger\LoggerFactory;
use CanadaSatellite\SimpleAmqp\Internal\Timer;

class StartConsumerCommand extends Command
{
	const COMMAND_CONSUMERS_START = 'simple_amqp:consumers:start';
    const ARGUMENT_QUEUE_NAME = 'queue';
    const OPTION_POLL_INTERVAL = 'interval';
    const OPTION_BATCH_TIMEOUT = 'timeout';

    /**
     * @var AmqpClientFactory
     */
    private $clientFactory;

    /**
     * @var LoggerFactory
     */
    private $loggerFactory;

    /**
     * @var Config
     */
    private $config;

    private $logger;

	public function __construct(	
        AmqpClientFactory $clientFactory,
        LoggerFactory $loggerFactory,
        Config $config,
        $name = null
	) {
		$this->clientFactory = $clientFactory;
        $this->loggerFactory = $loggerFactory;
		$this->config = $config;

		parent::__construct($name);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
        	$queueName = $input->getArgument(self::ARGUMENT_QUEUE_NAME);
            $this->logger = $this->loggerFactory->getLogger($queueName);
            $astQueue = new AstQueueProcessor();

            $this->log("Consumer started.");

        	$client = $this->clientFactory->getAmqpClient($queueName);
        	$client->createQueue($queueName);

        	// Interval in milliseconds.
            $interval = $input->getOption(self::OPTION_POLL_INTERVAL);
            // Timeout in milliseconds.
        	$timeout = $input->getOption(self::OPTION_BATCH_TIMEOUT);
        	$timer = new Timer($timeout);

        	$batch = array();
        	$timer->start();
        	do {
        		//$this->log("Try to get message from queue");

        		$message = $client->get();
        		if ($message === null) {
        			//$this->log("No message. Sleep...");
        			usleep($interval * 1000);
        		}
        		else {
        			$this->log("Got message:");

                    $decoded = json_decode($message->body);
                    if ($decoded === false || $decoded === null) {
                        $this->logger->info("Failed to decode message: {$message->body}");
                        $client->ack($message);
                        continue;
                    }

                    $envelope = new MessageEnvelope($message, $decoded);
        			$batch []= $envelope;
        		}

        		if ($timer->isExpired()) {
        			//$this->log("Timeout passed");

        			if (count($batch) > 0) {
        				$consumer = $this->config->getQueueBatchConsumerInstance($queueName);

        				try {
        					$consumer->consume($batch, $client, $astQueue);
        				}
        				catch (\Exception $e) {
        					$this->log("Error: " . $e->getMessage());
                            $this->log("Stack trace: " . $e->getTraceAsString());
        				}
        			}

                    $astQueue->consume();

        			$batch = array();
        			$timer->restart();
        		}
        	} while (true);
        } catch (\Exception $e) {
            $this->log("Command failed: " . $e->getMessage());
            $this->log("Stack trace: " . $e->getTraceAsString());
        }
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_CONSUMERS_START);
        $this->setDescription('Start queue consumer');

        $this->addArgument(
            self::ARGUMENT_QUEUE_NAME,
            InputArgument::REQUIRED,
            'The queue name.'
        );

        $this->addOption(
            self::OPTION_POLL_INTERVAL,
            null,
            InputOption::VALUE_REQUIRED,
            'Polling interval in ms (default is 1000).',
            1000
        );

        $this->addOption(
            self::OPTION_BATCH_TIMEOUT,
            null,
            InputOption::VALUE_REQUIRED,
            'Batching timeout in ms (default is 5000).',
            5000
        );

        parent::configure();
    }

    private function log($message) {
        echo "$message \r\n";
        $this->logger->info($message);
    }
}
