<?php
 namespace Cart2Quote\Features\Feature; final class AttachPdf extends \Cart2Quote\License\Feature\AbstractFeature { protected $configs = array("\x63\141\x72\x74\62\x71\165\x6f\x74\x65\x5f\145\x6d\x61\x69\x6c\57\x71\x75\157\164\145\x5f\160\162\x6f\x70\157\x73\x61\154\x2f\x61\x74\x74\141\x63\x68\137\x70\162\x6f\160\x6f\163\x61\154\x5f\160\x64\x66" => array("\x6e\x6f\x74\x41\154\x6c\x6f\167\145\x64\126\141\154\165\145" => array("\x31"), "\162\x65\166\145\x72\164\x56\x61\x6c\x75\x65" => "\60", "\x6d\145\x73\x73\x61\x67\145" => "\x54\150\145\40\141\x74\164\141\x63\x68\x20\160\144\x66\x20\x6f\x70\x74\x69\157\156\40\x69\x73\x20\x6e\157\164\x20\x61\166\x61\151\154\141\x62\154\x65\40\x66\157\x72\40\x74\x68\151\163\x20\103\141\x72\x74\62\121\165\x6f\164\x65\x20\154\x69\143\x65\x6e\x73\145\x2e")); protected $plugins = array("\103\141\x72\x74\62\x51\x75\157\164\145\x5c\x51\165\x6f\164\x61\164\x69\x6f\x6e\x5c\103\x6f\x6e\x74\162\x6f\x6c\154\x65\162\134\101\144\155\151\x6e\x68\164\155\x6c\x5c\x51\x75\x6f\164\145\x5c\120\x64\146" => array("\x70\x6c\x75\147\151\156\x73" => array("\141\164\x74\x61\143\150\x5f\x70\144\x66" => array("\163\x6f\x72\164\x4f\x72\144\145\x72" => 99999, "\x69\156\163\164\141\x6e\143\x65" => \Cart2Quote\Features\Feature\AttachPdf\Pdf::class))), "\x43\141\162\x74\x32\x51\165\157\164\145\x5c\121\165\x6f\x74\141\164\151\x6f\156\x5c\115\x6f\144\145\x6c\x5c\121\x75\x6f\164\x65\x5c\120\144\146\x5c\121\x75\157\x74\145" => array("\160\154\x75\x67\151\x6e\x73" => array("\x61\x74\164\141\x63\150\137\160\x64\x66" => array("\163\x6f\162\x74\x4f\162\x64\x65\162" => 99999, "\151\156\163\164\x61\156\143\145" => \Cart2Quote\Features\Feature\AttachPdf\Quote::class)))); protected final function allowedStates() { return $this->{"\144\145\146\x61\x75\154\x74\x41\x6c\x6c\x6f\167\x65\x64\x53\x74\141\x74\x65\x73"}; } protected final function allowedEdition() { return $this->{"\142\x75\x73\x69\156\145\x73\163"}; } }