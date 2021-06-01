<?php

namespace CanadaSatellite\DynamicsIntegration\Validators\Algorithms;

class LuhnChecksumAlgorithm {
	function calcCheckDigit($srcNumber) {

	}

	function checkSum($srcNumber) {
		$sum = 0;
		$srcLength = strlen($srcNumber);

		for ($i = -1; $i >= 0 - $srcLength; $i--) {
			$digit = substr($srcNumber, $i, 1);
			if ($i % 2 == 0) {
				$doubledDigit = $digit * 2;
				if ($doubledDigit > 9) {
					$doubledDigit -= 9;
				}
				$sum += $doubledDigit;
			} else {
				$sum += $digit;
			}
		}

		return $sum % 10 == 0;
	}
}