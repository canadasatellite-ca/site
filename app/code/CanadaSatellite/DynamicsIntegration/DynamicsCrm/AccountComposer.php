<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

class AccountComposer
{
	private $mapper;
	private $addressBuilder;

	public function __construct(
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\DynamicsMapper $mapper,
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\AddressBuilder $addressBuilder
	) {
		$this->mapper = $mapper;
		$this->addressBuilder = $addressBuilder;
	}

	/**
	 * @param $customer Customer envelope.
	 */
	public function compose($customer)
	{
		$customerId = $customer->getId();
		$firstName = $customer->getFirstname();
		$lastName = $customer->getLastname();
		$email = $customer->getEmail();
		$phone = $customer->getPhone();

		$source = $customer->getSource();

		$company = $customer->getCompany();
		if ($company === null) {
			$name = "$lastName, $firstName";
		} else {
			$name = $company;
		}

		$data = array(
			'name' => $name,
			'accountnumber' => strval($customerId),
			'emailaddress1' => $email,
		);

		if ($phone !== null) {
			$data['telephone1'] = $phone;
		}

		if ($source !== null) {
			$accountSource = $this->mapper->mapAccountSource($source);
			$data['new_accountsource'] = $accountSource;
		}

		$data = array_merge_recursive($data, $this->addressBuilder->buildBillingAddressData($customer->getBillingAddress()));

		return $data;
	}
}