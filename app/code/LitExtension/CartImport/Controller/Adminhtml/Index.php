<?php

namespace LitExtension\CartImport\Controller\Adminhtml;

abstract class Index extends \Magento\Backend\App\Action
{

    protected $_cart = null;
    protected $_notice = null;
    protected $_user_id = null;
    protected $_scopeConfig = null;
    protected $_import_action = array(
        'taxes',
        'manufacturers',
        'categories',
        'products',
        'customers',
        'orders',
        'reviews',
    );
    protected $_next_action = array(
        'taxes' => 'manufacturers',
        'manufacturers' => 'categories',
        'categories' => 'products',
        'products' => 'customers',
        'customers' => 'orders',
        'orders' => 'reviews',
        'reviews' => false,
    );
    protected $_simple_action = array(
        'taxes' => 'tax',
        'manufacturers' => 'manufacturer',
        'categories' => 'category',
        'products' => 'product',
        'customers' => 'customer',
        'orders' => 'order',
        'reviews' => 'review',
    );

    public function __construct(
        \Magento\Backend\App\Action\Context $nhtec2727b3b71f07635f726026bef44352ec89e452,
        \Magento\Framework\App\Config\ScopeConfigInterface $nht7e30e5879651ff951a7471e5c4d8996bac0a0c21
    ) {
        parent::__construct($nhtec2727b3b71f07635f726026bef44352ec89e452);
        $this->_scopeConfig = $nht7e30e5879651ff951a7471e5c4d8996bac0a0c21;
    }

    protected function _isAllowed(){
        return $this->_authorization->isAllowed('LitExtension_CartImport::leci_process');
    }

}
__halt_compiler();