<?php

namespace CanadaSatellite\DynamicsIntegration\Utils;

class AddressUtils
{
	private $countryInfo;

	function __construct(
		\Magento\Directory\Api\CountryInformationAcquirerInterface $countryInfo
	) {
		$this->countryInfo = $countryInfo;
	}

	/**
	 * @param \Magento\Customer\Api\Data\CustomerInterface $customer
	 * @return \Magento\Customer\Api\Data\AddressInterface
	 */
	function getCustomerDefaultBillingAddress($customer)
	{
		foreach ($customer->getAddresses() as $address) {
			if ($address->isDefaultBilling()) {
				return $address;
			}
		}

		return null;
	}

	/**
	 * @param \Magento\Customer\Api\Data\AddressInterface
	 * @return string|null
	 */
	function getCountryName($address)
	{
		$countryId = $address->getCountryId();
		if ($countryId === null) {
			return null;
		}

		$info = $this->countryInfo->getCountryInfo($countryId);
		return $info->getFullNameEnglish();
	}
}
