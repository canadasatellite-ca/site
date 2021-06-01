<?php

namespace CanadaSatellite\DynamicsIntegration\LogicProcessors;

class OrderNoteProcessor {
	private $simFactory;
	private $simUpdater;
	private $configValuesProvider;
	private $simValidator;
	private $logger;

	function __construct(
		\CanadaSatellite\DynamicsIntegration\Model\SimFactory $simFactory,
		\CanadaSatellite\DynamicsIntegration\Updater\SimUpdater $simUpdater,
		\CanadaSatellite\DynamicsIntegration\Config\ConfigValuesProvider $configValuesProvider,
		\CanadaSatellite\DynamicsIntegration\Validators\IccidValidator $simValidator,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->simFactory = $simFactory;
		$this->simUpdater = $simUpdater;
		$this->configValuesProvider = $configValuesProvider;
		$this->simValidator = $simValidator;
		$this->logger = $logger;
	}

	// Creates and activates SIM if it is presented in the note text
	function processSimInNote($accountId, $noteText) {
		// if note contains sim number and looks like the sim creation request
		$simOrderNoteRegex = $this->configValuesProvider->getSimOrderNoteRegex();
		if (preg_match($simOrderNoteRegex, $noteText, $matches)) {
			$simNumber = $matches[1];
			if ($this->simValidator->validateIccid($simNumber)) {
				$sim = $this->simFactory->create($simNumber, $accountId);
				if ($sim === null) {
					$this->logger->info("[OrderNoteProcessor] -> Sim not created. SimNumber = $simNumber. AccountId = $accountId");
					return;
				}
				$this->simUpdater->createSim($sim);
				$this->logger->info("[OrderNoteProcessor] -> Sim created. SimNumber = $simNumber. AccountId = $accountId");
			} else {
				$this->logger->info("[OrderNoteProcessor] -> Sim ICCID is not valid. SimNumber = $simNumber");
			}
		}
	}
}