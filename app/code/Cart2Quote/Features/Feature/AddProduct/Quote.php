<?php
 namespace Cart2Quote\Features\Feature\AddProduct; final class Quote extends \Cart2Quote\License\Plugin\AbstractPlugin { private $context; public function __construct() { $this->context = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Backend\App\Action\Context::class); } public final function aroundAddProducts(\Cart2Quote\Quotation\Model\Quote $Mib2c, callable $q2uuA, $hM5CP) { if ($this->{"\151\x73\x41\154\x6c\x6f\x77\145\x64"}()) { goto fOZg9; } $this->context->getMessageManager()->addComplexErrorMessage("\x6c\151\x63\x65\156\x73\145\x4d\145\x73\163\x61\147\x65", ["\155\x65\163\x73\x61\x67\x65" => "\x41\144\144\151\156\x67\x20\160\x72\x6f\144\x75\x63\164\163\x20\x69\163\x20\156\157\x74\40\141\x76\141\151\154\x61\x62\154\x65\40\x66\x6f\x72\x20\x74\150\151\x73\40\103\141\162\x74\x32\x51\165\x6f\164\x65\40\x6c\x69\x63\x65\156\x73\x65\x2e"]); $vKwOi = $this->context->getResultRedirectFactory()->create(); return $vKwOi->setRefererUrl(); fOZg9: return $q2uuA($hM5CP); } }