<?php
 namespace Cart2Quote\Features\Feature\AutoConfirmProposal; final class Customer extends \Cart2Quote\License\Plugin\AbstractPlugin { public final function afterIsAutoConfirm(\Cart2Quote\Quotation\Controller\Quote\Checkout\Customer $xbmtl, $neMhz) { if ($this->{"\151\163\101\154\x6c\x6f\167\145\144"}()) { goto ZsApT; } return false; ZsApT: return $neMhz; } }