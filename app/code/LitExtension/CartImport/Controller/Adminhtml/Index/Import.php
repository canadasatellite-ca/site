<?php

namespace LitExtension\CartImport\Controller\Adminhtml\Index;

use Zend\Json\Json;

class Import extends \LitExtension\CartImport\Controller\Adminhtml\Index\Index
{

    /**
     * Show admin gui
     */
    public function execute(){
        $nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90 = $this->getRequest()->getParams();
        if(isset($nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90['action']) && $nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90['action'] != ''){
            $nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e = $nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90['action'];
            if(in_array($nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e, $this->_import_action)){
                $this->_import($nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e);
            } else {
                $nhtc218e39efa2e1aae69f39d2054528369ce1e1f46 = '_'.$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e;
                $this->$nhtc218e39efa2e1aae69f39d2054528369ce1e1f46();
            }
        } else {
            $this->_redirect('leci/index/index');
        }
        return ;
    }

    /**
     * Show display to success resume config in admin gui
     */
    protected function _resume(){
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array();
        $this->_initCart();
        $this->_notice['msg_start'] = $this->_cart->consoleSuccess("Resuming ...");
        $this->_view->loadLayout();
        $nht0214b4b355d11ca8f2ce45a968c264651bdfbf83 = $this->_view->getLayout()->createBlock('LitExtension\CartImport\Block\Adminhtml\Index', 'leci.import')->setTemplate('import.phtml');
        $nht950a39b6c2934bb72f2def76c71e88e9c035385f = "";
        if($nht0214b4b355d11ca8f2ce45a968c264651bdfbf83){
            $nht950a39b6c2934bb72f2def76c71e88e9c035385f = $nht0214b4b355d11ca8f2ce45a968c264651bdfbf83->setNotice($this->_notice)->toHtml();
        }
        $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
        $nht0ec6d150549780250a9772c06b619bcc46a0e560['html'] = $nht950a39b6c2934bb72f2def76c71e88e9c035385f;
        $this->_notice['setting'] = $this->_scopeConfig->getValue('leci/general');
        $nht13a4a11319d31c1b323d5774f44240a9ffc984d0 = $this->_saveNotice();
        if(!$nht13a4a11319d31c1b323d5774f44240a9ffc984d0){
            return $this->_responseAjaxJson($this->_cart->errorDatabase());
        }
        return $this->_responseAjaxJson($nht0ec6d150549780250a9772c06b619bcc46a0e560);
    }

    protected function _displayUpload(){
        $nht0e1f6a930da58a371a0a7b5421914808c919eb45 = $this->getRequest()->getParam('cart_type');
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = $this->_defaultResponse();
        $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'show';
        if(!$nht0e1f6a930da58a371a0a7b5421914808c919eb45){
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['html'] = "Cart type isn't supporting.";
            $this->_responseAjaxJson($nht0ec6d150549780250a9772c06b619bcc46a0e560);
            return ;
        }
        $nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28 = $this->_objectManager->create('LitExtension\CartImport\Model\Cart');
        $nht1d06a0d76f000e6edd18de492383983feefced4e = 'LitExtension\CartImport\Model\\' . $nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28->getCart($nht0e1f6a930da58a371a0a7b5421914808c919eb45);
        $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed = $this->_objectManager->create($nht1d06a0d76f000e6edd18de492383983feefced4e);
        $nhtbb73aaafa1596e5425dc514a361ad4ef658f2758 = $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed->getListUpload();
        $this->_view->loadLayout();
        $nht0214b4b355d11ca8f2ce45a968c264651bdfbf83 = $this->_view->getLayout()->createBlock('LitExtension\CartImport\Block\Adminhtml\Index', 'leci.upload')->setTemplate('upload.phtml');
        $nht950a39b6c2934bb72f2def76c71e88e9c035385f = "";
        if($nht0214b4b355d11ca8f2ce45a968c264651bdfbf83){
            $nht950a39b6c2934bb72f2def76c71e88e9c035385f = $nht0214b4b355d11ca8f2ce45a968c264651bdfbf83->setListUpload($nhtbb73aaafa1596e5425dc514a361ad4ef658f2758)->setCartType($nht0e1f6a930da58a371a0a7b5421914808c919eb45)->toHtml();
        }
        $nht0ec6d150549780250a9772c06b619bcc46a0e560['html'] = $nht950a39b6c2934bb72f2def76c71e88e9c035385f;
        $this->_responseAjaxJson($nht0ec6d150549780250a9772c06b619bcc46a0e560);
        return ;
    }

