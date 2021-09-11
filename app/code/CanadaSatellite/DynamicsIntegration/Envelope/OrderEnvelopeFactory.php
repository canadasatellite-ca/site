<?php

namespace CanadaSatellite\DynamicsIntegration\Envelope;

class OrderEnvelopeFactory
{
	private $customerModelFactory;
	private $orderUtils;
	private $converterUtils;
	private $addressFactory;
	private $customerFactory;
	private $itemFactory;

	function __construct(
		\Magento\Customer\Model\CustomerFactory $customerModelFactory,
		\CanadaSatellite\DynamicsIntegration\Utils\OrderUtils $orderUtils,
		\CanadaSatellite\DynamicsIntegration\Utils\ConverterUtils $converterUtils,
		\CanadaSatellite\DynamicsIntegration\Envelope\OrderAddressEnvelopeFactory $addressFactory,
		\CanadaSatellite\DynamicsIntegration\Envelope\CustomerEnvelopeFactory $customerFactory,
		\CanadaSatellite\DynamicsIntegration\Envelope\OrderItemEnvelopeFactory $itemFactory,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->customerModelFactory = $customerModelFactory;
		$this->orderUtils = $orderUtils;
		$this->converterUtils = $converterUtils;
		$this->addressFactory = $addressFactory;
		$this->customerFactory = $customerFactory;
		$this->itemFactory = $itemFactory;
		$this->logger = $logger;
	}

	/**
	 * @param \Magento\Sales\Api\Data\OrderInterface $order
	 */
	function create($order)
	{
		$data = array();

		$data['id'] = $order->getId();

		$billingAddress = $order->getBillingAddress();
		$data['billingAddress'] = $this->addressFactory->create($billingAddress);

		$shippingAddress = $order->getShippingAddress();
		$data['shippingAddress'] = $this->addressFactory->create($shippingAddress);

		$customerId = $order->getCustomerId();
		if ($customerId !== null) {
			// Service contracts returns stale customer data and have no possibility to force invalidation.
			$customerModel = $this->customerModelFactory->create()->load($customerId);
			if (!$customerModel->getId()) {
				throw new \Exception("Customer $customerId does not exist");
			}

			$data['customer'] = $this->customerFactory->create($customerModel->getDataModel());
		}
		else {
			$data['customer'] = null;
		}
		
		$data['createdAt'] = $this->orderUtils->getCreatedAt($order);
		$data['incrementId'] = $order->getIncrementId();
		$data['currency'] = $order->getOrderCurrencyCode();
		$data['status'] = $order->getStatus();

		$this->logger->info("Calculating visible items costs");
		$costs = $this->orderUtils->calculateVisibleItemsCosts($order);
		$this->logger->info("Visible items costs calculated: " . json_encode($costs));

		$data['items'] = array();
		foreach ($order->getAllVisibleItems() as $item) {
			$cost = $this->orderUtils->getVisibleItemBaseCost($item, $costs);
			$data['items'] []= $this->itemFactory->create($item, $cost);
		}

		$data['email'] = $order->getCustomerEmail();

		$shipmentDate = $this->orderUtils->getShipmentDate($order);
		$data['shippedAt'] = $shipmentDate;
		$this->logger->info("Shipping date: $shipmentDate");

		$shippingAmount = $order->getBaseShippingAmount();
		$this->logger->info("Shipping amount: $shippingAmount. Introspect: " . gettype($shippingAmount));
		$shippingAmount = $this->converterUtils->toFloat($shippingAmount);
		$this->logger->info("Shipping amount after converter: $shippingAmount. Introspect: " . gettype($shippingAmount));
		$data['shippingAmount'] = $shippingAmount;

		// TODO: Calculate bundle order item's costs.


		/*$this->logger->info("Experiment for base cost");
		foreach ($order->getAllItems() as $item) {
			$cost = $item->getBaseCost();
			$this->logger->info("Item cost: $cost");
		}*/

		return $data;
	}
}
