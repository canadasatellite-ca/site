<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View;

/**
 * Class Search
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View
 */
class Search extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\AbstractView
{

    /**
     * Contains button descriptions to be shown at the top of quote view
     *
     * @var array
     */
    protected $buttons = [];

    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Please select products');
    }

    /**
     * Add button to the items header
     *
     * @param array $args
     * @return void
     */
    public function addButton($args)
    {
        $this->buttons[] = $args;
    }

    /**
     * Get buttons html
     *
     * @return string
     */
    public function getButtonsHtml()
    {
        $html = '';
        foreach ($this->buttons as $buttonData) {
            $html .= $this->getLayout()
                ->createBlock(\Magento\Backend\Block\Widget\Button::class)
                ->setData($buttonData)
                ->toHtml();
        }

        return $html;
    }

    /**
     * Get header css class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-catalog-product';
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('quote_view_search');
        $this->addButton(
            [
                'label' => __('Add Custom Product'),
                'class' => 'action-add action-secondary',
                'onclick' => 'jQuery("#custom_product_modal").modal("openModal");',
                'data_attribute' => [
                    'mage-init' => [
                        'Cart2Quote_Quotation/quote/view/addCustomProduct' => [
                            'elementId' => '#custom_product_modal',
                            'url' => $this->getUrl('quotation/product/create'),
                            'response' => true
                        ]
                    ]
                ]
            ]
        );
        $this->addButton(
            [
                'label' => __('Add Selected Product(s) to Quote'),
                'class' => 'action-add action-secondary',
                'onclick' => 'quote.productGridAddSelected()'
            ]
        );
    }
}
