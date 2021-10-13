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

namespace Mageplaza\AutoRelated\Block\Product\ProductList;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\AutoRelated\Helper\Data;
use Mageplaza\AutoRelated\Model\Config\Source\DisplayStyle;

/**
 * Class AutoRelated
 * @package Mageplaza\AutoRelated\Block\Product\ProductList
 */
class AutoRelated extends Template
{
    /**
     * Path to template file.
     *
     * @var string
     */
    protected $_template = 'Mageplaza_AutoRelated::product/list/autorelated.phtml';

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Mageplaza\AutoRelated\Helper\Data
     */
    private $helperData;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Mageplaza\AutoRelated\Helper\Data
     * @param array $data
     */
    function __construct(
        Context $context,
        Registry $registry,
        Data $helperData,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->registry   = $registry;
        $this->helperData = $helperData;
    }

    /**
     * Get Data send ajax
     *
     * @return mixed
     */
    function getAjaxData()
    {
        if (!$this->helperData->isEnabled()) {
            return false;
        }

        $product    = $this->registry->registry('current_product');
        $productId  = $product ? $product->getId() : '';
        $category   = $this->registry->registry('current_category');
        $categoryId = $category ? $category->getId() : '';
        $request    = $this->getRequest();

        $params = [
            'url'             => $this->getUrl('autorelated/ajax/load'),
            'urlClick'        => $this->getUrl('autorelated/ajax/click'),
            'isAjax'          => $this->isAjaxLoad(),
            'originalRequest' => [
                'route'       => $request->getRouteName(),
                'module'      => $request->getModuleName(),
                'controller'  => $request->getControllerName(),
                'action'      => $request->getActionName(),
                'uri'         => $request->getRequestUri(),
                'product_id'  => $productId,
                'category_id' => $categoryId
            ]
        ];
        if ($this->getIsCms()) {
            $params['originalRequest']['cms'] = true;
        }

        return $this->helperData->jsonEncode($params);
    }

    /**
     * @return bool
     */
    function isAjaxLoad()
    {
        return (bool)$this->helperData->getConfigDisplay() == DisplayStyle::TYPE_AJAX;
    }

    /**
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    function getBlockListData()
    {
        return $this->helperData->getRelatedProduct($this->getLayout(), $this->helperData->jsonDecode($this->getAjaxData())['originalRequest'], false);
    }
}
