<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Email;
/**
 * Trait ZendTwo
 *
 * @package Cart2Quote\Quotation\Model\Email
 */
trait ZendTwo
{
    /**
     * Get attach file adpater
     *
     * @param string $file
     * @param string $name
     * @return \Zend\Mime\Part
     */
    private function attachFileAdapter($file, $name)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!empty($file) && file_exists($file)) {
            $fileContents = fopen($file, 'r');
            $attachment = new \Zend\Mime\Part($fileContents);
            $attachment->type = \Zend\Mime\Mime::TYPE_OCTETSTREAM;
            $attachment->encoding = \Zend\Mime\Mime::ENCODING_BASE64;
            $attachment->disposition = \Zend\Mime\Mime::DISPOSITION_ATTACHMENT;
            $attachment->filename = $name;
            return $attachment;
        }
		}
	}
    /**
     * Get message adapter
     *
     * @param array $attachedPart
     * @param string $body
     * @param \Magento\Framework\Mail\Message|\Zend\Mime\Message|null $message
     */
    private function getMessageAdapter($attachedPart, $body, $message = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			/** @var \Zend\Mime\Message $mimeMessage */
        $mimeMessage = new \Zend\Mime\Message();
        $mimePart = new \Zend\Mime\Part($body);
        $mimePart->type = \Zend\Mime\Mime::TYPE_HTML;
        $mimePart->charset = 'utf-8';
        $mimeMessage->setParts([$mimePart]);
        if (!empty($attachedPart)) {
            foreach ($attachedPart as $part) {
                $mimeMessage->addPart($part);
            }
        }
        $message->setBody($mimeMessage);
		}
	}
}