    protected function _upload(){
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = $this->_defaultResponse();
        $nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28 = $this->_objectManager->create('LitExtension\CartImport\Model\Cart');
        $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61 = $this->_getNotice($nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28);
        if($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['folder']){
            $nht6fe76473ac513bc9ff94a78a1fc882e285b45ae8 = $this->_objectManager->get('Magento\Store\Model\Store')->getBaseMediaDir() . \LitExtension\CartImport\Model\Cart::FOLDER_SUFFIX . $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['folder'];
            $nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28->deleteDir($nht6fe76473ac513bc9ff94a78a1fc882e285b45ae8);
            if($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['cart_type']){
                $nht1d06a0d76f000e6edd18de492383983feefced4e = 'LitExtension\CartImport\Model\\' . $nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28->getCart($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['cart_type']);
                $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed = $this->_objectManager->create($nht1d06a0d76f000e6edd18de492383983feefced4e);
                $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed->setNotice($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61);
                $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed->clearPreSection();
            }
        }
        $nhtfea453f853c8645b085126e6517eab38dfaa022f = $this->_deleteNotice($nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28);
        if(!$nhtfea453f853c8645b085126e6517eab38dfaa022f){
            $this->_responseAjaxJson($nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28->errorDatabase());
            return ;
        }
        $this->_notice = $this->_getNotice($nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28);
        $nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90 = $this->getRequest()->getParams();
        $this->_notice['config']['cart_type'] = $nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90['cart_type'];
        $this->_notice['config']['cart_url'] = trim(rtrim($nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90['cart_url'], '/'));
        $this->_notice['config']['folder'] = \LitExtension\CartImport\Model\Custom::FOLDER_UPLOAD ? \LitExtension\CartImport\Model\Custom::FOLDER_UPLOAD : $nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28->createFolderUpload($this->_notice['config']['cart_url']);
        $nht1d06a0d76f000e6edd18de492383983feefced4e = 'LitExtension\CartImport\Model\\' . $nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28->getCart($this->_notice['config']['cart_type']);
        $this->_cart = $this->_objectManager->create($nht1d06a0d76f000e6edd18de492383983feefced4e);
        $nht8d2e436b2620d3f61751b03b186a7fd8a1a413cb = $this->_cart->getAllowExtensions();
        $nht9bf4089191af0ee6f4e73649e04ae5e0a0f65caa = $this->_cart->getListUpload();
        $this->_notice['config']['file_data'] = $nht9bf4089191af0ee6f4e73649e04ae5e0a0f65caa;
        $nht00478006529e8981655252b945d377723327f9f2 = $this->_objectManager->get('Magento\Store\Model\Store')->getBaseMediaDir() . \LitExtension\CartImport\Model\Cart::FOLDER_SUFFIX . $this->_notice['config']['folder'];
        $nht1b6539c0fa17fdc15b93f2cedfc5bbd60ed8b887 = array();
        foreach($nht9bf4089191af0ee6f4e73649e04ae5e0a0f65caa as $nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af){
            $nht311d027bc7c3111f52564b5b28c56f23f6ceb462 = $nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af['value'];
            if(isset($_FILES[$nht311d027bc7c3111f52564b5b28c56f23f6ceb462])){
                $nht6124c3f57246e68e6ae44dd055bc5bc7a567e55f = $this->_cart->getUploadFileName($nht311d027bc7c3111f52564b5b28c56f23f6ceb462);
                $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->_uploadFile($_FILES[$nht311d027bc7c3111f52564b5b28c56f23f6ceb462], $nht00478006529e8981655252b945d377723327f9f2, $nht6124c3f57246e68e6ae44dd055bc5bc7a567e55f, $nht8d2e436b2620d3f61751b03b186a7fd8a1a413cb);
                if($nht37a5301a88da334dc5afc5b63979daa0f3f45e68['result'] == 'success'){
                    $this->_notice['config']['files'][$nht311d027bc7c3111f52564b5b28c56f23f6ceb462] = true;
                    $nht1b6539c0fa17fdc15b93f2cedfc5bbd60ed8b887[$nht311d027bc7c3111f52564b5b28c56f23f6ceb462] = array(
                        'elm' => '#ur-' . $nht311d027bc7c3111f52564b5b28c56f23f6ceb462,
                        'msg' => "<div class='uir-success'> Uploaded successfully.</div>"
                    );
                } else {
                    $this->_notice['config']['files'][$nht311d027bc7c3111f52564b5b28c56f23f6ceb462] = false;
                    $this->_notice['config']['upload_success'] = false;
                    $nht1b6539c0fa17fdc15b93f2cedfc5bbd60ed8b887[$nht311d027bc7c3111f52564b5b28c56f23f6ceb462] = array(
                        'elm' => '#ur-' . $nht311d027bc7c3111f52564b5b28c56f23f6ceb462,
                        'msg' => "<div class='uir-warning'> Upload failed.</div>"
                    );
                }
            } else {
                $this->_notice['config']['files'][$nht311d027bc7c3111f52564b5b28c56f23f6ceb462] = false;
            }
        }
        $this->_cart->setNotice($this->_notice);
        $nhtd87805138349c408f8af375ef0287c71d7435faa = $this->_cart->getUploadInfo($nht1b6539c0fa17fdc15b93f2cedfc5bbd60ed8b887);
        $this->_notice = $this->_cart->getNotice();
        $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = $nhtd87805138349c408f8af375ef0287c71d7435faa['msg'];
        $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
        $nht13a4a11319d31c1b323d5774f44240a9ffc984d0 = $this->_saveNotice();
        if(!$nht13a4a11319d31c1b323d5774f44240a9ffc984d0){
            $this->_responseAjaxJson($this->_cart->errorDatabase());
            return ;
        }
        $this->_responseAjaxJson($nht0ec6d150549780250a9772c06b619bcc46a0e560);
        return ;
    }

