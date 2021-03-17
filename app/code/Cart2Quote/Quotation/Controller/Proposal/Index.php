<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Proposal;

use Magento\Framework\App\ResponseInterface;

/**
 * Class Index
 *
 * @package Cart2Quote\Quotation\Controller\Proposal
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \Cart2Quote\Quotation\Model\Session $quotationSession
     */
    protected $quotationSession;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote|\Cart2Quote\Quotation\Model\QuoteRepository $quotationQuote
     */
    protected $quotationQuote;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalRejectSender $rejectEmailSender
     */
    protected $rejectEmailSender;

    /**
     * @var Context $context
     */
    protected $context;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param \Cart2Quote\Quotation\Model\Quote $quotationQuote
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalRejectSender $rejectEmailSender
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Cart2Quote\Quotation\Model\QuoteRepository $quotationQuote,
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalRejectSender $rejectEmailSender
    ) {
        parent::__construct($context);
        $this->request = $context->getRequest();
        $this->pageFactory = $pageFactory;
        $this->quotationSession = $quotationSession;
        $this->quotationQuote = $quotationQuote;
        $this->rejectEmailSender = $rejectEmailSender;
        $this->context = $context;
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $params = $this->request->getParams();
        try {
            $quotation = $this->quotationQuote->get($params['quote_id']);
            $quotation->setRejectMessage($params['reason']);
            $quotation->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROPOSAL_REJECTED);
            $quotation->setState(\Cart2Quote\Quotation\Model\Quote\Status::STATE_CANCELED);
            $quotation->save();
            $this->sendRejectionEmail($quotation);

            return $resultRedirect->setPath(
                'quotation/proposal/success/quote_id/',
                ['quote_id' => $this->_request->getParam(
                    'quote_id'
                )]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('quotation/quote/history');
        }
    }

    /**
     * @param \Cart2Quote\Quotation\Model\Quote $quotation
     */
    public function sendRejectionEmail($quotation)
    {
        $this->rejectEmailSender->send($quotation);
    }
}
