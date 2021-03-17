<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Email;

use Magento\Framework\Mail\MimePartInterfaceFactory;

/**
 * Class ZendTree
 *
 * @package Cart2Quote\Quotation\Model\Email
 */
class ZendTree extends ZendAdapter
{
    use \Cart2Quote\Features\Traits\Model\Email\ZendTree {
        attachFileAdapter as private traitAttachFileAdapter;
        getMessageAdapter as private traitGetMessageAdapter;
    }

    /**
     * @var \Magento\Framework\Mail\MimePartInterfaceFactory
     */
    private $mimePartInterfaceFactory;

    /**
     * ZendTree constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->mimePartInterfaceFactory = $objectManager->get(MimePartInterfaceFactory::class);
    }

    /**
     * Get attach file adapter
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
     * Adapter not needed after Magento 2.3.3 and higher
     *
     * @param array $attachedPart
     * @param string $body
     * @param \Magento\Framework\Mail\Message|\Zend\Mime\Message|null $message
     */
    public function getMessageAdapter($attachedPart, $body, $message = null)
    {$this->traitGetMessageAdapter($attachedPart, $body, $message);
    }
}
