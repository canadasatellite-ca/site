<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Email;

use Zend_Mail_Exception;

/**
 * Class SenderBuilder
 *
 * @package Cart2Quote\Quotation\Model\Quote\Email
 */
class SenderBuilder extends \Magento\Sales\Model\Order\Email\SenderBuilder
{
    use \Cart2Quote\Features\Traits\Model\Quote\Email\SenderBuilder {
        send as private traitSend;
        sendCopyTo as private traitSendCopyTo;
        attachFiles as private traitAttachFiles;
        configureEmailTemplate as private traitConfigureEmailTemplate;
    }

    /**
     * @var \Magento\Sales\Model\Order\Email\Container\Template
     */
    protected $templateContainer;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Container\IdentityInterface
     */
    protected $identityContainer;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\UploadTransportBuilder
     */
    protected $uploadTransportBuilder;

    /**
     * Sender resolver
     *
     * @var \Magento\Framework\Mail\Template\SenderResolverInterface
     */
    protected $senderResolver;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * SenderBuilder constructor
     *
     * @param \Cart2Quote\Quotation\Model\Quote\Email\UploadTransportBuilder $uploadTransportBuilder
     * @param \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver
     * @param \Magento\Sales\Model\Order\Email\Container\Template $templateContainer
     * @param \Magento\Sales\Model\Order\Email\Container\IdentityInterface $identityContainer
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        UploadTransportBuilder $uploadTransportBuilder,
        \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver,
        \Magento\Sales\Model\Order\Email\Container\Template $templateContainer,
        \Magento\Sales\Model\Order\Email\Container\IdentityInterface $identityContainer,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        if (class_exists(\Magento\Framework\Mail\Template\TransportBuilderByStore::class)) {
            parent::__construct(
                $templateContainer,
                $identityContainer,
                $transportBuilder,
                \Magento\Framework\App\ObjectManager::getInstance()->create(
                    \Magento\Framework\Mail\Template\TransportBuilderByStore::class
                )
            );
        } else {
            parent::__construct(
                $templateContainer,
                $identityContainer,
                $transportBuilder
            );
        }

        $this->templateContainer = $templateContainer;
        $this->identityContainer = $identityContainer;
        $this->transportBuilder = $transportBuilder;
        $this->uploadTransportBuilder = $uploadTransportBuilder;
        $this->senderResolver = $senderResolver;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Prepare and send email message
     *
     * @param null|array $attachments
     * @throws \Magento\Framework\Exception\MailException
     */
    public function send(
        $attachments = null
    ) {
        $this->traitSend($attachments);
    }

    /**
     * Prepare and send copy email message
     *
     * @param null $attachments
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendCopyTo(
        $attachments = null
    ) {
        $this->traitSendCopyTo($attachments);
    }

    /**
     * Attach files to email message
     *
     * @param array $attachments
     * @return array
     */
    public function attachFiles($attachments)
    {
        return $this->traitAttachFiles($attachments);
    }

    /**
     * Configure email template
     * Fix for Magento not setting the email From header. (Fixed in M2.1.x, >M2.3.0 and >M2.2.8)
     *
     * @return void
     */
    protected function configureEmailTemplate()
    {
        $this->traitConfigureEmailTemplate();
    }
}
