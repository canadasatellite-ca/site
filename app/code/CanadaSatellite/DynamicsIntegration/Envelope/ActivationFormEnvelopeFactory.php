<?php

namespace CanadaSatellite\DynamicsIntegration\Envelope;

class ActivationFormEnvelopeFactory
{
	private $orderModelFactory;
	private $orderEnvelopeFactory;
	private $customerEnvelopeFactory;

	public function __construct(
		\Magento\Sales\Model\OrderFactory $orderModelFactory,
		\CanadaSatellite\DynamicsIntegration\Envelope\OrderEnvelopeFactory $orderEnvelopeFactory,
		\CanadaSatellite\DynamicsIntegration\Envelope\CustomerEnvelopeFactory $customerEnvelopeFactory,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->orderModelFactory = $orderModelFactory;
		$this->orderEnvelopeFactory = $orderEnvelopeFactory;
		$this->customerEnvelopeFactory = $customerEnvelopeFactory;
		$this->logger = $logger;
	}

	public function create($activationForm, $customer = null)
	{
		$data = array();

		$data['id'] = $activationForm->getId();
		
		$data['email'] = $activationForm->getEmail();
		$data['firstName'] = $activationForm->getFirstname();
		$data['lastName'] = $activationForm->getLastname();
		$data['companyName'] = $activationForm->getCompany();
		$data['notes'] = $activationForm->getNotes();
		$data['simNumber'] = $activationForm->getSimNumber();
		$data['dataNumber'] = $activationForm->getDataNumber();

		$orderNumber = $activationForm->getOrderNumber();
		$data['orderNumber'] = $orderNumber;

		$orderModel = $this->orderModelFactory->create()->loadByIncrementId($orderNumber);
		if ($this->checkOrderItems($orderModel) && $orderModel->getId()) {
			$data['order'] = $this->orderEnvelopeFactory->create($orderModel);
		} else {
			$data['order'] = null;
		}

		$status = $activationForm->getStatus();
		$data['status'] = intval($status);

		if ($customer !== null) {
			$data['customer'] = $this->customerEnvelopeFactory->create($customer);
		} else {
			$data['customer'] = null;
		}

		$data['desiredActivationDate'] = $this->formatDateOnly($activationForm->getDesiredActivationDate());
		$data['completedDate'] = $this->formatDateUtc($activationForm->getCompletedDate());
		$data['expirationDate'] = $this->formatDateOnly($activationForm->getExpirationDate());
		$data['phoneNumber'] = $activationForm->getPhoneNumber();
		$data['comments'] = $activationForm->getComments();

		$this->logger->info("DesiredActivationDate " . var_export($activationForm->getDesiredActivationDate(), true));
		$this->logger->info("CompletedDate " . var_export($activationForm->getCompletedDate(), true));
		$this->logger->info("ExpirationDate " . var_export($activationForm->getExpirationDate(), true));

		return $data;
	}

    /**
     * Check product items in order.
     *
     * @param $order
     * @return bool
     */
    private function checkOrderItems($order)
    {
        foreach ($order->getAllItems() as $item) {
            $parent = $item->getParentItem();
            if ($parent) {
                continue;
            }
            $product = $item->getProduct();
            if($product === null)
                return false;
        }
        return true;
    }

    private function formatDateUtc($dateUtc)
	{
		if ($dateUtc === null) {
			return null;
		}

		return (new \DateTime($dateUtc))->format("Y-m-d\TH:i:s\Z");
	}

	private function formatDateOnly($dateLocal)
	{
		if ($dateLocal === null) {
			return null;
		}

		return (new \DateTime($dateLocal))->format("Y-m-d");
	}
}
