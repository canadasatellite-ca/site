<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Email;

use Magento\Email\Model\AbstractTemplate;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\AddressConverter;
use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\MimeMessageInterfaceFactory;
use Magento\Framework\Mail\MimePartInterfaceFactory;

/**
 * Class UploadTransportBuilder
 *
 * @package Cart2Quote\Quotation\Model\Quote\Email
 */
class UploadTransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    use \Cart2Quote\Features\Traits\Model\Quote\Email\UploadTransportBuilder {
        addCc as private traitAddCc;
        addTo as private traitAddTo;
        addBcc as private traitAddBcc;
        setReplyTo as private traitSetReplyTo;
        setFrom as private traitSetFrom;
        setFromByScope as private traitSetFromByScope;
        attachFile as private traitAttachFile;
        getMessage as private traitGetMessage;
        resetUploadTransportBuilder as private traitResetUploadTransportBuilder;
        setTemplateFilter as private traitSetTemplateFilter;
        prepareQuoteMessage as private traitPrepareQuoteMessage;
        addAddressByType as private traitAddAddressByType;
        reset as private traitReset;
    }

    /**
     * @var \Cart2Quote\Quotation\Model\Email\ZendAdapter
     */
    private $zendAdapter;

    /**
     * @var EmailMessageInterfaceFactory
     */
    private $emailMessageInterfaceFactory;

    /**
     * Param that used for storing all message data until it will be used
     *
     * @var array
     */
    private $messageData = [];

    /**
     * Template data
     *
     * @var array
     */
    protected $templateData = [];

    /**
     * @var MimeMessageInterfaceFactory
     */
    private $mimeMessageInterfaceFactory;

    /**
     * @var MimePartInterfaceFactory
     */
    private $mimePartInterfaceFactory;

    /**
     * @var AddressConverter|null
     */
    private $addressConverter;

    /**
     * UploadTransportBuilder constructor.
     *
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\Mail\Template\FactoryInterface $templateFactory
     * @param \Magento\Framework\Mail\MessageInterface $message
     * @param \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Mail\TransportInterfaceFactory $mailTransportFactory
     * @param \Magento\Framework\Mail\MessageInterfaceFactory|null $messageFactory
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Mail\Template\FactoryInterface $templateFactory,
        \Magento\Framework\Mail\MessageInterface $message,
        \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Mail\TransportInterfaceFactory $mailTransportFactory,
        \Magento\Framework\Mail\MessageInterfaceFactory $messageFactory = null
    ) {
        if (method_exists(\Magento\Framework\Mail\Message::class, 'addAttachment')) {
            parent::__construct(
                $templateFactory,
                $message,
                $senderResolver,
                $objectManager,
                $mailTransportFactory
            );

            $this->zendAdapter = $objectManager->get(\Cart2Quote\Quotation\Model\Email\ZendOne::class);
            $this->productMetadata = $productMetadata;
        } elseif (class_exists('Magento\Framework\Mail\MimeMessage')) {
            parent::__construct(
                $templateFactory,
                $message,
                $senderResolver,
                $objectManager,
                $mailTransportFactory,
                $messageFactory
            );

            $this->zendAdapter = $objectManager->get(\Cart2Quote\Quotation\Model\Email\ZendTree::class);
            $this->emailMessageInterfaceFactory = $objectManager->get(EmailMessageInterfaceFactory::class);
            $this->mimeMessageInterfaceFactory = $objectManager->get(MimeMessageInterfaceFactory::class);
            $this->mimePartInterfaceFactory = $objectManager->get(MimePartInterfaceFactory::class);
            $this->addressConverter = $objectManager->get(AddressConverter::class);
        } else {
            parent::__construct(
                $templateFactory,
                $message,
                $senderResolver,
                $objectManager,
                $mailTransportFactory,
                $messageFactory
            );

            $this->zendAdapter = $objectManager->get(\Cart2Quote\Quotation\Model\Email\ZendTwo::class);
        }
    }

    /**
     * Add cc address
     *
     * @param array|string $address
     * @param string $name
     *
     * @return \Magento\Framework\Mail\Template\TransportBuilder
     * @throws MailException
     */
    public function addCc($address, $name = '')
    {
        return $this->traitAddCc($address, $name);
    }

    /**
     * Add to address
     *
     * @param array|string $address
     * @param string $name
     *
     * @return $this
     * @throws MailException
     */
    public function addTo($address, $name = '')
    {
        return $this->traitAddTo($address, $name);
    }

    /**
     * Add bcc address
     *
     * @param array|string $address
     *
     * @return $this
     * @throws MailException
     */
    public function addBcc($address)
    {
        return $this->traitAddBcc($address);
    }

    /**
     * Set Reply-To Header
     *
     * @param string $email
     * @param string|null $name
     *
     * @return $this
     * @throws MailException
     */
    public function setReplyTo($email, $name = null)
    {
        return $this->traitSetReplyTo($email, $name);
    }

    /**
     * Set mail from address
     *
     * @param string|array $from
     *
     * @return $this
     * @throws MailException
     * @see setFromByScope()
     */
    public function setFrom($from)
    {
        return $this->traitSetFrom($from);
    }

    /**
     * Set mail from address by scopeId
     *
     * @param string|array $from
     * @param string|int $scopeId
     *
     * @return $this
     * @throws MailException
     */
    public function setFromByScope($from, $scopeId = null)
    {
        return $this->traitSetFromByScope($from, $scopeId);
    }

    /**
     * Function to attach a file to an outgoing email
     *
     * @param string $file
     * @param string $name
     * @return array
     */
    public function attachFile($file, $name)
    {
        return $this->traitAttachFile($file, $name);
    }

    /**
     * Get mail message
     *
     * @param array $attachedPart
     * @return \Magento\Framework\Mail\TransportInterface
     */
    public function getMessage($attachedPart)
    {
        return $this->traitGetMessage($attachedPart);
    }

    /**
     * Reset UploadTransportBuilder object state
     */
    public function resetUploadTransportBuilder()
    {
        $this->traitResetUploadTransportBuilder();
    }

    /**
     * Sets up template filter
     *
     * @param AbstractTemplate $template
     *
     * @return void
     */
    protected function setTemplateFilter(AbstractTemplate $template)
    {
        $this->traitSetTemplateFilter($template);
    }

    /**
     * @param array $attachedPart
     * @param string $body
     * @return \Magento\Framework\Mail\TransportInterface
     */
    protected function prepareQuoteMessage($attachedPart, $body)
    {
        return $this->traitPrepareQuoteMessage($attachedPart, $body);
    }

    /**
     * Handles possible incoming types of email (string or array)
     * Note: addressConverter is only set when on Magento 2.3.3+
     *
     * @param string $addressType
     * @param string|array $email
     * @param string|null $name
     *
     * @return void
     * @throws MailException
     */
    private function addAddressByType(string $addressType, $email, $name = null)
    {
        $this->traitAddAddressByType($addressType, $email, $name);
    }

    /**
     * Reset object state
     *
     * @return $this|\Magento\Framework\Mail\Template\TransportBuilder
     */
    protected function reset()
    {
        return $this->traitReset();
    }
}
