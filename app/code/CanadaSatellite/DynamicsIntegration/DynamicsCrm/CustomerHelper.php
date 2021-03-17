<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

class CustomerHelper {
	private $accountComposer;
	private $contactComposer;
	private $currencyHelper;
	private $restApi;
	private $logger;

	public function __construct(
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\AccountComposer $accountComposer,
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\ContactComposer $contactComposer,
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\CurrencyHelper $currencyHelper,
		\CanadaSatellite\DynamicsIntegration\Rest\RestApi $restApi,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->accountComposer = $accountComposer;
		$this->contactComposer = $contactComposer;
		$this->currencyHelper = $currencyHelper;
		$this->restApi = $restApi;
		$this->logger = $logger;
	}

	/**
	* @param $customer Customer envelope. (Created in \CanadaSatellite\DynamicsIntegration\Envelope\CustomerEnvelopeFactory)
	* @return string Dynamics account id
	*/
	public function createOrUpdateCustomer($customer)
	{
		$this->logger->info("[createOrUpdateCustomer] Finding customer contact by id/email.");
		$contact = $this->findCustomerContact($customer);
		if ($contact === false) {
			$this->logger->info("[createOrUpdateCustomer] Contact not found -> create customer");
			$accountId = $this->createCustomer($customer);
			$this->logger->info("[createOrUpdateCustomer] Customer created with account id $accountId");
		} else {
			$contactId = $contact->contactid;
			$this->logger->info("[createOrUpdateCustomer] Contact found with id $contactId -> update customer");
			$this->validateCustomerContact($customer, $contact);

			$oldAccountId = isset($contact->parentcustomerid_account) ? $contact->parentcustomerid_account->accountid : null;
			$this->logger->info("[createOrUpdateCustomer] Customer account id before update " . $oldAccountId);
			$accountId = $this->updateCustomer($customer, $contact);
			$this->logger->info("[createOrUpdateCustomer] Customer account id after update $accountId");
		}

		return $accountId;
	}

	public function deleteCustomer($customer)
	{
		$customerId = $customer->getId();

		$this->logger->info("[deleteCustomer] Finding customer $customerId contact by id/email");
		$contact = $this->findCustomerContact($customer);
		if ($contact === null)
		{
			$this->logger->info("[deleteCustomer] Customer $customerId contact not found");
			$account = $this->findCustomerAccount($customer);
		} else {
			$this->validateCustomerContact($customer, $contact);
			$account = $contact->parentcustomerid_account;
		}

		if ($contact !== null) {
			$contactId = $contact->contactid;
			$this->logger->info("[deleteCustomer] Deleting customer $customerId contact $contactId...");
			$this->restApi->deleteContact($contactId);
			$this->logger->info("[deleteCustomer] Customer $customerId contact $contactId deleted");
		}


		if ($account === null) {
			$this->logger->info("[deleteCustomer] Customer $customerId account not found");
			return;
		}

		$accountId = $account->accountid;
		$this->logger->info("[deleteCustomer] Check if it's safe to delete customer $customerId account $accountId");
		$safeToDeleteAccount = $this->isSeparateAccountForContact($account, $contact);
		if (!$safeToDeleteAccount) {
			$this->logger->info("[deleteCustomer] It's not safe to delete customer $customerId account $accountId. It may have other customer's contacts");
			return;
		}

		$this->logger->info("[deleteCustomer] It's safe to delete. Deleting customer $customerId account $accountId...");
		$this->restApi->deleteAccount($accountId);
		$this->logger->info("[deleteCustomer] Customer $customerId account $accountId deleted");
	}

	/** 
	* @param string $customerId
	* @return string|null Dynamics account id
	*/
	public function getCustomerAccountId($customer) {
		$customerId = $customer->getId();

		$contact = $this->findCustomerContact($customer);
		if ($contact === false) {
			$this->logger->info("[getCustomerAccountId] Contact of $customerId not found.");
			return null;
		}
		if (!isset($contact->parentcustomerid_account)) {
			$this->logger->info("[getCustomerAccountId] Contact of $customerId has no account!");
			return null;
		}
		
		return $contact->parentcustomerid_account->accountid;
	}

