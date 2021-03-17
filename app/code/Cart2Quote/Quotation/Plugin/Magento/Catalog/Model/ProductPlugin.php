<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Catalog\Model;

/**
 * Class ProductPlugin
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Catalog\Model
 */
class ProductPlugin
{
    /**
     * ProductPlugin constructor.
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->request = $request;
    }

    /**
     * @param \Magento\Catalog\Model\Product $subject
     * @param $result
     * @return int
     */
    public function afterGetStatus(\Magento\Catalog\Model\Product $subject, $result)
    {
        if ($this->request->getModuleName() == 'quotation' && $this->request->getFrontName() == 'admin') {
            return \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED;
        }

        return $result;
    }
}
