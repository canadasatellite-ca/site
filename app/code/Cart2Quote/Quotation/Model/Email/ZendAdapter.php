<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Email;

/**
 * Class ZendAdapter
 *
 * @package Cart2Quote\Quotation\Model\Email
 */
abstract class ZendAdapter
{

    use \Cart2Quote\Features\Traits\Model\Email\ZendAdapter {
    }

    /**
     * Attach file adapter
     *
     * @param string $file
     * @param string $name
     * @return \Zend_Mime_Part | \Zend\Mime\Part
     */
    abstract public function attachFileAdapter($file, $name);

    /**
     * Get message adapter
     *
     * @param array $attachedPart
     * @param string $body
     * @param \Magento\Framework\Mail\Message|null $message
     * @return \Zend_Mime_Message | \Zend\Mime\Message
     */
    abstract public function getMessageAdapter($attachedPart, $body, $message = null);
}
