<?php

namespace CanadaSatellite\DynamicsIntegration\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ImportOrderCommand extends Command
{
	private $appState;
	private $orderFactory;
	private $orderRepository;

	private $publisher;
	private $config;
	private $envelopeFactory;
	private $eventFactory;

	private $logger;

	const COMMAND_IMPORT_ORDER = 'dynamics_integration:orders:import';
	const ARGUMENT_FROM_ORDER_ID = 'from_order_id';
	const ARGUMENT_TO_ORDER_ID = 'to_order_id';

	public function __construct(
		\Magento\Framework\App\State $appState,
		\Magento\Sales\Model\OrderFactory $orderFactory,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,

		\CanadaSatellite\SimpleAmqp\Publisher $publisher,
		\CanadaSatellite\DynamicsIntegration\Config\Config $config,
		\CanadaSatellite\DynamicsIntegration\Envelope\OrderEnvelopeFactory $envelopeFactory,
		\CanadaSatellite\DynamicsIntegration\Event\EventFactory $eventFactory,

		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger,

		$name = null
	) {
		$this->appState = $appState;
		$this->orderFactory = $orderFactory;
		$this->orderRepository = $orderRepository;

		$this->publisher = $publisher;
		$this->config = $config;
		$this->envelopeFactory = $envelopeFactory;
		$this->eventFactory = $eventFactory;

		$this->logger = $logger;

		parent::__construct($name);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->appState->setAreaCode('adminhtml');

		$fromOrderId = $input->getArgument(self::ARGUMENT_FROM_ORDER_ID);
		$toOrderId = $input->getArgument(self::ARGUMENT_TO_ORDER_ID);
		
		$error = $this->validateArguments($fromOrderId, $toOrderId);
		if ($error !== null) {
			$this->log($error);
			return;
		}

		$this->log("Import orders command executing.");
		if ($toOrderId === null) {
			$toOrderId = $fromOrderId;
		}

		for ($orderId = $fromOrderId; $orderId <= $toOrderId; $orderId++) {
			$this->log("Importing order $orderId");
			try {
				$order = $this->orderFactory->create()->loadByIncrementId($orderId);
				if (!$order->getId()) {
					$this->log("Order $orderId not found. Skipping...");
					continue;
				}
				$this->log("Order $orderId loaded");

				$this->publishOrderSavedEvent($order);

				$this->log("Order $orderId imported");
			} catch (\Exception $e) {
				$this->log("Failed to import order $orderId: " . $e->getMessage() . "\r\nStack trace: " . $e->getTraceAsString());
			}
		}

		$this->log("Import orders command executed.");
	}

	protected function configure()
    {
        $this->setName(self::COMMAND_IMPORT_ORDER);
        $this->setDescription('Import orders to Dynamics CRM');

        $this->addArgument(
            self::ARGUMENT_FROM_ORDER_ID,
            InputArgument::REQUIRED,
            'Order to import.'
        );

        $this->addArgument(
            self::ARGUMENT_TO_ORDER_ID,
            InputArgument::OPTIONAL,
            'Order to finish import.',
            null
        );

        parent::configure();
    }

    private function validateArguments($fromOrderId, $toOrderId) {
    	if (!$this->validateOrderId($fromOrderId)) {
			return "Invalid order id $fromOrderId. Please enter positive integer.";
		}
		if ($toOrderId !== null && !$this->validateOrderId($toOrderId)) {
			return "Invalid order id $toOrderId. Please enter positive integer.";
		}

		$parsedFromOrderId = intval($fromOrderId);
		$parsedToOrderId = $toOrderId !== null ? intval($toOrderId) : null;

		if ($parsedToOrderId !== null && $parsedToOrderId < $parsedFromOrderId) {
			return "To order id should be greater or equal to from order id.";
		}

		return null;
    }

    private function validateOrderId($value) {
    	return !!preg_match('/^\d+$/', $value);
    }

    private function publishOrderSavedEvent($order) {
    	$orderId = $order->getId();
    	$this->publisher->publish(
			$this->config->getIntegrationQueue(),
			$this->eventFactory->createOrderSavedEvent($orderId, $this->envelopeFactory->create($order))
		);
    }

    private function log($message) {
    	echo "$message\r\n";
    	$this->logger->info($message);
    }
}
