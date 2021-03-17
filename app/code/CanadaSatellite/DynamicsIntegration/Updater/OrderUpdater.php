<?php

namespace CanadaSatellite\DynamicsIntegration\Updater;

class OrderUpdater {
	private $orderRepository;
	private $customerRepository;
	private $helper;
	private $crm;
	private $logger;

	public function __construct(
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\CustomerHelper $helper,
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\DynamicsCrm $crm,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->orderRepository = $orderRepository;
		$this->customerRepository = $customerRepository;
		$this->helper = $helper;
		$this->crm = $crm;
		$this->logger = $logger;
	}

	public function createOrUpdate($order) {
		$this->logger->info('Try to create/update order in CRM.');
		$crmId = $this->crm->createOrUpdateOrder($order);
		$this->logger->info("Order created/updated in CRM with id $crmId.");
	}
}
