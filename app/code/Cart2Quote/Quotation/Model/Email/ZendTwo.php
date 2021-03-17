<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Email;

/**
 * Class ZendTwo
 *
 * @package Cart2Quote\Quotation\Model\Email
 */
class ZendTwo extends ZendAdapter
{
    use \Cart2Quote\Features\Traits\Model\Email\ZendTwo {
        attachFileAdapter as private traitAttachFileAdapter;
        getMessageAdapter as private traitGetMessageAdapter;
    }

    /**
     * Get attach file adpater
     *
     * @param string $file
     * @param string $name
     * @return \Zend\Mime\Part
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
     * @param \Magento\Framework\Mail\Message|\Zend\Mime\Message|null $message
     */
    public function getMessageAdapter($attachedPart, $body, $message = null)
    {
        $this->traitGetMessageAdapter($attachedPart, $body, $message);
    }
}
