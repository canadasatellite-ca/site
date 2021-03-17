<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Email;

/**
 * Class ZendOne
 *
 * @package Cart2Quote\Quotation\Model\Email
 */
class ZendOne extends \Cart2Quote\Quotation\Model\Email\ZendAdapter
{
    use \Cart2Quote\Features\Traits\Model\Email\ZendOne {
        attachFileAdapter as private traitAttachFileAdapter;
        getMessageAdapter as private traitGetMessageAdapter;
    }

    /**
     * Function to attach a file to an outgoing email
     *
     * @param string $file
     * @param string $name
     * @return \Zend_Mime_Part
     */
    public function attachFileAdapter($file, $name)
    {
        return $this->traitAttachFileAdapter($file, $name);
    }

    /**
     * Get message adapter
     *
     * @param array $attachedPart
     * @param string $body
     * @param null|\Magento\Framework\Mail\Message $message
     */
    public function getMessageAdapter($attachedPart, $body, $message = null)
    {
        $this->traitGetMessageAdapter($attachedPart, $body, $message);
    }
}
