<?php

namespace CanadaSatellite\DynamicsIntegration\Updater;

class CustomerUpdater {
	private $customerRepository;
	private $helper;
	private $logger;

	public function __construct(
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\CustomerHelper $helper,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->customerRepository = $customerRepository;
		$this->helper = $helper;
		$this->logger = $logger;
	}

	public function createOrUpdate($customer) {
		$this->logger->info("Try to create/update customer in CRM.");
		$crmId = $this->helper->createOrUpdateCustomer($customer);
		$this->logger->info("Customer created/updated in CRM with id $crmId");
	}

	public function delete($customer) {
		$this->logger->info("Try to delete customer in CRM.");
		$this->helper->deleteCustomer($customer);
		$this->logger->info("Customer deleted in CRM.");
	}
}