<?php

namespace CanadaSatellite\DynamicsIntegration\Validators;

// Integrated circuit card identifier (SIM's primary account number) validator
class IccidValidator {
	private $checkAlgorithm;

	function __construct(\CanadaSatellite\DynamicsIntegration\Validators\Algorithms\LuhnChecksumAlgorithm $checkAlgorithm) {
		$this->checkAlgorithm = $checkAlgorithm;
	}

	function validateIccid($number) {
		return $this->checkAlgorithm->checkSum($number);
	}
}