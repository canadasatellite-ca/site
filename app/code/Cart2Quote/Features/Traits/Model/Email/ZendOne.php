<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Email;
/**
 * Trait ZendOne
 *
 * @package Cart2Quote\Quotation\Model\Email
 */
trait ZendOne
{
    /**
     * Function to attach a file to an outgoing email
     *
     * @param string $file
     * @param string $name
     * @return \Zend_Mime_Part
     */
    private function attachFileAdapter($file, $name)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!empty($file) && file_exists($file)) {
            $fileContents = fopen($file, 'r');
            $attachment = new \Zend_Mime_Part($fileContents);
            $attachment->type = \Zend_Mime::TYPE_OCTETSTREAM;
            $attachment->encoding = \Zend_Mime::ENCODING_BASE64;
            $attachment->disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
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
     * @param null|\Magento\Framework\Mail\Message $message
     */
    private function getMessageAdapter($attachedPart, $body, $message = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$message->setMessageType(\Zend_Mime::TYPE_HTML);
        $message->setBody($body);
        if (!empty($attachedPart)) {
            foreach ($attachedPart as $part) {
                $message->addAttachment($part);
            }
        }
		}
	}
}