    /**
     * Show display to success step setup in admin gui
     */
    protected function _setup(){
        $this->_initCart();
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->_cart->displayStorage();
        if($nht37a5301a88da334dc5afc5b63979daa0f3f45e68['result'] != 'success'){
            $this->_responseAjaxJson($nht37a5301a88da334dc5afc5b63979daa0f3f45e68);
            return ;
        }
        $this->_notice = $this->_cart->getNotice();
        $this->_view->loadLayout();
        $nht0214b4b355d11ca8f2ce45a968c264651bdfbf83 = $this->_view->getLayout()->createBlock('LitExtension\CartImport\Block\Adminhtml\Index', 'leci.csv')->setTemplate('csv.phtml');
        $nht950a39b6c2934bb72f2def76c71e88e9c035385f = "";
        if($nht0214b4b355d11ca8f2ce45a968c264651bdfbf83){
            $nht950a39b6c2934bb72f2def76c71e88e9c035385f = $nht0214b4b355d11ca8f2ce45a968c264651bdfbf83->setNotice($this->_notice)->toHtml();
        }
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = $this->_defaultResponse();
        $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
        $nht0ec6d150549780250a9772c06b619bcc46a0e560['html'] = $nht950a39b6c2934bb72f2def76c71e88e9c035385f;
        $nht13a4a11319d31c1b323d5774f44240a9ffc984d0 = $this->_saveNotice();
        if(!$nht13a4a11319d31c1b323d5774f44240a9ffc984d0){
            $this->_responseAjaxJson($this->_cart->errorDatabase());
            return ;
        }
        try{
            $this->_objectManager->create('LitExtension\Core\Helper\Data')->saveConfig('lecupd/general/type', $this->_notice['config']['cart_type']);
            $this->_objectManager->create('LitExtension\Core\Helper\Data')->saveConfig('lecupd/general/url', $this->_notice['config']['cart_url']);
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){}
        $this->_responseAjaxJson($nht0ec6d150549780250a9772c06b619bcc46a0e560);
        return ;
    }

