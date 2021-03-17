<?php
/**
 * @project: CartImport
 * @author : LitExtension
 * @url    : http://litextension.com
 * @email  : litextension@gmail.com
 */

namespace LitExtension\CartImport\Block\Adminhtml;

class Index extends \Magento\Backend\Block\Template{

    protected $_objectManager;

    protected $_seo_plugin = array(
        'volusion' => array(
            'Seo_Volusion_DefaultSeo' => 'Default',
//            'Seo_Volusion_Custom' => 'Custom',
        ),
        'mivamerchant' => array(
            'Seo_Mivamerchant_DefaultSeo' => 'Default',
//            'Seo_Mivamerchant_Custom' => 'Custom',
        ),
        'yahoostore' => array(
            'Seo_Yahoostore_DefaultSeo' => 'Default',
            //'Seo_Mivamerchant_Custom' => 'Custom',
        ),
        'nopcommerce' => array(
            'Seo_Nopcommerce_DefaultSeo' => 'Default',
        ),
        'squarespace' => array(
            'Seo_Squarespace_DefaultSeo' => 'Default',
        ),
    );

    public function __construct(
        \Magento\Backend\Block\Template\Context $nhtec2727b3b71f07635f726026bef44352ec89e452,
        \Magento\Framework\ObjectManagerInterface $nhtb4d2ed732317b214b436bccb235e3e68b2da49d1
    ) {
        $this->_objectManager = $nhtb4d2ed732317b214b436bccb235e3e68b2da49d1;
        parent::__construct($nhtec2727b3b71f07635f726026bef44352ec89e452);
    }

    /**
     * Get list seo plugin is available in package
     */
    public function getSeoPlugin($nht0e1f6a930da58a371a0a7b5421914808c919eb45){
        $nhtc1e566bba075fa3e06ec405f79201e28f9b546c1 = array();
        if($nht0e1f6a930da58a371a0a7b5421914808c919eb45){
            $nhtcf38ccec37584a18ea588e11ca3a6a4620ba3a06 = isset($this->_seo_plugin[$nht0e1f6a930da58a371a0a7b5421914808c919eb45])? $this->_seo_plugin[$nht0e1f6a930da58a371a0a7b5421914808c919eb45] : array();
            if(!empty($nhtcf38ccec37584a18ea588e11ca3a6a4620ba3a06)){
                foreach($nhtcf38ccec37584a18ea588e11ca3a6a4620ba3a06 as $nhta62f2225bf70bfaccbc7f1ef2a397836717377de => $nhtfd8c7c8edae3b4c57efb1d2d3c2eb91936aba8ce){
                    if($this->checkSeoPluginExists($nhta62f2225bf70bfaccbc7f1ef2a397836717377de)){
                        $nhtc1e566bba075fa3e06ec405f79201e28f9b546c1[] = array(
                            'value' => $nhta62f2225bf70bfaccbc7f1ef2a397836717377de,
                            'label' => $nhtfd8c7c8edae3b4c57efb1d2d3c2eb91936aba8ce
                        );
                    }
                }
            }
        }
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array(
            array('value' => '', 'label' => __('Select Plugin'))
        );
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array_merge($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtc1e566bba075fa3e06ec405f79201e28f9b546c1);
        return $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd;
    }

