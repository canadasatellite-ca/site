<?php

namespace CanadaSatellite\DynamicsIntegration\Observer;

use Magento\Framework\Event\ObserverInterface;

class ActivationFormSaveAfterObserver implements ObserverInterface {
	private $activationFormFactory;
	private $publisher;
	private $config;
	private $envelopeFactory;
	private $eventFactory;
	private $logger;

	public function __construct(
		\Interactivated\ActivationForm\Model\ActivationFormFactory $activationFormFactory,
		\CanadaSatellite\SimpleAmqp\Publisher $publisher,
		\CanadaSatellite\DynamicsIntegration\Config\Config $config,
		\CanadaSatellite\DynamicsIntegration\Envelope\ActivationFormEnvelopeFactory $envelopeFactory,
		\CanadaSatellite\DynamicsIntegration\Event\EventFactory $eventFactory,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->activationFormFactory = $activationFormFactory;
		$this->publisher = $publisher;
		$this->config = $config;
		$this->envelopeFactory = $envelopeFactory;
		$this->eventFactory = $eventFactory;
		$this->logger = $logger;
	}

	public function execute(\Magento\Framework\Event\Observer $observer) {
		try {
			$this->logger->info("[ActivationFormSaveAfterObserver] -> start");

			$activationForm = $observer->getEvent()->getActivationform();
			$activationFormId = $activationForm->getId();

			$this->logger->info("Activation form $activationFormId saved.");

			// Service contracts returns stale data and have no possibility to force invalidation.
			$activationFormModel = $this->activationFormFactory->create()->load($activationFormId);
			if (!$activationFormModel->getId()) {
				throw new \Exception("Activation form $activationFormId does not exist");
			}

			$this->publisher->publish(
				$this->config->getIntegrationQueue(),
				$this->eventFactory->createActivationFormSavedEvent(
					$activationFormId,
					$this->envelopeFactory->create($activationFormModel)
				)
			);

			$this->logger->info("[ActivationFormSaveAfterObserver] -> end");
		}
		catch (\Exception $e) {
			$this->logger->info("Failed at ActivationFormSaveAfterObserver: " . $e->getMessage());
		}
	}
}