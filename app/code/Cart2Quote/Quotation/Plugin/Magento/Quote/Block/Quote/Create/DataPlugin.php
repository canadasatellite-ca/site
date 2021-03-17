<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Quote\Block\Quote\Create;

/**
 * Class StockCheckPlugin
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Quote\Block\Quote\Create
 */
class DataPlugin
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote $quoteResourceModel
     */
    protected $quoteResourceModel;

    /**
     * StockCheckPlugin constructor.
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote $quoteResourceModel
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote $quoteResourceModel
    ) {
        $this->quoteResourceModel = $quoteResourceModel;
    }

    /**
     * Before plugin for the get button HTML
     *
     * @param \Magento\Sales\Block\Adminhtml\Order\Create\Data $data
     * @param string $label
     * @param string $onclick
     * @param string $class
     * @param null|int $buttonId
     * @param array $dataAttr
     * @return array
     */
    public function beforeGetButtonHtml(
        \Magento\Sales\Block\Adminhtml\Order\Create\Data $data,
        $label,
        $onclick,
        $class = '',
        $buttonId = null,
        $dataAttr = []
    ) {
        $quotationQuote = $data->getQuote();
        if ($id = $quotationQuote->getId()) {
            $this->quoteResourceModel->load($quotationQuote, $id);
            if ($quotationQuote->getIncrementId()) {
                $label = 'Update Quote';
            }
        }

        return [__($label), $onclick, $class, $buttonId, $dataAttr];
    }
}