	public function findCustomerAccount($customer)
	{
		$customerId = $customer->getId();
		$email = $customer->getEmail();

		$account = false;
		if ($email !== null) {
			$this->logger->info("[findCustomerAccount] Finding customer $customerId account by customer id $customerId and email $email");
			$account = $this->restApi->findAccountByCustomerIdAndEmail($customerId, $email);
		}
		if ($account !== false) {
			return $account;
		}

		$this->logger->info("[findCustomerAccount] Account exact match not found -> finding by customer id $customerId");
		$account = $this->restApi->findAccountByCustomerId($customerId);
		if ($account !== false) {
			return $account;
		}

		if ($email !== null) {
			$this->logger->info("[findCustomerAccount] Account not found by customer id -> find by email $email");
			$account = $this->restApi->findAccountByEmail($email);
		}

		return $account;
	}

	/**
	 * @return string Account id
	 */
	private function createCustomer($customer)
	{
		// Assumes customer has no contact, should be validated by caller.
		$this->logger->info("[createCustomer] Creating customer account if not exist...");
		$accountId = $this->getOrCreateCustomerAccountId($customer);
		$this->logger->info("[createCustomer] Customer account id $accountId.");

		$this->logger->info("[createCustomer] Creating customer contact...");
		$crmContact = $this->contactComposer->compose($customer, $accountId);
		$contactId = $this->restApi->createContact($crmContact);
		$this->logger->info("[createCustomer] Customer contact created with id $contactId");

		$this->logger->info("[createCustomer] Setting account $accountId primary contact $contactId and currency.");
		$this->setAccountPrimaryContactAndCurrency($accountId, $contactId);
		$this->logger->info("[createCustomer] Account primary contact and currency set.");

		$this->logger->info("[createCustomer] Customer created with account id $accountId");

		return $accountId;
	}

	/** 
	 * @return string Account Id
	 */
	private function updateCustomer($customer, $contact)
	{
		$customerId = $customer->getId();
		$contactId = $contact->contactid;
		$this->logger->info("[updateCustomer] Updating customer $customerId contact $contactId.");

		$this->logger->info("[updateCustomer] Creating customer account if not exist...");
		$accountId = $this->getOrCreateCustomerAccountId($customer);
		$this->logger->info("[updateCustomer] Customer account id $accountId.");

		$this->logger->info("[updateCustomer] Updating contact $contactId...");
		$crmContact = $this->contactComposer->compose($customer, $accountId);
		$this->restApi->updateContact($contactId, $crmContact);
		$this->logger->info("[updateCustomer] Contact $contactId updated.");

		$this->logger->info("[updateCustomer] Updating account $accountId...");
		$crmAccount = $this->accountComposer->compose($customer);
		$this->restApi->updateAccount($accountId, $crmAccount);
		$this->logger->info("[updateCustomer] Account $accountId updated.");

		$this->logger->info("[updateCustomer] Setting account $accountId primary contact $contactId and currency.");
		$this->setAccountPrimaryContactAndCurrency($accountId, $contactId);
		$this->logger->info("[updateCustomer] Account primary contact and currency set.");

		$this->logger->info("[updateCustomer] Customer updated with account id $accountId");

		return $accountId;
	}

	private function setAccountPrimaryContactAndCurrency($accountId, $contactId)
	{
		$this->logger->info("[setAccountPrimaryContactAndCurrency] Setting account primary contact");
		$this->restApi->setAccountPrimaryContact($accountId, $contactId);
		$this->logger->info("[setAccountPrimaryContactAndCurrency] Account primary contact is set.");

		$this->logger->info("[setAccountPrimaryContactAndCurrency] Setting account currency as CAD by default");
		$currencyId = $this->currencyHelper->getCurrencyIdByCode('CAD');
		$this->restApi->setAccountCurrency($accountId, $currencyId);
		$this->logger->info("[setAccountPrimaryContactAndCurrency] Account currency set.");
	}


	/**
	 * @return string CRM Account id
	 */
	private function getOrCreateCustomerAccountId($customer)
	{
		$this->logger->info("[getOrCreateCustomerAccountId] Finding customer account...");
		$account = $this->findCustomerAccount($customer);
		if ($account === false) {
			$this->logger->info("[getOrCreateCustomerAccountId] Customer account not found. Creating account...");
			$crmAccount = $this->accountComposer->compose($customer);
			$accountId = $this->restApi->createAccount($crmAccount);
			$this->logger->info("[getOrCreateCustomerAccountId] Customer account created with account id $accountId");
		} else {
			$accountId = $account->accountid;
			$this->logger->info("[getOrCreateCustomerAccountId] Customer account found with account id $accountId");
		}

		return $accountId;
	}

