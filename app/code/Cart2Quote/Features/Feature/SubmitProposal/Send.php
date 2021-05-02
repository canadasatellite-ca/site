<?php

namespace Cart2Quote\Features\Feature\SubmitProposal;
final class Send extends \Cart2Quote\License\Plugin\AbstractPlugin
{
	private $context;

	public function __construct()
	{
		$this->context = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Backend\App\Action\Context::class);
	}

	public final function aroundExecute(\Cart2Quote\Quotation\Controller\Adminhtml\Quote\Send $HA35W, callable $VbjNE)
	{
		if (!(!$this->{"\151\x73\101\154\x6c\x6f\167\x65\144"}() || $this->isExceeded())) {
			goto Gak2b;
		}
		$dS81T = "\131\x6f\165\x20\150\141\166\x65\40\x72\145\141\143\x68\x65\144\40\164\x68\145\40\155\141\170\x69\155\165\x6d\x20\156\x75\155\x62\x65\x72\40\x6f\146\x20\x70\x72\157\x70\x6f\x73\x61\x6c\163\x20\x74\150\x69\x73\x20\155\157\x6e\164\150\40\50" . \Cart2Quote\License\Model\License::ALLOWED_PROPOSAL_AMOUNT . "\51\40\x61\154\x6c\x6f\167\145\144\40\151\x6e\40\164\150\x65\x20\154\151\164\x65\x20\x70\x6c\141\x6e\x2e\40" . "\x54\x6f\40\163\145\x6e\144\40\x75\156\x6c\x69\155\x69\x74\145\x64\x20\160\162\157\x70\x6f\163\141\x6c\x73\40\160\154\145\x61\163\x65\x20\165\x70\147\162\141\x64\145\x2e";
		$this->context->getMessageManager()->addComplexErrorMessage("\x6c\x69\143\x65\x6e\x73\x65\115\145\x73\163\x61\147\x65", ["\155\145\163\x73\x61\x67\x65" => $dS81T]);
		$ZEbK0 = $this->context->getResultRedirectFactory()->create();
		return $ZEbK0->setRefererUrl();
		Gak2b:
		if (!(\Cart2Quote\License\Model\License::getInstance()->getEdition() == "\x6c\151\x74\145")) {
			goto tqT3R;
		}
		\Cart2Quote\License\Model\License::getInstance()->setProposalSent();
		tqT3R:
		return $VbjNE();
	}

	private final function isExceeded()
	{
		return \Cart2Quote\License\Model\License::getInstance()->getProposalAmount() >= \Cart2Quote\License\Model\License::ALLOWED_PROPOSAL_AMOUNT;
	}
}