<?php

namespace CanadaSatellite\DynamicsIntegration\Model;

class SimFactory
{
	function __construct() {

	}

	function create($simNumber, $accountId)	{
		if ($simNumber === null) {
			return null;
		}

		$sim = new Sim($simNumber, $accountId);
		return $sim;
	}
}