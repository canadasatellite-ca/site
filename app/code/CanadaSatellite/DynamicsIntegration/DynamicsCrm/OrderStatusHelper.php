<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

class OrderStatusHelper
{
	private $statuses;
	private $logger;

	function __construct(
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->logger = $logger;
		$this->statuses = array(
			'attnreq' => 100000016,
			'partially_backordered' => 100000009,
			'processing' => 100000002,
			'complete_virtual' => 100000022,
			'payment_declined' => 100000020,
			'pending_payment' => 100000004,
			'payment_review' => 100000021,
			'pending' => 100000001,
			'returned' => 100000014,
			'inv_unpaid' => 100000019,
			'complete' => 100000008,
			'complete_all' => 100000018,
			'closed' => 100000017,
			'canceled' => 100000015,
		);
	}

	/**
	 * @param string $status
	 * @return int Dynamics sales order status id
	 */
	function getStatusId($status)
	{
		$this->logger->info("Get status for '$status'");
		if (!array_key_exists($status, $this->statuses)) {
			return null;
		}

		return $this->statuses[$status];
	}
}