    /**
     * Check seo plugin is available
     */
    public function checkSeoPluginExists($nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c){
        $nhtb19ef2555e9aa71a4dcc4403373953dca6c15ce9 = 'LitExtension\CartImport\Model\\' . $this->_pathToName($nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c);
        try {
            $nht1d06a0d76f000e6edd18de492383983feefced4e = $this->_objectManager->create($nhtb19ef2555e9aa71a4dcc4403373953dca6c15ce9);
            if ($nht1d06a0d76f000e6edd18de492383983feefced4e) {
                return true;
            }
        } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
            return false;
        }
        return false;
    }

    /**
     * Convert result of function toOptionArray to option of element select
     */
    public function _convertOptions($nht513f8de9259fe7658fe14d1352c54ccf070e911f, $nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        $nht950a39b6c2934bb72f2def76c71e88e9c035385f = '';
        if($nht513f8de9259fe7658fe14d1352c54ccf070e911f){
            foreach($nht513f8de9259fe7658fe14d1352c54ccf070e911f as $nht14eb14ece52df99c284b819d9f8092e50aa1613e){
                $nht950a39b6c2934bb72f2def76c71e88e9c035385f .='<option value="'.$nht14eb14ece52df99c284b819d9f8092e50aa1613e['value'].'"';
                if($nht14eb14ece52df99c284b819d9f8092e50aa1613e['value'] == $nhtf32b67c7e26342af42efabc674d441dca0a281c5){
                    $nht950a39b6c2934bb72f2def76c71e88e9c035385f .= 'selected="selected"';
                }
                $nht950a39b6c2934bb72f2def76c71e88e9c035385f .= '>'.$nht14eb14ece52df99c284b819d9f8092e50aa1613e['label'].'</option>';
            }
        }
        return $nht950a39b6c2934bb72f2def76c71e88e9c035385f;
    }

    /**
     * Get select cart type
     */
    public function getCartsOption($nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        $nht17994471a9a7a6fdf0818b65dae3008512bde344 = $this->_objectManager->create('LitExtension\CartImport\Model\Types')->toOptionArray();
        return $this->_convertOptions($nht17994471a9a7a6fdf0818b65dae3008512bde344, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Get select stores
     */
    public function getStoresOption($nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        $nht00870f5d781b62bc3f1ad6145f127687ef4b6257 = $this->_objectManager->create('Magento\Store\Model\Store')->getCollection()->toOptionArray();
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array();
        foreach($nht00870f5d781b62bc3f1ad6145f127687ef4b6257 as $nht3a21295d813c26eb287fc6b59454fb37858d63e6){
            $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = array();
            $nht37a5301a88da334dc5afc5b63979daa0f3f45e68['value'] = $nht3a21295d813c26eb287fc6b59454fb37858d63e6['value'];
            $_store = $this->_objectManager->create('Magento\Store\Model\Store')->load($nht3a21295d813c26eb287fc6b59454fb37858d63e6['value'])->getCode();
            $nht37a5301a88da334dc5afc5b63979daa0f3f45e68['label'] = $nht3a21295d813c26eb287fc6b59454fb37858d63e6['label'].' ('.$this->_scopeConfig->getValue('general/locale/code',\Magento\Store\Model\ScopeInterface::SCOPE_STORE ,$_store).')';
            $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd[] = $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
        }
        return $this->_convertOptions($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd,$nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Get select root categories
     */
    public function getCategoriesOption($nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        $nht50b9e78177f37e3c747f67abcc8af36a44f218f5 = $this->_objectManager->create('Magento\Catalog\Model\Category')->getCollection()->addRootLevelFilter()->addNameToResult()->toOptionArray();
        return $this->_convertOptions($nht50b9e78177f37e3c747f67abcc8af36a44f218f5, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Get select currencies
     */
    public function getCurrenciesOption($nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        $nhtebdea3620f4144a57de2a68a23251f5df5b471a7 = $this->_objectManager->create('Magento\Config\Model\Config\Source\Locale\Currency')->toOptionArray();
        return $this->_convertOptions($nhtebdea3620f4144a57de2a68a23251f5df5b471a7, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Get select order status
     */
    public function getOrderStatusOption($nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        $nht55e21aa76eb2e3cd08c952a7732e3b51e85ef9c9 = $this->_objectManager->create('Magento\Sales\Model\Order\Status')->getCollection()->toOptionArray();
        return $this->_convertOptions($nht55e21aa76eb2e3cd08c952a7732e3b51e85ef9c9, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Get select attribute set
     */
    public function getAttributesOption($nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        $nht75f9bdcc9115d70b3b0e6e0cc17de52ec085cc87 = $this->_objectManager->create('Magento\Eav\Model\Entity')->setType(\Magento\Catalog\Model\Product::ENTITY)->getTypeId();
        $nhtd7f67a250d8254339cc572b200990d99fe1baf29 = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Set')->getCollection()->setEntityTypeFilter($nht75f9bdcc9115d70b3b0e6e0cc17de52ec085cc87)->toOptionArray();
        return $this->_convertOptions($nhtd7f67a250d8254339cc572b200990d99fe1baf29, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    public function getCountriesOption($nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        $nht11ef521f70854107a328afbaa22be36d09084b25 = $this->_objectManager->create('Magento\Directory\Model\Country')->getCollection()->toOptionArray(false);
        return $this->_convertOptions($nht11ef521f70854107a328afbaa22be36d09084b25, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    public function getCustomerGroupOption($nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        $nht0a894db7c792e10a79e380cb0453380548bbacb5 = $this->_objectManager->create('Magento\Customer\Model\Group')->getCollection()->toOptionArray();
        return $this->_convertOptions($nht0a894db7c792e10a79e380cb0453380548bbacb5, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Get name cart type show in select
     */
    public function getCartTypeByValue($nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        $nht17994471a9a7a6fdf0818b65dae3008512bde344 = $this->_objectManager->create('LitExtension\CartImport\Model\Types')->toOptionArray();
        foreach($nht17994471a9a7a6fdf0818b65dae3008512bde344 as $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed){
            if($nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed['value'] == $nhtf32b67c7e26342af42efabc674d441dca0a281c5){
                return $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed['label'];
            }
        }
        return "No Cart";
    }

    /**
     * Get store name show in select
     */
    public function getStoreNameById($nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        if(!$nhtf32b67c7e26342af42efabc674d441dca0a281c5){
            return '';
        }
        $nht3a21295d813c26eb287fc6b59454fb37858d63e6 = $this->_objectManager->create('Magento\Store\Model\Store')->load($nhtf32b67c7e26342af42efabc674d441dca0a281c5);
        $nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e = $nht3a21295d813c26eb287fc6b59454fb37858d63e6->getCode();
        $nht64c65374dbab6fe3762748196d9d3a9610e2e5a9 = $nht3a21295d813c26eb287fc6b59454fb37858d63e6->getName().' ('.$this->_scopeConfig->getValue('general/locale/code',\Magento\Store\Model\ScopeInterface::SCOPE_STORE ,$nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e).')';
        return $nht64c65374dbab6fe3762748196d9d3a9610e2e5a9;
    }

    /**
     * Get root category name show in select
     */
    public function getCategoryNameById($nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        $nht64c65374dbab6fe3762748196d9d3a9610e2e5a9 = '';
        if(!$nhtf32b67c7e26342af42efabc674d441dca0a281c5){
            return '';
        }
        $nht50b9e78177f37e3c747f67abcc8af36a44f218f5 = $this->_objectManager->create('Magento\Catalog\Model\Category')->getCollection()->addRootLevelFilter()->addNameToResult()->toOptionArray();
        foreach($nht50b9e78177f37e3c747f67abcc8af36a44f218f5 as $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165){
            if($nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165['value'] == $nhtf32b67c7e26342af42efabc674d441dca0a281c5){
                $nht64c65374dbab6fe3762748196d9d3a9610e2e5a9 = $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165['label'];
                break ;
            }
        }
        return $nht64c65374dbab6fe3762748196d9d3a9610e2e5a9;
    }

    /**
     * Get attribute set name show in select
     */
    public function getAttributeSetNameById($nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        if(!$nhtf32b67c7e26342af42efabc674d441dca0a281c5){
            return '';
        }
        $nht64c65374dbab6fe3762748196d9d3a9610e2e5a9 = '';
        $nht75f9bdcc9115d70b3b0e6e0cc17de52ec085cc87 = $this->_objectManager->create('Magento\Eav\Model\Entity')->setType(\Magento\Catalog\Model\Product::ENTITY)->getTypeId();
        $nhtd7f67a250d8254339cc572b200990d99fe1baf29 = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Set')->getCollection()->setEntityTypeFilter($nht75f9bdcc9115d70b3b0e6e0cc17de52ec085cc87)->toOptionArray();
        foreach($nhtd7f67a250d8254339cc572b200990d99fe1baf29 as $nht2c317cd0735d9046d7675c475fa1c6dce647953f){
            if($nht2c317cd0735d9046d7675c475fa1c6dce647953f['value'] == $nhtf32b67c7e26342af42efabc674d441dca0a281c5){
                $nht64c65374dbab6fe3762748196d9d3a9610e2e5a9 = $nht2c317cd0735d9046d7675c475fa1c6dce647953f['label'];
                break ;
            }
        }
        return $nht64c65374dbab6fe3762748196d9d3a9610e2e5a9;
    }

    /**
     * Get currency name show in select
     */
    public function getCurrencyNameByCode($nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        if(!$nhtf32b67c7e26342af42efabc674d441dca0a281c5){
            return '';
        }
        $nht64c65374dbab6fe3762748196d9d3a9610e2e5a9 = '';
        $nhtebdea3620f4144a57de2a68a23251f5df5b471a7 = $this->_objectManager->create('Magento\Config\Model\Config\Source\Locale\Currency')->toOptionArray();
        foreach($nhtebdea3620f4144a57de2a68a23251f5df5b471a7 as $nht001517ee5d3d0c7f4481ec2cd77c6aefd2fa802e){
            if($nht001517ee5d3d0c7f4481ec2cd77c6aefd2fa802e['value'] == $nhtf32b67c7e26342af42efabc674d441dca0a281c5){
                $nht64c65374dbab6fe3762748196d9d3a9610e2e5a9 = $nht001517ee5d3d0c7f4481ec2cd77c6aefd2fa802e['label'];
                break;
            }
        }
        return $nht64c65374dbab6fe3762748196d9d3a9610e2e5a9;
    }

    /**
     * Get order status name show in select
     */
    public function getOrderStatusByValue($nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        if(!$nhtf32b67c7e26342af42efabc674d441dca0a281c5){
            return '';
        }
        $nht64c65374dbab6fe3762748196d9d3a9610e2e5a9 = '';
        $nht55e21aa76eb2e3cd08c952a7732e3b51e85ef9c9 = $this->_objectManager->create('Magento\Sales\Model\Order\Status')->getCollection()->toOptionArray();
        foreach($nht55e21aa76eb2e3cd08c952a7732e3b51e85ef9c9 as $nht48a3661d846478fa991a825ebd10b78671444b5b){
            if($nht48a3661d846478fa991a825ebd10b78671444b5b['value'] == $nhtf32b67c7e26342af42efabc674d441dca0a281c5){
                $nht64c65374dbab6fe3762748196d9d3a9610e2e5a9 = $nht48a3661d846478fa991a825ebd10b78671444b5b['label'];
                break ;
            }
        }
        return $nht64c65374dbab6fe3762748196d9d3a9610e2e5a9;
    }

    public function getCountryNameById($nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        $nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c = "";
        $nht8e68b3e5af636475363c23b52ade8e6064b05806 = $this->_objectManager->create('Magento\Directory\Model\Country')->load($nhtf32b67c7e26342af42efabc674d441dca0a281c5);
        if($nht8e68b3e5af636475363c23b52ade8e6064b05806){
            $nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c = $nht8e68b3e5af636475363c23b52ade8e6064b05806->getName();
        }
        return $nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c;
    }

    public function getCustomerGroupCodeById($nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        $nht64292b1c2b2e13ead8788fc8a2b8edc8c1db4ecd = $this->_objectManager->create('Magento\Customer\Model\Group')->load($nhtf32b67c7e26342af42efabc674d441dca0a281c5);
        return $nht64292b1c2b2e13ead8788fc8a2b8edc8c1db4ecd->getCustomerGroupCode();
    }

    public function checkFolderMediaPermission(){
        $nht3150ecd5e0294534a81ae047ddac559de481d774 = $this->_objectManager->get('Magento\Store\Model\Store')->getBaseMediaDir();
        if(is_writable($nht3150ecd5e0294534a81ae047ddac559de481d774)){
            return true;
        } else {
            return false;
        }
    }

    public function checkAllowUrlFOpen(){
        if(ini_get('allow_url_fopen')){
            return true;
        } else {
            return false;
        }
    }

    public function checkShowWarning(){
        if(!$this->checkFolderMediaPermission() || !$this->checkAllowUrlFOpen() || !$this->_scopeConfig->getValue('system/smtp/disable')){
            return true;
        } else {
            return false;
        }
    }

    public function getListUploadOfFirstCart(){
        $nhte7b1fff7007b635892a8f2c7c17f4fabc7aa2f8c = $this->_objectManager->create('LitExtension\CartImport\Model\Types')->toOptionArray();
        $nht4a4b13fb65765b44000af2ac22643547bb59a772 = "";
        if($nhte7b1fff7007b635892a8f2c7c17f4fabc7aa2f8c){
            foreach($nhte7b1fff7007b635892a8f2c7c17f4fabc7aa2f8c as $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9){
                $nht4a4b13fb65765b44000af2ac22643547bb59a772 = $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9['value'];
                break ;
            }
        }
        if(!$nht4a4b13fb65765b44000af2ac22643547bb59a772){
            return array();
        }
        $nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28 = $this->_objectManager->create('LitExtension\CartImport\Model\Cart');
        $nht1d06a0d76f000e6edd18de492383983feefced4e = 'LitExtension\CartImport\Model\\' . $nht77eb1db6cb81b3cb088d36ab7aae8f230dcfaa28->getCart($nht4a4b13fb65765b44000af2ac22643547bb59a772);
        $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed = $this->_objectManager->create($nht1d06a0d76f000e6edd18de492383983feefced4e);
        $nhtbb73aaafa1596e5425dc514a361ad4ef658f2758 = $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed->getListUpload();
        return $nhtbb73aaafa1596e5425dc514a361ad4ef658f2758;
    }

    public function getCartTypeFirst()
    {
        $nhte7b1fff7007b635892a8f2c7c17f4fabc7aa2f8c = $this->_objectManager->create('LitExtension\CartImport\Model\Types')->toOptionArray();
        $nht4a4b13fb65765b44000af2ac22643547bb59a772 = "";
        if($nhte7b1fff7007b635892a8f2c7c17f4fabc7aa2f8c){
            foreach($nhte7b1fff7007b635892a8f2c7c17f4fabc7aa2f8c as $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9){
                $nht4a4b13fb65765b44000af2ac22643547bb59a772 = $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9['value'];
                break ;
            }
        }
        return $nht4a4b13fb65765b44000af2ac22643547bb59a772;
    }

    public function createUploadId($nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c){
        return "file-upload-" . $nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c;
    }

    public function getConfig($nht286b0fd075953fe15becfed6d60dd15f8f97473e)
    {
        return $this->_scopeConfig->getValue(
            $nht286b0fd075953fe15becfed6d60dd15f8f97473e,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getVersion() {
        return $this->_objectManager->create('LitExtension\CartImport\Helper\Data')->getVersion();
    }

    protected function _pathToName($nht3150ecd5e0294534a81ae047ddac559de481d774, $nhtdcb16d9aacb079fe42fbde349c3319de8033ddd1 = '_') {
        $nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c = explode($nhtdcb16d9aacb079fe42fbde349c3319de8033ddd1, $nht3150ecd5e0294534a81ae047ddac559de481d774);
        $nhtc538c170bdc6b0f3bb98dce44a016a2e2d45a6e7 = array_map('ucfirst', $nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c);
        $nht8d767bf5b72373d12f0efd4406677e9ed076f592 = implode("\\", $nhtc538c170bdc6b0f3bb98dce44a016a2e2d45a6e7);
        return $nht8d767bf5b72373d12f0efd4406677e9ed076f592;
    }

}