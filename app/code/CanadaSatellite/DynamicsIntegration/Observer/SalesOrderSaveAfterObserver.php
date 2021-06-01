<?php

namespace CanadaSatellite\DynamicsIntegration\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderSaveAfterObserver implements ObserverInterface {
	private $orderRepository;
	private $publisher;
	private $config;
	private $envelopeFactory;
	private $eventFactory;
	private $logger;

	function __construct(
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
		\CanadaSatellite\SimpleAmqp\Publisher $publisher,
		\CanadaSatellite\DynamicsIntegration\Config\Config $config,
		\CanadaSatellite\DynamicsIntegration\Envelope\OrderEnvelopeFactory $envelopeFactory,
		\CanadaSatellite\DynamicsIntegration\Event\EventFactory $eventFactory,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->orderRepository = $orderRepository;
		$this->publisher = $publisher;
		$this->config = $config;
		$this->envelopeFactory = $envelopeFactory;
		$this->eventFactory = $eventFactory;
		$this->logger = $logger;
	}

	function execute(\Magento\Framework\Event\Observer $observer) {
		try {
			$this->logger->info("[SalesOrderSaveAfterObserver] -> start");

			$order = $observer->getEvent()->getOrder();
			$orderId = $order->getId();
			$this->logger->info("Order $orderId saved.");

			$this->publisher->publish(
				$this->config->getIntegrationQueue(),
				$this->eventFactory->createOrderSavedEvent(
					$orderId,
					$this->envelopeFactory->create($this->orderRepository->get($orderId))
				)
			);

			$this->logger->info("[SalesOrderSaveAfterObserver] -> end");
		}
		catch (\Exception $e) {
			$this->logger->info("Failed at SalesOrderSaveAfterObserver: " . $e->getMessage());
		}
	}
}