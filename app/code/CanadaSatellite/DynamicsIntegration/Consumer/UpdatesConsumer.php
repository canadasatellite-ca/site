<?php

namespace CanadaSatellite\DynamicsIntegration\Consumer;

use CanadaSatellite\Theme\Model;

class UpdatesConsumer implements \CanadaSatellite\SimpleAmqp\Api\BatchConsumerInterface
{
	private $customerFactory;
	private $customerUpdater;
	private $orderFactory;
	private $orderUpdater;
	private $productFactory;
	private $productUpdater;
	private $activationFormFactory;
	private $activationFormUpdater;
	private $orderNoteProcessor;
	private $logger;

	public function __construct(
		\CanadaSatellite\DynamicsIntegration\Model\CustomerFactory $customerFactory,
		\CanadaSatellite\DynamicsIntegration\Updater\CustomerUpdater $customerUpdater,
		\CanadaSatellite\DynamicsIntegration\Model\OrderFactory $orderFactory,
		\CanadaSatellite\DynamicsIntegration\Updater\OrderUpdater $orderUpdater,
		\CanadaSatellite\DynamicsIntegration\Model\ProductFactory $productFactory,
		\CanadaSatellite\DynamicsIntegration\Updater\ProductUpdater $productUpdater,
		\CanadaSatellite\DynamicsIntegration\Model\ActivationFormFactory $activationFormFactory,
		\CanadaSatellite\DynamicsIntegration\Updater\ActivationFormUpdater $activationFormUpdater,
		\CanadaSatellite\DynamicsIntegration\LogicProcessors\OrderNoteProcessor $orderNoteProcessor,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->customerFactory = $customerFactory;
		$this->customerUpdater = $customerUpdater;

		$this->orderFactory = $orderFactory;
		$this->orderUpdater = $orderUpdater;

		$this->productFactory = $productFactory;
		$this->productUpdater = $productUpdater;

		$this->activationFormFactory = $activationFormFactory;
		$this->activationFormUpdater = $activationFormUpdater;

		$this->orderNoteProcessor = $orderNoteProcessor;

		$this->logger = $logger;
	}

	public function consume($batch, $client)
	{
		$this->dump("Got message!", $batch);

		$eventsByKind = $this->groupByKind($batch, $client);
		$this->dump("Grouped:", $eventsByKind);

		foreach ($eventsByKind as $kind => $eventsByEntity) {
			foreach ($eventsByEntity as $entityId => $events) {
				// Events are envelopes from CanadaSatellite\SimpleAmqp\Envelope\MessageEnvelope
				try {
					$envelope = $this->getLast($events);
					$event = $envelope->getBody();

					switch ($kind) {
						case 'CustomerSaved':
							$customer = $event->data;
							$this->processCustomer($customer);
							break;
						case 'CustomerDeleted':
							$customerId = $entityId;
							$email = property_exists($event, 'email') ? $event->email : null;
							$this->processCustomerDelete($customerId, $email);
							break;
						case 'OrderSaved':
							$order = $event->data;
							$this->processOrder($order);
							break;
						case 'OrderNoteAdded':
							$orderId = $event->id;
							$note = $event->data;
							$this->processOrderNote($orderId, $note);
							break;
						case 'ProductSaved':
							$product = $event->data;
							$sku = $event->sku;
							$this->processProduct($product, $sku);
							break;
						case 'ProductDeleted':
							$productId = $entityId;
							$sku = $event->sku;
							$this->processProductDelete($productId, $sku);
							break;
						case 'ActivationFormSaved':
							$activationForm = $event->data;
							$this->processActivationForm($activationForm);
							break;
						}
				} catch (\Exception $e) {
					$this->logger->info("Failed processing: " . $e->getMessage());
					$this->logger->info("Stack trace: " . $e->getTraceAsString());
				} finally {
					foreach ($events as $envelope) {
						$client->ack($envelope->getAmqpMessage());
					}
				}
			}
		}
	}

	private function groupByKind($batch, $client)
	{
		$groups = array();

		foreach ($batch as $envelope) {
			$event = $envelope->getBody();

			if (!property_exists($event, 'kind') || !property_exists($event, 'id')) {
				// Skip, it's not valid event.
				$client->ack($envelope->getAmqpMessage());
				continue;
			}

			if (!array_key_exists($event->kind, $groups)) {
				$groups[$event->kind] = array();
			}

			$kindGroup = &$groups[$event->kind];
			if (!array_key_exists($event->id, $kindGroup)) {
				$kindGroup[$event->id] = array();
			}

			$entityEvents = &$kindGroup[$event->id];
			$entityEvents []= $envelope;
		}

		return $groups;
	}

	private function getLast($array)
	{
		if (!is_array($array)) {
			throw new \Exception("Not array.");
		}
		if (count($array) === 0) {
			throw new \Exception("Empty array.");
		}

		return $array[count($array) - 1];
	}

	private function processCustomer($customerEnvelope)
	{
		$customer = $this->customerFactory->fromEnvelope($customerEnvelope);

		$this->logger->info("[CustomerConsumer] -> Customer message received for customer {$customer->getId()}");

		$this->customerUpdater->createOrUpdate($customer);

		$this->logger->info("[CustomerConsumer] -> Customer message processed");
	}

	private function processCustomerDelete($customerId, $email)
	{
		$customer = $this->customerFactory->create($customerId, $email, 'Deleted', 'Deleted');

		$this->logger->info("[CustomerDeleteConsumer] -> Customer message received for customer delete $customerId");

		$this->customerUpdater->delete($customer);

		$this->logger->info("[CustomerDeleteConsumer] -> Customer message processed");
	}

	private function processOrder($orderEnvelope)
	{
		$order = $this->orderFactory->fromEnvelope($orderEnvelope);

		$this->logger->info("[OrderConsumer] -> Order message received for order {$order->getId()}");

		$this->orderUpdater->createOrUpdate($order);

		$this->logger->info("[OrderConsumer] -> Order message processed");
	}

	private function processOrderNote($orderId, $note) {
		$this->logger->info("[OrderNoteConsumer] -> Order note message received for order $orderId");
		$this->orderUpdater->createOrderNote($orderId, $note);

		$order = $this->orderUpdater->getOrder($orderId);
		if ($order) {
			$accountId = $order->_customerid_value;
			$this->orderNoteProcessor->processSimInNote($accountId, $note);
		}

        $this->logger->info("[OrderNoteConsumer] -> Order note message processed");
    }

	private function processProduct($productEnvelope, $sku)
	{
		$product = $this->productFactory->fromEnvelope($productEnvelope);

		$this->logger->info("[ProductConsumer] -> Product message received for product {$product->getId()}");

		$this->productUpdater->createOrUpdate($product, $sku);

		$this->logger->info("[ProductConsumer] -> Product message processed");
	}

	private function processProductDelete($productId, $sku)
	{
		$this->logger->info("[ProductDeleteConsumer] -> Product message received for product $productId");

		$this->productUpdater->delete($sku);

		$this->logger->info("[ProductDeleteConsumer] -> Product message processed");
	}

	private function processActivationForm($activationFormEnvelope)
	{
		$activationForm = $this->activationFormFactory->fromEnvelope($activationFormEnvelope);

		$this->logger->info("[ActivationFormConsumer] -> Activation form message received for activation form {$activationForm->getId()}");

		$this->activationFormUpdater->createOrUpdate($activationForm);

		$this->logger->info("[ActivationFormConsumer] -> Activation form message processed");
	}

	private function dump($message, $body)
	{
		echo "$message\r\n";
		var_dump($body);

		$this->logger->info($message . var_export($body, true));
	}
}
