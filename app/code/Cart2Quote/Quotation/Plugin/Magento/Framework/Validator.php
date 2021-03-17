<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Framework;

/**
 * Class Validator
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Customer\Model
 */
class Validator extends \Magento\Framework\Validator
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * Address constructor.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(\Magento\Framework\App\RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * After is valid plugin
     *
     * @param \Magento\Framework\Validator $subject
     * @param bool $value
     * @return bool
     */
    public function afterIsValid($subject, $value)
    {
        if (strpos($this->request->getHeader('Referer'), 'quotation') !== false) {
            $messages = $subject->getMessages();

            if (isset($messages['city']) ||
                isset($messages['postcode']) ||
                isset($messages['street']) ||
                isset($messages['telephone'])
            ) {
                $value = true;
            }
        }

        return $value;
    }
}
