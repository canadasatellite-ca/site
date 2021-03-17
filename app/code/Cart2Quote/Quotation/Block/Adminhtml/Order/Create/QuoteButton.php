<?php
/**
 * Cart2Quote
 */
namespace Cart2Quote\Quotation\Block\Adminhtml\Order\Create;

/**
 * Adminhtml sales order create quote button
 */
class QuoteButton extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{

    protected $_onClickCode;

    /**
     * Get buttons html
     * @return string
     */
    public function getButtonsHtml()
    {
        $addButtonData = [
            'label' => __('Create Quote'),
            'onclick' => $this->_onClickCode,
            'class' => 'action-add primary quote-create-button-bottom',
            'style' => 'display: none; float: left;'
        ];
        return $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            $addButtonData
        )->toHtml();
    }

    /**
     * Constructor
     * @return void
     */
    protected function _construct()
    {
        //set the javascript for the onClick buttons
        $this->_onClickCode = 'order.quoteSubmit();';

        //add the button to the header button bar in the order create screen
        if (is_object($this->getLayout()->getBlock('order_content'))) {
            $this->getLayout()->getBlock('order_content')->addButton(
                'quote',
                [
                    'label' => __('Create Quote'),
                    'onclick' => $this->_onClickCode,
                    'class' => 'action-add primary',
                    'style' => 'display: none;'
                ]
            );
        }

        //construct this Block
        parent::_construct();
        $this->setId('sales_order_create_create_quote');
    }

    /**
     * Retrieve url for form submitting
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('quotation/quote/create');
    }
}