    protected function _csv(){
        $this->_initCart();
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = $this->_cart->storageCsv();
        $this->_notice = $this->_cart->getNotice();
        if($nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] == 'success'){
            $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->_cart->displayConfig();
            $this->_notice = $this->_cart->getNotice();
            if($nht37a5301a88da334dc5afc5b63979daa0f3f45e68['result'] != 'success'){
                $this->_responseAjaxJson($nht37a5301a88da334dc5afc5b63979daa0f3f45e68);
                return ;
            }
            $this->_view->loadLayout();
            $nht0214b4b355d11ca8f2ce45a968c264651bdfbf83 = $this->_view->getLayout()->createBlock('LitExtension\CartImport\Block\Adminhtml\Index', 'leci.config')->setTemplate('config.phtml');
            $nht950a39b6c2934bb72f2def76c71e88e9c035385f = "";
            if($nht0214b4b355d11ca8f2ce45a968c264651bdfbf83){
                $nht950a39b6c2934bb72f2def76c71e88e9c035385f = $nht0214b4b355d11ca8f2ce45a968c264651bdfbf83->setNotice($this->_notice)->toHtml();
            }
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['html'] = $nht950a39b6c2934bb72f2def76c71e88e9c035385f;
        }
        $nht13a4a11319d31c1b323d5774f44240a9ffc984d0 = $this->_saveNotice();
        if(!$nht13a4a11319d31c1b323d5774f44240a9ffc984d0){
            $this->_responseAjaxJson($this->_cart->errorDatabase(true));
            return ;
        }
        $this->_responseAjaxJson($nht0ec6d150549780250a9772c06b619bcc46a0e560);
        return ;
    }

    /**
     * Show display to success step config in admin gui
     */
    protected function _config(){
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array();
        $this->_initCart();
        $nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90 = $this->getRequest()->getParams();
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->_cart->displayConfirm($nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90);
        if($nht37a5301a88da334dc5afc5b63979daa0f3f45e68['result'] != 'success'){
            return $this->_responseAjaxJson($nht37a5301a88da334dc5afc5b63979daa0f3f45e68);
        }
        $this->_notice = $this->_cart->getNotice();
        $this->_view->loadLayout();
        $nht0214b4b355d11ca8f2ce45a968c264651bdfbf83 = $this->_view->getLayout()->createBlock('LitExtension\CartImport\Block\Adminhtml\Index', 'leci.confirm')->setTemplate('confirm.phtml');
        $nht950a39b6c2934bb72f2def76c71e88e9c035385f = "";
        if($nht0214b4b355d11ca8f2ce45a968c264651bdfbf83){
            $nht950a39b6c2934bb72f2def76c71e88e9c035385f = $nht0214b4b355d11ca8f2ce45a968c264651bdfbf83->setNotice($this->_notice)->toHtml();
        }
        $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
        $nht0ec6d150549780250a9772c06b619bcc46a0e560['html'] = $nht950a39b6c2934bb72f2def76c71e88e9c035385f;
        $nht13a4a11319d31c1b323d5774f44240a9ffc984d0 = $this->_saveNotice();
        if(!$nht13a4a11319d31c1b323d5774f44240a9ffc984d0){
            return $this->_responseAjaxJson($this->_cart->errorDatabase());
        }
        return $this->_responseAjaxJson($nht0ec6d150549780250a9772c06b619bcc46a0e560);
    }

    /**
     * Show display to success step confirm in admin gui
     */
    protected function _confirm(){
        $this->_initCart();
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array();
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->_cart->displayImport();
        if($nht37a5301a88da334dc5afc5b63979daa0f3f45e68['result'] != 'success'){
            return $this->_responseAjaxJson($nht37a5301a88da334dc5afc5b63979daa0f3f45e68);
        }
        $this->_notice = $this->_cart->getNotice();
        if($this->_notice['config']['add_option']['clear_data']){
            $nht19f34ee1e406ea84ca83c835a3301b5d9014a788 = $this->_cart->consoleSuccess("Clearing store ...");
        } else {
            $nht19f34ee1e406ea84ca83c835a3301b5d9014a788 = $this->_cart->getMsgStartImport('taxes');
        }
        $this->_notice['msg_start'] = $nht19f34ee1e406ea84ca83c835a3301b5d9014a788;
        $this->_view->loadLayout();
        $nht0214b4b355d11ca8f2ce45a968c264651bdfbf83 = $this->_view->getLayout()->createBlock('LitExtension\CartImport\Block\Adminhtml\Index', 'leci.import')->setTemplate('import.phtml');
        $nht950a39b6c2934bb72f2def76c71e88e9c035385f = "";
        if($nht0214b4b355d11ca8f2ce45a968c264651bdfbf83){
            $nht950a39b6c2934bb72f2def76c71e88e9c035385f = $nht0214b4b355d11ca8f2ce45a968c264651bdfbf83->setNotice($this->_notice)->toHtml();
        }
        $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
        $nht0ec6d150549780250a9772c06b619bcc46a0e560['html'] = $nht950a39b6c2934bb72f2def76c71e88e9c035385f;
        $nht13a4a11319d31c1b323d5774f44240a9ffc984d0 = $this->_saveNotice();
        if(!$nht13a4a11319d31c1b323d5774f44240a9ffc984d0){
            return $this->_responseAjaxJson($this->_cart->errorDatabase());
        }
        return $this->_responseAjaxJson($nht0ec6d150549780250a9772c06b619bcc46a0e560);
    }

    /**
     * Process action clear store
     */
    protected function _clear(){
        $this->_initCart();
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = $this->_cart->clearStore();
        $this->_notice = $this->_cart->getNotice();
        $this->_notice['fn_resume'] = 'clearStore';
        $nht13a4a11319d31c1b323d5774f44240a9ffc984d0 = $this->_saveNotice();
        if(!$nht13a4a11319d31c1b323d5774f44240a9ffc984d0){
            return $this->_responseAjaxJson($this->_cart->errorDatabase(true));
        }
        return $this->_responseAjaxJson($nht0ec6d150549780250a9772c06b619bcc46a0e560);
    }

    /**
     * Process config currencies
     */
    protected function _currencies(){
        $this->_initCart();
        $this->_cart->configCurrency();
        $this->_notice = $this->_cart->getNotice();
        if($this->_notice['config']['import']['taxes']){
            $this->_cart->prepareImportTaxes();
        }
        $this->_notice['taxes']['time_start'] = time();
        $this->_notice['fn_resume'] = 'importTaxes';
        $nht47b89dd393e6b1c57f0ea17983c26b8618ebabb8 = $this->_saveNotice();
        if(!$nht47b89dd393e6b1c57f0ea17983c26b8618ebabb8){
            $nht0ec6d150549780250a9772c06b619bcc46a0e560 = $this->_cart->errorDatabase();
            return $this->_responseAjaxJson($nht0ec6d150549780250a9772c06b619bcc46a0e560);
        }
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array('result' => 'success');
        return $this->_responseAjaxJson($nht0ec6d150549780250a9772c06b619bcc46a0e560);
    }

    /**
     * Process import by action
     */
    protected function _import($nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e){
        $this->_initCart();
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = $this->_defaultResponse();
        $this->_notice['is_running'] = true;
        if(!$this->_notice['config']['import'][$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e]){
            $nht5ad07154a787cf8a9e97815aefcea3145122fdc1 = $this->_next_action[$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e];
            if($nht5ad07154a787cf8a9e97815aefcea3145122fdc1 && $this->_notice['config']['import'][$nht5ad07154a787cf8a9e97815aefcea3145122fdc1]){
                $nhtfd77e22c2fb38dbc7dc64921c87989ca5c0b78bd = 'prepareImport' . ucfirst($nht5ad07154a787cf8a9e97815aefcea3145122fdc1);
                $this->_cart->$nhtfd77e22c2fb38dbc7dc64921c87989ca5c0b78bd();
                $this->_notice[$nht5ad07154a787cf8a9e97815aefcea3145122fdc1]['time_start'] = time();
            }
            if($nht5ad07154a787cf8a9e97815aefcea3145122fdc1){
                $nhtd94d8e8c2d19a3d96d610f0f2d98f8499f263a43 = 'import' . ucfirst($nht5ad07154a787cf8a9e97815aefcea3145122fdc1);
                $this->_notice['fn_resume'] = $nhtd94d8e8c2d19a3d96d610f0f2d98f8499f263a43;
            }
            if($nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e == 'reviews'){
                $this->_cart->updateApi();
                $this->_notice['is_running'] = false;
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] .= $this->_cart->consoleSuccess('Finished migration!');
            }
            $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61 = $this->_cart->getNotice();
            $this->_notice['extend'] = $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['extend'];
            $nht47b89dd393e6b1c57f0ea17983c26b8618ebabb8 = $this->_saveNotice();
            if(!$nht47b89dd393e6b1c57f0ea17983c26b8618ebabb8){
                $nht0ec6d150549780250a9772c06b619bcc46a0e560 = $this->_cart->errorDatabase();
                $this->_responseAjaxJson($nht0ec6d150549780250a9772c06b619bcc46a0e560);
                return ;
            }
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'no-import';
            $this->_responseAjaxJson($nht0ec6d150549780250a9772c06b619bcc46a0e560);
            return ;
        }
        $nht5a537e209151ae5fcccd6326b34b5622bcfb0578 = $this->_notice[$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e]['total'];
        $nhta671a412ad5a5208f0c3501753914eea14efc15f = $this->_notice[$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e]['imported'];
        $nht11f9578d05e6f7bb58a3cdd00107e9f4e3882671 = $this->_notice[$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e]['error'];
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->_notice[$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e]['id_src'];
        $nht13b38b9c0f9afcf4f1ed55125d739ea736497546 = $this->_simple_action[$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e];
        $nht5ad07154a787cf8a9e97815aefcea3145122fdc1 = $this->_next_action[$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e];
        if($nhta671a412ad5a5208f0c3501753914eea14efc15f < $nht5a537e209151ae5fcccd6326b34b5622bcfb0578){
            $nht1991ac8330ef7c26f0cfa3ed38b1528647eae767 = 'get' . ucfirst($nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e);
            $nht39a09532cef06ebac16f841db0e078b329c2b6db = 'get' .ucfirst($nht13b38b9c0f9afcf4f1ed55125d739ea736497546) . 'Id';
            $nht87b132218bbbc586cbef6496f6b2c6266491d440 = 'check' . ucfirst($nht13b38b9c0f9afcf4f1ed55125d739ea736497546) . 'Import';
            $nht0ea88d8b780fdb250bf00a74ebbbb6b3be02518e = 'convert' . ucfirst($nht13b38b9c0f9afcf4f1ed55125d739ea736497546);
            $nhtc8a572fdaa5da3e9a78f8a388b44d458f5dc142b = 'import' . ucfirst($nht13b38b9c0f9afcf4f1ed55125d739ea736497546);
            $nhtde0cfb44b6b91bc1462616a08c41a6a1603f442e = 'afterSave' . ucfirst($nht13b38b9c0f9afcf4f1ed55125d739ea736497546);

            $nht904ffe7cb89cc7510a0a48ecb5e46eb2f4157146 = $this->_cart->$nht1991ac8330ef7c26f0cfa3ed38b1528647eae767();
            if($nht904ffe7cb89cc7510a0a48ecb5e46eb2f4157146['result'] != 'success'){
                $this->_responseAjaxJson($nht904ffe7cb89cc7510a0a48ecb5e46eb2f4157146);
                return ;
            }
            foreach($nht904ffe7cb89cc7510a0a48ecb5e46eb2f4157146['data'] as $nhtb28b7af69320201d1cf206ebf28373980add1451){
                if($nhta671a412ad5a5208f0c3501753914eea14efc15f >= $nht5a537e209151ae5fcccd6326b34b5622bcfb0578){
                    break ;
                }
                $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->_cart->$nht39a09532cef06ebac16f841db0e078b329c2b6db($nhtb28b7af69320201d1cf206ebf28373980add1451);
                $nhta671a412ad5a5208f0c3501753914eea14efc15f++;
                if($this->_cart->$nht87b132218bbbc586cbef6496f6b2c6266491d440($nhtb28b7af69320201d1cf206ebf28373980add1451)){
                    continue ;
                }
                $nht20a70aaf7e25faabeb80d477937f0a1a2d3ba60d = $this->_cart->$nht0ea88d8b780fdb250bf00a74ebbbb6b3be02518e($nhtb28b7af69320201d1cf206ebf28373980add1451);
                if($nht20a70aaf7e25faabeb80d477937f0a1a2d3ba60d['result'] == 'error'){
                    $this->_responseAjaxJson($nht20a70aaf7e25faabeb80d477937f0a1a2d3ba60d);
                    return ;
                }
                if($nht20a70aaf7e25faabeb80d477937f0a1a2d3ba60d['result'] == 'warning'){
                    $nht11f9578d05e6f7bb58a3cdd00107e9f4e3882671++;
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] .= $nht20a70aaf7e25faabeb80d477937f0a1a2d3ba60d['msg'];
                    continue ;
                }
                if($nht20a70aaf7e25faabeb80d477937f0a1a2d3ba60d['result'] == 'pass'){
                    continue ;
                }
                if($nht20a70aaf7e25faabeb80d477937f0a1a2d3ba60d['result'] == 'wait'){
                    $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61 = $this->_cart->getNotice();
                    $this->_notice['extend'] = $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['extend'];
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'process';
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560[$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e] = $this->_notice[$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e];
                    $nht47b89dd393e6b1c57f0ea17983c26b8618ebabb8 = $this->_saveNotice();
                    if(!$nht47b89dd393e6b1c57f0ea17983c26b8618ebabb8){
                        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = $this->_cart->errorDatabase();
                        $this->_responseAjaxJson($nht0ec6d150549780250a9772c06b619bcc46a0e560);
                        return ;
                    }
                    $this->_responseAjaxJson($nht0ec6d150549780250a9772c06b619bcc46a0e560);
                    return ;
                }
                $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = $nht20a70aaf7e25faabeb80d477937f0a1a2d3ba60d['data'];
                $nht62fdfbd55d19b2a4671102ad7bca17d875f8207a = $this->_cart->$nhtc8a572fdaa5da3e9a78f8a388b44d458f5dc142b($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtb28b7af69320201d1cf206ebf28373980add1451);
                if($nht62fdfbd55d19b2a4671102ad7bca17d875f8207a['result'] == 'error'){
                    $this->_responseAjaxJson($nht62fdfbd55d19b2a4671102ad7bca17d875f8207a);
                    return ;
                }
                if($nht62fdfbd55d19b2a4671102ad7bca17d875f8207a['result'] != 'success'){
                    $nht11f9578d05e6f7bb58a3cdd00107e9f4e3882671++;
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] .= $nht62fdfbd55d19b2a4671102ad7bca17d875f8207a['msg'];
                    continue ;
                }
                $nhtdfc376b378afbd9fe17722b3b58eed29e597d05d = $nht62fdfbd55d19b2a4671102ad7bca17d875f8207a['mage_id'];
                $this->_cart->$nhtde0cfb44b6b91bc1462616a08c41a6a1603f442e($nhtdfc376b378afbd9fe17722b3b58eed29e597d05d, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtb28b7af69320201d1cf206ebf28373980add1451);
            }
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'process';
            $this->_notice[$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e]['point'] = $this->_cart->getPoint($nht5a537e209151ae5fcccd6326b34b5622bcfb0578, $nhta671a412ad5a5208f0c3501753914eea14efc15f);
        } else {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
            $nht4db8113d3b95c87679df8bcdad6eaa8402694c15 = $this->_cart->createTimeToShow(time() - $this->_notice[$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e]['time_start']);
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] .= $this->_cart->consoleSuccess('Finished importing ' . $nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e . '! Run time: ' . $nht4db8113d3b95c87679df8bcdad6eaa8402694c15);
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] .= $this->_cart->getMsgStartImport($nht5ad07154a787cf8a9e97815aefcea3145122fdc1);
            if($nht5ad07154a787cf8a9e97815aefcea3145122fdc1){
                $this->_notice[$nht5ad07154a787cf8a9e97815aefcea3145122fdc1]['time_start'] = time();
            }
            $this->_notice[$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e]['finish'] = true;
            $this->_notice[$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e]['point'] = $this->_cart->getPoint($nht5a537e209151ae5fcccd6326b34b5622bcfb0578, $nhta671a412ad5a5208f0c3501753914eea14efc15f, true);
            if($nht5ad07154a787cf8a9e97815aefcea3145122fdc1){
                $this->_notice['fn_resume'] = 'import' . ucfirst($nht5ad07154a787cf8a9e97815aefcea3145122fdc1);
            }
            if($nht5ad07154a787cf8a9e97815aefcea3145122fdc1 && $this->_notice['config']['import'][$nht5ad07154a787cf8a9e97815aefcea3145122fdc1]){
                $nht26ee89f83e2f37e260aea283ced53139e9f5f5b5 = 'prepareImport' . ucfirst($nht5ad07154a787cf8a9e97815aefcea3145122fdc1);
                $this->_cart->$nht26ee89f83e2f37e260aea283ced53139e9f5f5b5();
            }
        }
        $this->_notice[$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e]['imported'] = $nhta671a412ad5a5208f0c3501753914eea14efc15f;
        $this->_notice[$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e]['id_src'] = $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595;
        $this->_notice[$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e]['error'] = $nht11f9578d05e6f7bb58a3cdd00107e9f4e3882671;
        $nht0ec6d150549780250a9772c06b619bcc46a0e560[$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e] = $this->_notice[$nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e];
        $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61 = $this->_cart->getNotice();
        $this->_notice['extend'] = $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['extend'];
        if($nht34eb4c4ef005207e8b8f916b9f1fffacccd6945e == 'reviews' && $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] == 'success'){
            $this->_cart->updateApi();
            $this->_notice['is_running'] = false;
        }
        $nht47b89dd393e6b1c57f0ea17983c26b8618ebabb8 = $this->_saveNotice();
        if(!$nht47b89dd393e6b1c57f0ea17983c26b8618ebabb8){
            $nht0ec6d150549780250a9772c06b619bcc46a0e560 = $this->_cart->errorDatabase();
            $this->_responseAjaxJson($nht0ec6d150549780250a9772c06b619bcc46a0e560);
            return ;
        }
        $this->_responseAjaxJson($nht0ec6d150549780250a9772c06b619bcc46a0e560);
        return ;
    }

    /**
     * Process after finish migration
     */
    protected function _finish(){
        $this->_initCart();
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = $this->_cart->finishImport();
        $this->_deleteNotice($this->_cart);
        return $this->_responseAjaxJson($nht0ec6d150549780250a9772c06b619bcc46a0e560);
    }

    protected function _uploadFile($nht140f86aae51ab9e1cda9b4254fe98a74eb54c1a1, $nhtdeb80331306dc3bc1cd0f2369ad72d699fe1e995, $nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c = null ,$nht1841c385846e4cfd953c3c3ec0446039fce80187 = array()){
        try{
            $nht41ae8cf7f1ec7edf8c7cc1f77e800593e14aa265 = new \Magento\Framework\File\Uploader($nht140f86aae51ab9e1cda9b4254fe98a74eb54c1a1);
            $nht41ae8cf7f1ec7edf8c7cc1f77e800593e14aa265->setAllowRenameFiles(false);
            $nht41ae8cf7f1ec7edf8c7cc1f77e800593e14aa265->setFilesDispersion(false);
            $nht41ae8cf7f1ec7edf8c7cc1f77e800593e14aa265->setAllowCreateFolders(true);
            if($nht1841c385846e4cfd953c3c3ec0446039fce80187){
                $nht41ae8cf7f1ec7edf8c7cc1f77e800593e14aa265->setAllowedExtensions($nht1841c385846e4cfd953c3c3ec0446039fce80187);
            }
            $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $nht41ae8cf7f1ec7edf8c7cc1f77e800593e14aa265->save($nhtdeb80331306dc3bc1cd0f2369ad72d699fe1e995, $nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c);
            return array(
                'result' => 'success',
                'data' => $nht37a5301a88da334dc5afc5b63979daa0f3f45e68
            );
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            return array(
                'result' => 'error',
                'msg' => $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage()
            );
        }
    }
}

__halt_compiler();

