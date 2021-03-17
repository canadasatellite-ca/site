<?php

namespace LitExtension\CartImport\Controller\Adminhtml\Index;

use Zend\Json\Json;

class Index extends \LitExtension\CartImport\Controller\Adminhtml\Index
{

    /**
     * Show admin gui
     */
    public function execute(){
        $this->_initCart();
        $this->_notice['setting'] = $this->_scopeConfig->getValue('leci/general');
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('leci.index')->setNotice($this->_notice);
        $this->_view->renderLayout();
    }

    /**
     * Router to model cart process migration
     */
    protected function _initCart(){
        $nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28 = $this->_objectManager->create('LitExtension\CartImport\Model\Cart');
        $this->_notice = $this->_getNotice($nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28);
        $nht0e1f6a930da58a371a0a7b5421914808c919eb45 = $this->_notice['config']['cart_type'];
        $nht1d06a0d76f000e6edd18de492383983feefced4e = 'LitExtension\CartImport\Model\\' . $nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28->getCart($nht0e1f6a930da58a371a0a7b5421914808c919eb45);
        $this->_cart = $this->_objectManager->create($nht1d06a0d76f000e6edd18de492383983feefced4e);
        $this->_cart->setNotice($this->_notice);
        return $this;
    }

    /**
     * Get migration notice by mode
     */
    protected function _getNotice($nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28){
        $nht12dea96fec20593566ab75692c9949596833adc9 = $this->_auth->getUser();
        if($nht12dea96fec20593566ab75692c9949596833adc9 && $this->_auth->isLoggedIn()){
            $this->_user_id = $nht12dea96fec20593566ab75692c9949596833adc9->getUserId();
        }
        if(\LitExtension\CartImport\Model\Custom::DEMO_MODE){
            $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61 = $this->_getSession()->getLeCaIp();
        } else {
            $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61 = $nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28->getUserNotice($this->_user_id);
        }
        if(!$nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61){
            $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61 = $nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28->getDefaultNotice();
        }
        return $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61;
    }

    /**
     * Save migration notice by mode
     */
    protected function _saveNotice(){
        $nht12dea96fec20593566ab75692c9949596833adc9 = $this->_auth->getUser();
        if($nht12dea96fec20593566ab75692c9949596833adc9 && $this->_auth->isLoggedIn()){
            $this->_user_id = $nht12dea96fec20593566ab75692c9949596833adc9->getUserId();
        }
        if(\LitExtension\CartImport\Model\Custom::DEMO_MODE){
            $this->_getSession()->setLeCaIp($this->_notice);
            return true;
        } else {
            return $this->_cart->saveUserNotice($this->_user_id, $this->_notice);
        }
    }

    /**
     * Delete migration notice by mode
     */
    protected function _deleteNotice($nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28){
        $nht12dea96fec20593566ab75692c9949596833adc9 = $this->_auth->getUser();
        if(!$this->_user_id){
            if($nht12dea96fec20593566ab75692c9949596833adc9 && $this->_auth->isLoggedIn()){
                $this->_user_id = $nht12dea96fec20593566ab75692c9949596833adc9->getUserId();
            }
        }
        if(\LitExtension\CartImport\Model\Custom::DEMO_MODE){
            $this->_getSession()->unsLeCaIp();
            return true;
        } else {
            return $nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28->deleteUserNotice($this->_user_id);
        }
    }

    /**
     * Convert array to json and response
     */
    protected function _responseAjaxJson($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd){
        $this->getResponse()->setBody(Json::encode($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd));
        return ;
    }

    /**
     * Construct of response
     */
    protected function _defaultResponse(){
        return array(
            'result' => '',
            'msg' => '',
            'html' => '',
            'elm' => ''
        );
    }

}
__halt_compiler();