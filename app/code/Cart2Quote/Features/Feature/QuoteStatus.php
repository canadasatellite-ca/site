<?php
 namespace Cart2Quote\Features\Feature; final class QuoteStatus extends \Cart2Quote\License\Feature\AbstractFeature { protected $plugins = array("\x43\x61\162\x74\x32\x51\165\x6f\164\145\134\121\x75\157\x74\141\164\x69\x6f\156\x5c\103\x6f\x6e\x74\162\x6f\x6c\154\x65\x72\x5c\x41\x64\155\x69\156\x68\164\x6d\154\134\x51\165\x6f\164\x65\x5c\123\141\x76\145" => array("\160\154\165\147\x69\156\163" => array("\161\165\x6f\164\x65\x5f\x73\x74\141\x74\165\163" => array("\x73\157\x72\x74\117\x72\x64\x65\162" => 99999, "\x69\156\163\x74\141\x6e\143\x65" => \Cart2Quote\Features\Feature\QuoteStatus\Save::class)))); protected function allowedStates() { return $this->{"\x64\145\x66\141\x75\x6c\x74\101\x6c\154\x6f\167\x65\144\123\x74\141\x74\145\x73"}; } protected function allowedEdition() { return $this->{"\x65\x6e\164\x65\x72\160\162\151\163\145"}; } }