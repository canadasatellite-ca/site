<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_AutoRelated
 * @copyright   Copyright (c) 2017-2018 Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AutoRelated\Model\Rule;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Registry;

/**
 * Class LocationOptionsProvider
 * @package Mageplaza\AutoRelated\Model\Rule
 */
class LocationOptionsProvider implements OptionSourceInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(Registry $coreRegistry)
    {
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [];

        $type = $this->coreRegistry->registry('autorelated_type');
        if ($type == 'product') {
            $options = [
                ['value' => 'replace-related', 'label' => __('Replace native related product block')],
                ['value' => 'before-related', 'label' => __('Before native related products')],
                ['value' => 'after-related', 'label' => __('After native related products')],
                ['value' => 'replace-upsell', 'label' => __('Replace upsell product block')],
                ['value' => 'before-upsell', 'label' => __('Before upsell products')],
                ['value' => 'after-upsell', 'label' => __('After upsell products')]
            ];
        } else if ($type == 'cart') {
            $options = [
                ['value' => 'replace-cross', 'label' => __('Replace cross product block')],
                ['value' => 'before-cross', 'label' => __('Before cross products')],
                ['value' => 'after-cross', 'label' => __('After cross products')],
            ];
        } else if ($type == 'category') {
            $options = [
                ['value' => 'before-sidebar', 'label' => __('Sidebar Top')],
                ['value' => 'after-sidebar', 'label' => __('Sidebar Bottom')]
            ];
        }

        $options = array_merge($options, [
            ['value' => 'before-content', 'label' => __('Above Content')],
            ['value' => 'after-content', 'label' => __('Below Content')],
            //            ['value' => 'custom', 'label' => __('Custom')]
        ]);

        return $options;
    }
}
