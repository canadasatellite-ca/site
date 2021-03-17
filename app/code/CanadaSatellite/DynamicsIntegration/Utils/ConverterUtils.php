<?php

namespace CanadaSatellite\DynamicsIntegration\Utils;

class ConverterUtils
{
	/**
	 * Casts string to value preserving null value.
	 * @param string|null $value
	 * @return float
	 */
	public function toFloat($value)
	{
		if ($value === null) {
			return null;
		}

		return (float)$value;
	}
}
