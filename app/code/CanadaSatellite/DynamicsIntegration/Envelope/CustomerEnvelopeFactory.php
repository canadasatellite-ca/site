<?php

namespace CanadaSatellite\DynamicsIntegration\Envelope;

class CustomerEnvelopeFactory
{
	private $addressUtils;
	private $eavUtils;
	private $customerUtils;
	private $addressFactory;

	function __construct(
		\CanadaSatellite\DynamicsIntegration\Utils\AddressUtils $addressUtils,
		\CanadaSatellite\DynamicsIntegration\Utils\EavUtils $eavUtils,
		\CanadaSatellite\DynamicsIntegration\Utils\CustomerUtils $customerUtils,
		\CanadaSatellite\DynamicsIntegration\Envelope\CustomerAddressEnvelopeFactory $addressFactory
	) {
		$this->addressUtils = $addressUtils;
		$this->eavUtils = $eavUtils;
		$this->customerUtils = $customerUtils;
		$this->addressFactory = $addressFactory;
	}

	/**
	 * @param \Magento\Customer\Api\Data\CustomerInterface $customer
	 * @return array
	 */
	function create($customer)
	{
		$data = array();

		$data['id'] = $customer->getId();
		$data['billingAddress'] = $this->addressFactory->create($this->addressUtils->getCustomerDefaultBillingAddress($customer));
		$data['prefix'] = $customer->getPrefix();
		$data['firstName'] = $customer->getFirstname();
		$data['middleName'] = $customer->getMiddlename();
		$data['lastName'] = $customer->getLastname();
		$data['email'] = $customer->getEmail();

		$data['url'] = $this->eavUtils->getTextAttributeValue($customer, 'url');

		$data['gender'] = $this->customerUtils->getGender($customer);
		$data['birthDate'] = $this->customerUtils->getBirthDate($customer);
		$data['group'] = $this->customerUtils->getGroup($customer);
		$data['source'] = $this->customerUtils->getSource($customer);

		return $data;
	}
}
