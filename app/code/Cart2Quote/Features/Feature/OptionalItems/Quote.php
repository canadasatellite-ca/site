<?php
 namespace Cart2Quote\Features\Feature\OptionalItems; final class Quote extends \Cart2Quote\License\Plugin\AbstractPlugin { private $context; public function __construct() { $this->context = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Backend\App\Action\Context::class); } public final function aroundExecute(\Cart2Quote\Quotation\Controller\Adminhtml\Quote $Ei0KW, callable $h20zE) { if ($this->{"\151\163\101\154\154\157\x77\145\x64"}()) { goto VierE; } $cYiw5 = $Ei0KW->getRequest()->getPost("\x69\164\x65\x6d", []); $MEUQ1 = false; foreach ($cYiw5 as &$Y_I27) { if (!isset($Y_I27["\164\x69\x65\x72\137\x69\x74\x65\155"])) { goto kuZ6R; } foreach ($Y_I27["\x74\151\x65\162\137\151\164\145\155"] as &$D4d4X) { if (!(isset($D4d4X["\x6d\x61\x6b\x65\x5f\157\160\x74\151\x6f\x6e\141\x6c"]) && $D4d4X["\155\x61\x6b\145\x5f\157\160\164\151\x6f\x6e\141\154"] == "\x6f\156")) { goto zeCqm; } unset($D4d4X["\155\x61\x6b\145\x5f\157\x70\164\x69\157\156\x61\154"]); $MEUQ1 = true; zeCqm: fGsam: } ykuKn: kuZ6R: guZP2: } T3eEw: if (!$MEUQ1) { goto dUP3L; } $this->context->getMessageManager()->addComplexErrorMessage("\x6c\x69\143\x65\x6e\163\145\115\145\x73\x73\x61\147\145", ["\x6d\145\163\x73\141\147\x65" => "\117\x70\x74\x69\157\156\x61\154\x20\x69\164\145\x6d\163\40\x61\162\145\x20\x6e\157\x74\x20\141\166\141\x69\154\x61\142\154\x65\x20\x66\157\x72\x20\164\x68\151\163\x20\103\141\162\164\x32\121\165\157\164\x65\40\x6c\151\143\145\x6e\163\145\56"]); dUP3L: $Ei0KW->getRequest()->setPostValue("\151\164\145\x6d", $cYiw5); VierE: return $h20zE(); } }