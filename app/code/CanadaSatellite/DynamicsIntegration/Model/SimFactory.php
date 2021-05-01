<?php

namespace CanadaSatellite\DynamicsIntegration\Model;

class SimFactory
{
	public function __construct() {

	}

	public function create($simNumber, $accountId)	{
		if ($simNumber === null) {
			return null;
		}

		$sim = new Sim($simNumber, $accountId);
		return $sim;
	}
}