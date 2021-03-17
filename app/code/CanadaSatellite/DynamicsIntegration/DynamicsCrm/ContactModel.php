<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

class ContactModel {
	// Account Number
	private $new_accountnumber;

	// Salutation
	private $salutation;

	// First Name
	private $firstname;

	// Middle Name
	private $middlename;

	// Last Name
	private $lastname;

	// Company Name
	private $parentcustomerid;

	// Business Phone
	private $telephone1;

	// Fax
	private $fax;

	// Email
	private $emailaddress1;

	// Website
	private $websiteurl;

	// Gender
	private $gendercode;

	// Birthday
	private $birthdate;

	/* Address */

	// Same As Ship To
	private $new_sameasshipto;

	// Address Type
	private $address1_addresstypecode;

	// Address Name
	private $address1_name;

	// Street
	private $address1_line1;

	// Street 2
	private $address1_line2;

	// Street 3
	private $address1_line3;

	// City
	private $address1_city;

	// State/Province
	private $address1_stateorprovince;

	// Zip/Postal Code
	private $address1_postalcode;

	// Country/Region
	private $address1_country;

	// Phone
	private $address1_telephone1;
}