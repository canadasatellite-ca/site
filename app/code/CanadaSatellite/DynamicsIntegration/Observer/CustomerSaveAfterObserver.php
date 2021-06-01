<?php

namespace CanadaSatellite\DynamicsIntegration\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerSaveAfterObserver implements ObserverInterface {
	private $customerFactory;
	private $publisher;
	private $config;
	private $envelopeFactory;
	private $eventFactory;
	private $logger;

	function __construct(
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\CanadaSatellite\SimpleAmqp\Publisher $publisher,
		\CanadaSatellite\DynamicsIntegration\Config\Config $config,
		\CanadaSatellite\DynamicsIntegration\Envelope\CustomerEnvelopeFactory $envelopeFactory,
		\CanadaSatellite\DynamicsIntegration\Event\EventFactory $eventFactory,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->customerFactory = $customerFactory;
		$this->publisher = $publisher;
		$this->config = $config;
		$this->envelopeFactory = $envelopeFactory;
		$this->eventFactory = $eventFactory;
		$this->logger = $logger;
	}

	function execute(\Magento\Framework\Event\Observer $observer) {
		try {
			$this->logger->info("[CustomerSaveAfterObserver] -> start");

			$customer = $observer->getCustomer();
			$customerId = $customer->getId();

			$this->logger->info("Customer $customerId saved.");

			// Service contracts returns stale customer data and have no possibility to force invalidation.
			$customerModel = $this->customerFactory->create()->load($customerId);
			if (!$customerModel->getId()) {
				throw new \Exception("Customer $customerId does not exist");
			}

			$this->publisher->publish(
				$this->config->getIntegrationQueue(),
				$this->eventFactory->createCustomerSavedEvent(
					$customerId,
					$this->envelopeFactory->create($customerModel->getDataModel())
				)
			);

			$this->logger->info("[CustomerSaveAfterObserver] -> end");
		}
		catch (\Exception $e) {
			$this->logger->info("Failed at CustomerSaveAfterObserver: " . $e->getMessage());
		}
	}
}