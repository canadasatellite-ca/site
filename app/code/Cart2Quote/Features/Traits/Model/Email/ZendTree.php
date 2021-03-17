<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Email;
use Magento\Framework\Mail\MimePartInterfaceFactory;
/**
 * Trait ZendTree
 *
 * @package Cart2Quote\Quotation\Model\Email
 */
trait ZendTree
{
    /**
     * Get attach file adapter
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
            $attachment = $this->mimePartInterfaceFactory->create(
                [
                    'content' => $fileContents,
                    'type' => \Zend\Mime\Mime::TYPE_OCTETSTREAM,
                    'fileName' => $name,
                    'disposition' => \Zend\Mime\Mime::DISPOSITION_ATTACHMENT,
                    'encoding' => \Zend\Mime\Mime::ENCODING_BASE64
                ]
            );
            return $attachment;
        }
		}
	}
    /**
     * Get message adapter
     * Adapter not needed after Magento 2.3.3 and higher
     *
     * @param array $attachedPart
     * @param string $body
     * @param \Magento\Framework\Mail\Message|\Zend\Mime\Message|null $message
     */
    private function getMessageAdapter($attachedPart, $body, $message = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
		}
	}
}
