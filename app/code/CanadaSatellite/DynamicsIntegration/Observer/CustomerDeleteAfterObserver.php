<?php

namespace CanadaSatellite\DynamicsIntegration\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerDeleteAfterObserver implements ObserverInterface {
	private $publisher;
	private $config;
	private $eventFactory;
	private $logger;

	public function __construct(
		\CanadaSatellite\SimpleAmqp\Publisher $publisher,
		\CanadaSatellite\DynamicsIntegration\Config\Config $config,
		\CanadaSatellite\DynamicsIntegration\Event\EventFactory $eventFactory,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->publisher = $publisher;
		$this->config = $config;
		$this->eventFactory = $eventFactory;
		$this->logger = $logger;
	}

	public function execute(\Magento\Framework\Event\Observer $observer) {
		try {
			$this->logger->info("[CustomerDeleteAfterObserver] -> start");

			$customer = $observer->getCustomer();
			$customerId = $customer->getId();
			$email = $customer->getEmail();

			$this->logger->info("Customer $customerId deleted.");

			$this->publisher->publish($this->config->getIntegrationQueue(), $this->eventFactory->createCustomerDeletedEvent($customerId, $email));

			$this->logger->info("[CustomerDeleteAfterObserver] -> end");
		}
		catch (\Exception $e) {
			$this->logger->info("Failed at CustomerDeleteAfterObserver: " . $e->getMessage());
		}
	}
}