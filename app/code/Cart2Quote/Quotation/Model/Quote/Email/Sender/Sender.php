<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Email\Sender;

/**
 * Class Sender
 *
 * @package Cart2Quote\Quotation\Model\Quote\Email\Sender
 */
class Sender extends \Cart2Quote\Quotation\Model\Quote\Email\AbstractSender implements QuoteSenderInterface
{
    use \Cart2Quote\Features\Traits\Model\Quote\Email\Sender\Sender {
        getSendEmailIdentifier as private traitGetSendEmailIdentifier;
        getEmailSentIdentifier as private traitGetEmailSentIdentifier;
        send as private traitSend;
        prepareTemplate as private traitPrepareTemplate;
        getPaymentHtml as private traitGetPaymentHtml;
    }

    /**
     * Application Event Dispatcher
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * Global configuration storage.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $globalConfig;

    /**
     * @var string
     */
    protected $sendEmailIdentifier;

    /**
     * @var string
     */
    protected $emailSentIdentifier;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Container\IdentityInterface
     */
    protected $identityContainer;

    /**
     * Sender constructor
     *
     * @param \Magento\Sales\Model\Order\Email\Container\Template $templateContainer
     * @param \Magento\Sales\Model\Order\Email\Container\Container $identityContainer
     * @param \Cart2Quote\Quotation\Model\Quote\Email\SenderBuilderFactory $senderBuilderFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Cart2Quote\Quotation\Model\Quote\Address\Renderer $addressRenderer
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig
     * @param \Cart2Quote\Quotation\Model\Quote\Pdf\Quote $pdfModel
     * @param string $sendEmailIdentifier
     * @param string $emailSentIdentifier
     */
    public function __construct(
        \Magento\Sales\Model\Order\Email\Container\Template $templateContainer,
        \Magento\Sales\Model\Order\Email\Container\Container $identityContainer,
        \Cart2Quote\Quotation\Model\Quote\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        \Cart2Quote\Quotation\Model\Quote\Address\Renderer $addressRenderer,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig,
        \Cart2Quote\Quotation\Model\Quote\Pdf\Quote $pdfModel,
        $sendEmailIdentifier = '',
        $emailSentIdentifier = ''
    ) {
        parent::__construct(
            $templateContainer,
            $identityContainer,
            $senderBuilderFactory,
            $logger,
            $addressRenderer,
            $pdfModel
        );
        $this->eventManager = $eventManager;
        $this->globalConfig = $globalConfig;
        $this->sendEmailIdentifier = $sendEmailIdentifier;
        $this->emailSentIdentifier = $emailSentIdentifier;
        $this->identityContainer = $identityContainer;
    }

    /**
     * Get Sender email identifier
     *
     * @return string
     */
    public function getSendEmailIdentifier()
    {
        return $this->traitGetSendEmailIdentifier();
    }

    /**
     * Get email sender identifier
     *
     * @return string
     */
    public function getEmailSentIdentifier()
    {
        return $this->traitGetEmailSentIdentifier();
    }

    /**
     * Sends quote request email to the customer.
     * - Email will be sent immediately in two cases:
     * - - if asynchronous email sending is disabled in global settings
     * - - if $forceSyncMode parameter is set to TRUE
     * - Otherwise, email will be sent later during running of
     * - corresponding cron job.
     * @used-by \Interactivated\Quotecheckout\Controller\Index\Updateordermethod::execute()
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param bool $forceSyncMode
     * @return bool
     */
    public function send(\Cart2Quote\Quotation\Model\Quote $quote, $forceSyncMode = false)
    {
        return $this->traitSend($quote, $forceSyncMode);
    }

    /**
     * Prepare email template with variables
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return void
     */
    protected function prepareTemplate(\Cart2Quote\Quotation\Model\Quote $quote)
    {
        $this->traitPrepareTemplate($quote);
    }

    /**
     * Get payment info block as html
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return string
     */
    protected function getPaymentHtml(\Cart2Quote\Quotation\Model\Quote $quote)
    {
        return $this->traitGetPaymentHtml($quote);
    }
}
