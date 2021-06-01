<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

class ContactComposer
{
	private $addressBuilder;
	private $logger;

	function __construct(
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\AddressBuilder $addressBuilder,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->addressBuilder = $addressBuilder;
		$this->logger = $logger;
	}

	/**
	 * @param $customer Customer envelope
	 * @param string $accountId
	 * @return array
	 */
	function compose($customer, $accountId = null)
	{
		$this->logger->info("Enter ContactComposer::compose");

		$customerId = $customer->getId();
		$prefix = $customer->getPrefix();
		$firstName = $customer->getFirstname();
		$middleName = $customer->getMiddlename();
		$lastName = $customer->getLastname();
		$email = $customer->getEmail();
		$phone = $customer->getPhone();
		$fax = $customer->getFax();

		$url = $customer->getUrl();
		$gender = $customer->getGender();
		$birthDate = $customer->getBirthDate();
		$group = $customer->getGroup();

		$data = array(
			'firstname' => $firstName,
			'lastname' => $lastName,
			'new_accountnumber' => strval($customerId),
			'emailaddress1' => $email,
		);
		if ($prefix !== null) {
			$data['salutation'] = $prefix;
		}
		if ($middleName !== null) {
			$data['middlename'] = $middleName;
		}
		if ($accountId !== null) {
			$data['parentcustomerid_account@odata.bind'] = "/accounts($accountId)";
		}

		if ($phone !== null) {
			$data['telephone1'] = $phone;
		}
		if ($fax !== null) {
			$data['fax'] = $fax;
		}

		if ($url !== null) {
			$this->logger->info("Url from magento: $url");
			$data['websiteurl'] = $url;
		}
		if ($gender !== null) {
			$this->logger->info("Gender from magento: {$gender}");
			// TODO: Convert to dynamics format.
			$gendercode = $gender;
			$data['gendercode'] = $gendercode;
		}
		if ($birthDate !== null) {
			$this->logger->info("Birth date from magento: {$birthDate}");
			// TODO: Convert to dynamics format.
			$data['birthdate'] = $birthDate;
		}
		if ($group !== null) {
			$this->logger->info("Group from magento: {$group}");
			// TODO: Add 'group' field to dynamics and map it.
		}

		$data = array_merge_recursive($data, $this->addressBuilder->buildBillingAddressData($customer->getBillingAddress()));

		return $data;
	}
}