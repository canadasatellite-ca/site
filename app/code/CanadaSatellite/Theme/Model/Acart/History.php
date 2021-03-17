<?php

namespace CanadaSatellite\Theme\Model\Acart;

use Amasty\Acart\Model\History as ParentHistory;

class History extends ParentHistory
{
    /**
     * @var \Magento\Framework\Mail\MessageFactory
     */
    private $messageFactory;

    /**
     * @var \Magento\Framework\Mail\TransportInterfaceFactory
     */
    private $mailTransportFactory;

    /**
     * @var \Amasty\Acart\Model\Config
     */
    private $config;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Mail\TransportInterfaceFactory $mailTransportFactory,
        \Magento\Framework\Mail\MessageFactory $messageFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Amasty\Acart\Model\RuleQuoteFactory $ruleQuoteFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\SalesRule\Model\RuleFactory $salesRuleFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Acart\Model\Config $config,
        \Magento\Newsletter\Model\ResourceModel\Subscriber\Collection $newsletterSubscriberCollection,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        $this->messageFactory = $messageFactory;
        $this->mailTransportFactory = $mailTransportFactory;
        $this->config = $config;

        parent::__construct($context,
            $registry,
            $date,
            $dateTime,
            $mailTransportFactory,
            $messageFactory,
            $quoteFactory,
            $ruleQuoteFactory,
            $stockRegistry,
            $salesRuleFactory,
            $storeManager,
            $config,
            $newsletterSubscriberCollection,
            $resource,
            $resourceCollection,
            $data);
    }

    /**
     * @param bool $testMode
     */
    protected function _sendEmail($testMode = false)
    {
        $senderName = $this->config->getSenderName($this->getStoreId());
        $senderEmail = $this->config->getSenderEmail($this->getStoreId());
        $bcc = $this->config->getBcc($this->getStoreId());
        $safeMode = $this->config->isSafeMode($this->getStoreId());
        $recipientEmail = $this->config->getTestingRecipientEmail($this->getStoreId());
        $replyToEmail = $this->config->getReplyToEmail($this->getStoreId());
        $replyToName = $this->config->getReplyToName($this->getStoreId());

        $name = [
            $this->getCustomerFirstname(),
            $this->getCustomerLastname(),
        ];

        $to = $this->getCustomerEmail();

        if (($testMode || $safeMode) && $recipientEmail) {
            $to = $recipientEmail;
        }

        /** @var \Magento\Framework\Mail\Message $message */
        $message = $this->messageFactory->create();

        $message
            ->addTo($to, implode(' ', $name))
            ->setFrom($senderEmail, $senderName)
            ->setMessageType(\Magento\Framework\Mail\MessageInterface::TYPE_HTML)
            ->setBody($this->getEmailBody())
            ->setSubject(html_entity_decode($this->getEmailSubject(), ENT_QUOTES));

        if (!empty($bcc) && !$testMode && !$safeMode) {
            $message->addBcc(explode(',', $bcc));
        }

        if ($replyToEmail) {
            $message->setReplyTo($replyToEmail, $replyToName ? : '');
        }

        $message->setPartsToBody();

        $mailTransport = $this->mailTransportFactory->create(
            [
                'message' => $message
            ]
        );
        $mailTransport->sendMessage();
    }

}
