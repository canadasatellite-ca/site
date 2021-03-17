<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Email;
use Zend_Mail_Exception;
/**
 * Trait SenderBuilder
 *
 * @package Cart2Quote\Quotation\Model\Quote\Email
 */
trait SenderBuilder
{
    /**
     * Prepare and send email message
     *
     * @param null|array $attachments
     * @throws \Magento\Framework\Exception\MailException
     */
    private function send(
        $attachments = null
    ) {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->transportBuilder = $this->uploadTransportBuilder;
        $attachedPart = $this->attachFiles($attachments);
        $this->configureEmailTemplate();
        if ($this->identityContainer->getRecieverEmail()) {
            $this->transportBuilder->addTo(
                $this->identityContainer->getRecieverEmail(),
                $this->identityContainer->getRecieverName()
            );
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We can\'t send an email to a quote without an email address.')
            );
        }
        $copyTo = $this->identityContainer->getEmailCopyTo();
        if (!empty($copyTo) && $this->identityContainer->getCopyMethod() == 'bcc') {
            foreach ($copyTo as $email) {
                $this->transportBuilder->addBcc($email);
            }
        }
        $transport = $this->transportBuilder->getMessage($attachedPart);
        $this->transportBuilder->resetUploadTransportBuilder();
        $transport->sendMessage();
		}
	}
    /**
     * Prepare and send copy email message
     *
     * @param null $attachments
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    private function sendCopyTo(
        $attachments = null
    ) {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$copyTo = $this->identityContainer->getEmailCopyTo();
        if (!empty($copyTo) && $this->identityContainer->getCopyMethod() == 'copy') {
            $this->transportBuilder = $this->uploadTransportBuilder;
            $attachedPart = $this->attachFiles($attachments);
            $this->configureEmailTemplate();
            foreach ($copyTo as $email) {
                $this->transportBuilder->addTo($email);
                $transport = $this->transportBuilder->getMessage($attachedPart);
                $this->transportBuilder->resetUploadTransportBuilder();
                $transport->sendMessage();
            }
        }
		}
	}
    /**
     * Attach files to email message
     *
     * @param array $attachments
     * @return array
     */
    private function attachFiles($attachments)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$attachedPart = [];
        $isMagetrendEnabled = $this->moduleManager->isEnabled('Magetrend_PdfCart2Quote');
        if ($isMagetrendEnabled && is_array($attachments)) {
            foreach ($attachments as $attachmentName => $attachmentPath) {
                if (!file_exists($attachmentPath)) {
                    $attachmentPathParts = explode('//', $attachmentPath);
                    if (is_array($attachmentPathParts) && isset($attachmentPathParts[1])) {
                        $magetrendAttachmentPath = "/" . $attachmentPathParts[1];
                        if (file_exists($magetrendAttachmentPath)) {
                            $attachedPart[] = $this->transportBuilder->attachFile($magetrendAttachmentPath, $attachmentName);
                        }
                    }
                } else {
                    $attachedPart[] = $this->transportBuilder->attachFile($attachmentPath, $attachmentName);
                }
            }
        }
        return $attachedPart;
		}
	}
    /**
     * Configure email template
     * Fix for Magento not setting the email From header. (Fixed in M2.1.x, >M2.3.0 and >M2.2.8)
     *
     * @return void
     */
    private function configureEmailTemplate()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			parent::configureEmailTemplate();
        try {
            //setFromByScope only exists in the final fixed version of magento (>M2.3.0 and >M2.2.8))
            if (!method_exists(\Magento\Framework\Mail\Template\TransportBuilder::class, 'setFromByScope')) {
                $this->transportBuilder->setFrom($this->identityContainer->getEmailIdentity());
            }
        } catch (Zend_Mail_Exception $exception) {
            //catch 'From Header set twice' error
            //That would mean that is Magento 2.1.x where this isn't an issue
        }
		}
	}
}
