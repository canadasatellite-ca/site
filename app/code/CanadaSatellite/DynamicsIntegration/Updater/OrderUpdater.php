<?php

namespace CanadaSatellite\DynamicsIntegration\Updater;

class OrderUpdater {
	private $crm;
	private $logger;

	public function __construct(
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\DynamicsCrm $crm,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->crm = $crm;
		$this->logger = $logger;
	}

	public function createOrUpdate($order) {
		$this->logger->info('Try to create/update order in CRM.');
		$crmId = $this->crm->createOrUpdateOrder($order);
		$this->logger->info("Order created/updated in CRM with id $crmId.");
	}

	public function createOrderNote($orderId, $note) {
		$this->logger->info('Try to add orderNote in CRM.');
		$noteId = $this->crm->createOrderNote($orderId, $note);
		$this->logger->info("Order note ($noteId) added in CRM");
	}

	public function getOrder($orderId) {
		$this->logger->info("Try to get order by Id");
		$order = $this->crm->getOrder($orderId);
		if ($order) {
			$this->logger->info("Order found. Id: ".$order->salesorderid);
		}

		return $order;
	}
}