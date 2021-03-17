<?php 
namespace Brsw\OrderEmailAddressUpd\Block\Adminhtml\Order\View;

class EmailEdit extends \Magento\Backend\Block\Template
{
    /****
     * EmailEdit constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
    }

    /****
     * @return string
     */
    public function getFormActionUrl()
    {
        //return $this->getUrl('order_customer/index/index', ['form_key' => $this->getFormKey()]);
        return $this->getUrl('emailupdate/index/index');
    }

    /****
     * @return mixed
     */
    public function getCurrentOrderId()
    {
        return $this->_request->getParam('order_id');
    }
}