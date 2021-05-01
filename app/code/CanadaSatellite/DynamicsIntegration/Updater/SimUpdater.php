<?php

namespace CanadaSatellite\DynamicsIntegration\Updater;

class SimUpdater {
	private $crm;
	private $logger;

	public function __construct(
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\DynamicsCrm $crm,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->crm = $crm;
		$this->logger = $logger;
	}

	public function createSim($sim) {
		$this->logger->info('Try to create SIM in CRM.');
		$crmId = $this->crm->createSim($sim);
		$this->logger->info("SIM created in CRM with id $crmId.");
	}
}