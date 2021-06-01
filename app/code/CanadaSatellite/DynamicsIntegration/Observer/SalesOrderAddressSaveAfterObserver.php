<?php

namespace CanadaSatellite\DynamicsIntegration\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderAddressSaveAfterObserver implements ObserverInterface {
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
			$this->logger->info("[SalesOrderAddressSaveAfterObserver] -> start");

			$this->logger->info("Order address saved.");
			$address = $observer->getAddress();
			$orderId = $address->getParentId();
			$this->logger->info("Order $orderId address saved.");

			$this->publisher->publish(
				$this->config->getIntegrationQueue(),
				$this->eventFactory->createOrderSavedEvent(
					$orderId,
					$this->envelopeFactory->create($this->orderRepository->get($orderId))
				)
			);

			$this->logger->info("[SalesOrderAddressSaveAfterObserver] -> end");
		}
		catch (\Exception $e) {
			$this->logger->info("Failed at SalesOrderAddressSaveAfterObserver: " . $e->getMessage());
		}
	}
}