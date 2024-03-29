<?php
namespace Cart2Quote\Features\Feature;
use Cart2Quote\Features\Feature\ApiAccess\Rest;
use Cart2Quote\Features\Feature\ApiAccess\Soap;
# 2021-10-12 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
# "Refactor the `Cart2Quote_Features` module": https://github.com/canadasatellite-ca/site/issues/250
final class ApiAccess extends \Cart2Quote\License\Feature\AbstractFeature {
	protected $plugins = [
		"\x4d\141\147\145\156\164\157\x5c\x57\145\142\141\160\x69\134\x43\157\x6e\x74\162\x6f\154\154\145\x72\134\123\157\141\160" => [
			"\x70\x6c\x75\x67\x69\156\163" => [
				"\x61\160\x69\x5f\x61\x63\x63\x65\x73\163" => [
					"\163\157\x72\164\x4f\162\x64\x65\162" => 99999, "\151\156\163\x74\141\x6e\x63\x65" => Soap::class
				]
			]
		]
		,"\115\x61\x67\145\156\164\157\x5c\x57\x65\x62\141\x70\x69\x5c\103\x6f\x6e\x74\x72\x6f\x6c\154\145\162\134\122\145\163\164" => [
			"\x70\x6c\x75\147\x69\x6e\163" => [
				"\141\160\x69\137\x61\143\x63\x65\x73\x73" => [
					"\x73\157\162\164\117\162\x64\145\x72" => 99999, "\151\156\163\x74\141\x6e\143\x65" => Rest::class
				]
			]
		]
	];
	protected function allowedStates() {return
		$this->{"\144\145\146\x61\165\154\164\x41\x6c\x6c\157\x77\145\144\123\164\141\164\145\163"}
	;}
	protected function allowedEdition(){return $this->{"\145\156\164\x65\x72\160\x72\x69\163\x65"};}
}