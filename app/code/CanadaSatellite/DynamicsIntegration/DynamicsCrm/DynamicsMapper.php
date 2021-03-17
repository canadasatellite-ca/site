<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

class DynamicsMapper
{
	public function mapAccountSource($source)
	{
		switch ($source) {
			case 'base':
				return 100000000;
			case 'africasatellite':
				return 100000007;
			case 'australiasatellite':
				return 100000009;
			case 'asiasatellite':
				return 100000008;
			case 'europasatellite':
				return 100000006;
			case 'calgarysatellite':
				return 100000001;
			case 'americansatellite':
				return 100000003;
			case 'universalrv_ca':
				return 100000010;
			case 'universalmaritime_ca':
				return 100000011;
			case 'satelliterentals_ca':
				return 100000013;
			case 'oilsat_ca':
				return 100000012;
			default:
				return null;
		}
	}

	public function mapNetwork($network) 
	{
		switch (strtolower($network)) {
			case 'bell tv':
				return 100800000;
			case 'globalstar':
				return 100300000;
			case 'inmarsat':
				return 100200000;
			case 'iridium':
				return 100100000;
			case 'kvh':
				return 100400000;
			case 'shaw direct':
				return 100900000;
			case 'telus satellite tv':
				return 101000000;
			case 'thuraya':
				return 100500000;
			case 'VSAT':
				return 100600000;

			case 'brazil claro':
			case 'directv':
			case 'directv latin america':
			case 'dish network':
			case 'sky brazil':
			case 'sky mexico':
				// Other
				return 100700000;

			default:
				// Other
				return 100700000;
		}
	}

	public function mapProductCategory($category)
	{
		switch (strtolower($category)) {
			case 'satellite phone':
				return 100001000;
			case 'satellite internet':
				return 100002000;
			case 'satellite tv':
				return 100005000;
			case 'accessories':
				return 100006000;

			case 'satellite tracking':
				// Other
				return 100009000;

			default:
				// Other
				return 100009000;
		}
	}

	public function mapServiceType($service)
	{
		switch (strtolower($service)) {
			case 'globalstar voice':
				return 100310000;
			case 'inmarsat voice':
				return 100210000;
			case 'inmarsat isathub':
				return 100260000;
			case 'inmarsat bgan':
				return 100220000;
			case 'inmarsat bgan m2m':
				return 100230000;

			case 'inmarsat bgan hdr':
			case 'inmarsat bgan link':
			case 'inmarsat fleetbroadband':
			case 'inmarsat isatphone link':
				// other
				return 100000004;

			case 'inmarsat gx':
				return 100250000;
			case 'inmarsat fleet xpress (fx)':
				// other
				return 100000004;

			case 'iridium voice':
				return 100110000;
			case 'iridium go!':
				return 100120000;
			case 'iridium ptt':
				return 100000000;
			case 'iridium sbd':
				return 100130000;
			case 'iridium certus':
				// iridium other
				return 100170000;

			case 'thuraya voice':
				return 100510000;
			case 'thuraya ip':
				return 100520000;

			case 'vsat':
			case 'avanti':
			case 'eutelsat':
			case 'hylas':
			case 'idirect':
			case 'intelsat':
			case 'nera':
			case 'starband':
			case 'viasat':
			case 'c band':
			case 'ku band':
			case 'ka band':
				// Other
				return 100000004;

			default:
				// Other
				return 100000004;
		}
	}

	public function mapCountryOfOrigin($country)
	{
		switch(strtolower($country)) {
			case 'canada':
				return 100000000;
			case 'united states':
				return 100000001;

			default:
				return null;
		}
	}

	public function mapWarranty($warranty)
	{
		switch (strtolower($warranty)) {
			case '12 months':
				return 100000000;						
			case '24 months':
				return 100000001;
			case '36 months':
				return 100000002;

			case '18 months':
			case '12 months on lnb':
			case '2 years parts + 1 year labor':
			case '3 years parts + 2 year labor':
			case 'lifetime':
				return null;

			default:
				return null;
		}
	}

	public function mapDatetime($datetime)
	{
		return $datetime->format('Y-m-d\TH:i:s\Z');
	}
}
