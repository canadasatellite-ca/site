<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote;

use Magento\Framework\App\ObjectManager;

/**
 * Class Email
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class Email extends \Cart2Quote\Quotation\Controller\Adminhtml\Quote
{
    /**
     * Notify user
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $quote = $this->_initQuote();
        if ($quote) {
            try {
                /**
                 * @var \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteSenderInterface $sender
                 */
                $sender = null;
                switch ($quote->getStatus()) {
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_OPEN:
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_NEW:
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_CHANGE_REQUEST:
                        $sender = ObjectManager::getInstance()->get(
                            \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender::class
                        );
                        break;
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_AUTO_PROPOSAL_SENT:
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROPOSAL_SENT:
                        $sender = ObjectManager::getInstance()->get(
                            \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalSender::class
                        );
                        break;
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_CANCELED:
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_OUT_OF_STOCK:
                        $sender = ObjectManager::getInstance()->get(
                            \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteCanceledSender::class
                        );
                        break;
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROPOSAL_EXPIRED:
                        $sender = ObjectManager::getInstance()->get(
                            \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalExpireSender::class
                        );
                        break;
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_ORDERED:
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_ACCEPTED:
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_CLOSED:
                        $sender = ObjectManager::getInstance()->get(
                            \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalAcceptedSender::class
                        );
                        break;
                    default:
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __('No e-mail available for this status')
                        );
                }

                if ($sender->send($quote)) {
                    $this->messageManager->addSuccess(__('You sent the quote email.'));
                } else {
                    $this->messageManager->addError(__('This e-mail is not enabled'));
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We can\'t send the email for the quote right now.'));
                $this->logger->critical($e);
            }
            return $this->resultRedirectFactory->create()->setPath(
                'quotation/quote/view',
                ['quote_id' => $quote->getId()]
            );
        }
        return $this->resultRedirectFactory->create()->setPath('quotation/*/');
    }

    /**
     * ACL check
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Cart2Quote_Quotation::email');
    }
}
