<?php

namespace CanadaSatellite\DynamicsIntegration\Updater;

class ActivationFormUpdater {
	private $crm;
	private $logger;

	public function __construct(
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\DynamicsCrm $crm,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->crm = $crm;
		$this->logger = $logger;
	}

	public function createOrUpdate($activationForm) {
		$this->logger->info('Try to create/update activation form in CRM.');
		$crmId = $this->crm->createOrUpdateActivationRequest($activationForm);
		$this->logger->info("Activation form created/updated in CRM with id $crmId.");
	}
}