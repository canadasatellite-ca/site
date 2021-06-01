<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

class VendorHelper
{
	private $restApi;
	private $logger;

	function __construct(
		\CanadaSatellite\DynamicsIntegration\Rest\RestApi $restApi,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->restApi = $restApi;
		$this->logger = $logger;
	}

	/**
	 * @param string $vendor
	 * @return string Dynamics account id
	 */
	function getOrCreateVendorAccount($vendor)
	{
		$id = $this->findVendorAccount($vendor);
		if ($id === null) {
			$this->logger->info("Creating vendor $vendor account");
			$id = $this->createVendorAccount($vendor);
			$this->logger->info("Created vendor account $id");

			// Custom Dynamics workflow resets account type to 'Customer' if no account number is set. We set accountnumber to placeholder to prevent this behavior.
			// Remove placeholder.
			//usleep(1000000);
			//$patch = array('accountnumber' => null);
			//$this->restApi->updateAccount($id, $patch);
		}

		return $id;
	}

	/**
	 * @param string $vendor
	 * @return string|null Dynamics id
	 */
	private function findVendorAccount($vendor)
	{
		$id = $this->restApi->findVendorAccountIdByName($vendor);
		if ($id === false) {
			return null;
		}

		return $id;
	}

	private function createVendorAccount($vendor)
	{
		$account = array(
			'name' => $vendor,
			'new_accounttype' => 100000005,
			// Custom Dynamics workflow resets account type to 'Customer' if no account number is set. Use this placeholder to prevent it, 
			'accountnumber' => 'VNDR-REMOVEIT',
		);

		return $this->restApi->createAccount($account);
	}
}
