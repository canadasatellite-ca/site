<?php
 namespace Cart2Quote\Features\Feature\QuoteSections; final class Grid extends \Cart2Quote\License\Plugin\AbstractPlugin { public function aroundGetButtonHtml(\Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Grid $E1nC_, callable $v9Tn3, $NMClh, $ytPwc, $w228p = '', $RYyZh = null, $OMC24 = array()) { if (!(!$this->{"\151\x73\101\154\154\x6f\x77\x65\x64"}() && $RYyZh == "\163\145\143\x74\x69\157\156\163\137\160\x6f\160\x75\x70\x5f\151\144")) { goto ROdt7; } $MAZk1 = __("\x51\x75\x6f\164\145\40\x73\145\x63\164\151\x6f\x6e\163\40\141\162\x65\40\156\x6f\164\40\x61\x6c\154\x6f\167\x65\x64\40\146\x6f\x72\x20\164\150\x69\163\40\x6c\151\x63\145\156\x73\145\x2c\x20\x70\154\145\141\x73\x65\40\x75\160\147\x72\141\144\x65\40\x74\157\x20\x61\40\x68\151\x67\x68\x65\162\x20\x65\144\151\164\x69\x6f\156\x2e"); $shMaz = __("\124\150\x69\x73\40\141\x63\164\151\x6f\156\x20\151\x73\x20\x6e\x6f\x74\x20\141\154\x6c\157\167\145\x64"); $T5V2m = sprintf("\x3c\x64\151\166\40\x63\154\x61\x73\x73\75\x22\x6d\x65\163\163\x61\x67\145\163\x22\x3e\74\x64\151\166\x20\x63\154\x61\163\163\x3d\x22\155\x65\163\x73\141\147\x65\x20\155\145\x73\163\x61\147\145\x2d\145\x72\162\x6f\162\x20\x65\x72\x72\157\x72\42\x3e\x25\163\x3c\57\x64\151\x76\x3e\x3c\57\144\x69\x76\76", $MAZk1); $ytPwc = sprintf("\152\121\165\145\162\171\x28\47\x25\x73\x27\51\56\x61\x6c\145\x72\164\50\173\40\x74\151\164\154\145\72\x20\x27\45\163\x27\40\x7d\51\73", $T5V2m, $shMaz); $OMC24 = []; ROdt7: return $v9Tn3($NMClh, $ytPwc, $w228p, $RYyZh, $OMC24); } }