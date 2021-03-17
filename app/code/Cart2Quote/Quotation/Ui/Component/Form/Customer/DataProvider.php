<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Ui\Component\Form\Customer;

/**
 * Class DataProvider
 * @package Cart2Quote\Quotation\Ui\Component\Form\Customer
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @return array
     */
    public function getData()
    {
        $quoteId = $this->request->getParam('quote_id');
        if ($quoteId) {
            return [$quoteId => ['entity_id' => $quoteId]];
        }

        return [];
    }
}
