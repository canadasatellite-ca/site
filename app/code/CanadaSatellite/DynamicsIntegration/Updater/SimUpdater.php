<?php

namespace CanadaSatellite\DynamicsIntegration\Updater;

class SimUpdater {
	private $crm;
	private $logger;

	function __construct(
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\DynamicsCrm $crm,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->crm = $crm;
		$this->logger = $logger;
	}

	function createSim($sim) {
		$this->logger->info('Try to create SIM in CRM.');
		$crmId = $this->crm->createSim($sim);
		$this->logger->info("SIM created in CRM with id $crmId.");
		return $crmId;
	}
}