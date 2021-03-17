<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Customer\Model;

/**
 * Class Quote
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Quote\Model\ResourceModel
 */
class Address
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * Address constructor
     *
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(\Magento\Framework\App\RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Plugin after updateData
     *
     * @param \Magento\Customer\Model\Address $subject
     * @return mixed
     */
    public function afterUpdateData($subject)
    {
        if (strpos($this->request->getHeader('Referer'), 'quotation') !== false) {
            return $subject->setShouldIgnoreValidation(true);
        }
        return $subject;
    }
}
