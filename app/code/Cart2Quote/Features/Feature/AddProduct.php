<?php
 namespace Cart2Quote\Features\Feature; final class AddProduct extends \Cart2Quote\License\Feature\AbstractFeature { protected $plugins = array("\103\x61\162\x74\x32\121\x75\x6f\164\145\134\121\165\x6f\164\141\x74\151\x6f\x6e\134\115\x6f\x64\x65\x6c\134\121\165\x6f\164\145" => array("\x70\154\x75\x67\x69\x6e\x73" => array("\141\144\144\x5f\160\162\x6f\144\165\x63\164" => array("\x73\x6f\x72\164\x4f\x72\144\145\x72" => 99999, "\x69\156\x73\164\x61\x6e\143\145" => \Cart2Quote\Features\Feature\AddProduct\Quote::class)))); protected function allowedStates() { return $this->{"\144\x65\146\141\165\x6c\x74\101\154\x6c\x6f\167\145\x64\x53\x74\x61\164\x65\163"}; } protected function allowedEdition() { return $this->{"\163\164\141\162\x74\x65\162"}; } }