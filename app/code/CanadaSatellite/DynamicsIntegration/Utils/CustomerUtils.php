<?php

namespace CanadaSatellite\DynamicsIntegration\Utils;

class CustomerUtils
{
	private $websiteRepository;
	private $groupRepository;
	private $logger;

	function __construct(
		\Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository,
		\Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->groupRepository = $groupRepository;
		$this->websiteRepository = $websiteRepository;
		$this->logger = $logger;
	}

	function getSource($customer)
	{
		$websiteId = $customer->getWebsiteId();
		if ($websiteId === null) {
			return null;
		}

		$website = $this->websiteRepository->getById($websiteId);
		$code = $website->getCode();
		return $code;
	}

	function getGroup($customer) {
		$groupId = $customer->getGroupId();
		if ($groupId === null) {
			return null;
		}

		$group = $this->groupRepository->getById($groupId);
		return $group->getCode();
	}

	function getGender($customer) {
		$gender = $customer->getGender();
		if ($gender === null) {
			return null;
		}

		// Intentional using of weak comparision
		if ($gender != 1 && $gender != 2) {
			return null;
		}

		return $gender;
	}

	function getBirthDate($customer) {
		$birthDate = $customer->getDob();
		if ($birthDate === null) {
			return null;
		}

		$date = \DateTime::createFromFormat('Y-m-d+', $birthDate);
		if ($date === false) {
			$errors = \DateTime::getLastErrors();
			$msg = json_encode($errors);
			$this->logger->err("Error while parsing '$birthDate' date: $msg");
			return null;
		}

		return $date->format('Y-m-d');
	}
}