	private function findCustomerContact($customer)
	{
		$customerId = $customer->getId();
		$email = $customer->getEmail();

		$contact = false;
		if ($email !== null) {
			$this->logger->info("[findCustomerContact] Finding customer $customerId contact by customer id $customerId and email $email.");
			$contact = $this->restApi->findContactByCustomerIdAndEmail($customerId, $email);
		}
		if ($contact !== false) {
			return $contact;
		}

		$this->logger->info("[findCustomerContact] Contact exact match not found -> finding by customer id $customerId.");
		$contact = $this->restApi->findContactByCustomerId($customerId);
		if ($contact !== false) {
			return $contact;
		}
		
		if ($email !== null) {
			$this->logger->info("[findCustomerContact] Contact not found by customer id -> find by email $email");
			$contact = $this->restApi->findContactByEmail($email);
		}
		

		return $contact;
	}

	private function validateCustomerContact($customer, $contact)
	{
		$customerId = $customer->getId();
		$contactId = $contact->contactid;
		$contactCustomerId = $contact->new_accountnumber;
		$contactEmail = $contact->emailaddress1;

		if ($contactCustomerId === null) {
			$this->logger->info("[checkContactAccount] Customer $customerId contact ($contactId) has no customer id.");
		}
		if ($contactEmail === null) {
			$this->logger->info("[checkContactAccount] Customer $customerId contact ($contactId) has no email.");
		}

		if ($contact->parentcustomerid_account === null) {
			$this->logger->info("[checkContactAccount] Customer $customerId contact ($contactId) has no account.");
			return;
		}

		$accountId = $contact->parentcustomerid_account->accountid;
		$accountCustomerId = $contact->parentcustomerid_account->accountnumber;
		$accountEmail = $contact->parentcustomerid_account->emailaddress1;
		if ($accountCustomerId === null) {
			$this->logger->info("[checkContactAccount] Customer $customerId account ($accountId) has no customer id.");
		}
		if ($accountEmail === null) {
			$this->logger->info("[checkContactAccount] Customer $customerId account ($accountId) has no email.");
		}

		if ($contactCustomerId !== $accountCustomerId) {
			$this->logger->info("[checkContactAccount] Customer $customerId contact ($contactId) and account ($accountId) customer id does not match!");
		}
		if ($contactEmail !== $accountEmail) {
			$this->logger->info("[checkContactAccount] Customer $customerId contact ($contactId) and account ($accountId) email does not match!");
		}
	}

	private function isSeparateAccountForContact($account, $contact)
	{
		$accountId = $account->accountid;

		$this->logger->info("[isSeparateAccountForContact] Finding account $accountId contacts...");
		$contacts = $this->restApi->findAccountContacts($accountId);
		if (empty($contacts)) {
			$this->logger->info("[isSeparateAccountForContact] Account $accountId contacts not found. Separate account.");
			return true;
		}

		$accountCustomerId = $contact->parentcustomerid_account->accountnumber;
		$accountEmail = $contact->parentcustomerid_account->emailaddress1;
		$this->logger->info("[isSeparateAccountForContact] Found account $accountId contacts. Checking if there is contact with customer id/email differ from $accountCustomerId / $accountEmail");
		
		foreach ($contacts as $contact) {
			$contactId = $contact->contactid;
			$contactCustomerId = $contact->new_accountnumber;
			$contactEmail = $contact->emailaddress1;

			// Customer id and email should match exactly for contact and account.

			if ($contactCustomerId !== $accountCustomerId || $contactEmail !== $accountEmail) {
				$this->logger->info("[isSeparateAccountForContact] Account customer id and email $accountCustomerId / $accountEmail does not match contact $contactCustomerId / $contactEmail");
				return false;
			}
		}

		if (count($contacts) > 1) {
			$this->logger->info("[isSeparateAccountForContact] All contacts match account, but there is > 1 contact for account.");
			return false;
		}

		$this->logger->info("[isSeparateAccountForContact] All contacts match account and there is 1 contact for account.");
		return true;
	}
}