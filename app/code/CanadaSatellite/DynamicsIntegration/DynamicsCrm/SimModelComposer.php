<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

class SimModelComposer {
	public function __construct() {

	}

	public function compose($sim) {
		$data = array();

		$accountId = $sim->getAccountId();
		if ($accountId) {
			$data['cs_accountid@odata.bind'] = "/accounts($accountId)";
		};

		$simStatus = $sim->getNetworkStatus();
		if ($simStatus) {
			$data['cs_simstatus'] = $simStatus;
		};

		$subStatus = $sim->getSubStatus();
		if ($subStatus) {
			$data['new_substatus'] = $subStatus;
		};

		$nickName = $sim->getNickname();
		if ($nickName) {
			$data['new_nickname'] = $nickName;
		};

		$simNumber = $sim->getSimNumber();
		if ($simNumber) {
			$data['cs_number'] = $simNumber;
		};

		$network = $sim->getNetwork();
		if ($network) {
			$data['cs_network'] = $network;
		};

		$type = $sim->getType();
		if ($type) {
			$data['cs_type'] = $type;
		};

		$service = $sim->getService();
		if ($service) {
			$data['cs_service'] = $service;
		};

		$plan = $sim->getPlan();
		if ($plan) {
			$data['cs_plan'] = $plan;
		};

		$currentMinutes = $sim->getCurrentMinutes();
		if ($currentMinutes) {
			$data['cs_currentminutes'] = $currentMinutes;
		};

		$satelliteNumber = $sim->getSatelliteNumber();
		if ($satelliteNumber) {
			$data['cs_satellitenumber'] = $satelliteNumber;
		};

		$dataNumber = $sim->getDataNumber();
		if ($dataNumber) {
			$data['cs_data'] = $dataNumber;
		};

		$activationDate = $sim->getActivationDate();
		if ($activationDate) {
			$data['cs_activationdate'] = $activationDate;
		};

		$expiryDate = $sim->getExpiryDate();
		if ($expiryDate) {
			$data['cs_expirydate'] = $expiryDate;
		};

		$quickNote = $sim->getQuickNote();
		if ($quickNote) {
			$data['new_quicknote'] = $quickNote;
		};

		return $data;
	}
}