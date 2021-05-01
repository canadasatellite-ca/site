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

	public function __construct(
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

	public function getSimNumber() {
		return $this->cs_number;
	}

	public function getAccountId() {
		return $this->accountId;
	}

	public function getNetworkStatus() {
		return $this->simStatus;
	}

	public function getSubStatus() {
		return $this->subStatus;
	}

	public function getNickname() {
		return $this->nickName;
	}

	public function getNetwork() {
		return $this->network;
	}

	public function getType() {
		return $this->type;
	}

	public function getService() {
		return $this->service;
	}

	public function getPlan() {
		return $this->plan;
	}

	public function getCurrentMinutes() {
		return $this->currentMinutes;
	}

	public function getSatelliteNumber() {
		return $this->satelliteNumber;
	}

	public function getDataNumber() {
		return $this->dataNumber;
	}

	public function getActivationDate() {
		return $this->activationDate;
	}

	public function getExpiryDate() {
		return $this->expiryDate;
	}

	public function getQuickNote() {
		return $this->quickNote;
	}
}
?>