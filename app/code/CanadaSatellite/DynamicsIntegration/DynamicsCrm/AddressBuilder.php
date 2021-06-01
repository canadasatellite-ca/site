<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

class AddressBuilder
{	
	function buildBillingAddressData($address)
	{
		$data = array();

		if ($address === null) {
			return $data;
		}

		$streetLines = $address->getStreet();
		if ($streetLines !== null) {
			if (isset($streetLines[0])) {
				$data['address1_line1'] = $streetLines[0];
			}
			if (isset($streetLines[1])) {
				$data['address1_line2'] = $streetLines[1];
			}
			if (isset($streetLines[2])) {
				$data['address1_line3'] = $streetLines[2];
			}
		}
		
		$city = $address->getCity();
		if ($city !== null) {
			$data['address1_city'] = $city;
		}

		$region = $address->getRegion();
		if ($region !== null) {
			$data['address1_stateorprovince'] = $region;
		}

		$postcode = $address->getPostcode();
		if ($postcode !== null) {
			$data['address1_postalcode'] = $postcode;
		}

		$country = $address->getCountry();
		if ($country !== null) {
			$data['address1_country'] = $country;
		}

		$phone = $address->getPhone();
		if ($phone !== null) {
			$data['address1_telephone1'] = $phone;
		}

		return $data;
	}
}