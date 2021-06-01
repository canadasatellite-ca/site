<?php

namespace CanadaSatellite\DynamicsIntegration\Model;

class Sim
{
	private $cs_number;
	private $accountId;
	private $simStatus;
	private $subStatus;
	private $nickName;
	private $network;
	private $type;
	private $service;
	private $plan;
	private $currentMinutes;
	private $satelliteNumber;
	private $dataNumber;
	private $activationDate;
	private $expiryDate;
	private $quickNote;

	function __construct(
		$cs_number,
		$accountId = null,
		$simStatus = null,
		$subStatus = null,
		$nickName = null,
		$network = null,
		$type = null,
		$service = null,
		$plan = null,
		$currentMinutes = null,
		$satelliteNumber = null,
		$dataNumber = null,
		$activationDate = null,
		$expiryDate = null,
		$quickNote = null
	) {
		$this->cs_number = $cs_number;
		$this->accountId = $accountId;
		$this->simStatus = $simStatus;
		$this->subStatus = $subStatus;
		$this->nickName = $nickName;
		$this->network = $network;
		$this->type = $type;
		$this->service = $service;
		$this->plan = $plan;
		$this->currentMinutes = $currentMinutes;
		$this->satelliteNumber = $satelliteNumber;
		$this->dataNumber = $dataNumber;
		$this->activationDate = $activationDate;
		$this->expiryDate = $expiryDate;
		$this->quickNote = $quickNote;
	}

	function getSimNumber() {
		return $this->cs_number;
	}

	function getAccountId() {
		return $this->accountId;
	}

	function getNetworkStatus() {
		return $this->simStatus;
	}

	function getSubStatus() {
		return $this->subStatus;
	}

	function getNickname() {
		return $this->nickName;
	}

	function getNetwork() {
		return $this->network;
	}

	function getType() {
		return $this->type;
	}

	function getService() {
		return $this->service;
	}

	function getPlan() {
		return $this->plan;
	}

	function getCurrentMinutes() {
		return $this->currentMinutes;
	}

	function getSatelliteNumber() {
		return $this->satelliteNumber;
	}

	function getDataNumber() {
		return $this->dataNumber;
	}

	function getActivationDate() {
		return $this->activationDate;
	}

	function getExpiryDate() {
		return $this->expiryDate;
	}

	function getQuickNote() {
		return $this->quickNote;
	}
}
?>