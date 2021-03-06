<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Blog
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Blog\Model\Config\Source\Comments\Facebook;

class Orderby implements \Magento\Framework\Option\ArrayInterface
{
    const SOCIAL = 'social';
    const REVERSE_TIME  = 'reverse_time';
    const TIME  = 'time';

    function toOptionArray()
    {
        return [
        	['value' => self::SOCIAL, 'label' => __('Social')],
			['value' => self::REVERSE_TIME, 'label' => __('Reverse time')],
			['value' => self::TIME, 'label' => __('Time')]
		];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    function toArray()
    {
        return [self::SOCIAL => __('Social'), self::REVERSE_TIME => __('Reverse time'), self::TIME => __('Time')];
    }

    function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
