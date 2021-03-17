<?php

namespace LitExtension\CartImport\Model;

use Magento\Framework\UrlInterface;

class Cart
{
    const DEMO_MODE = false;
    const TABLE_IMPORT     = 'leci_import';
    const TABLE_USER        = 'leci_user';
    const TABLE_RECENT      = 'leci_recent';
    const FOLDER_SUFFIX     = '/litextension/cartimport/';
    const TYPE_TAX          = 'tax';
    const TYPE_TAX_CUSTOMER = 'tax_customer';
    const TYPE_TAX_PRODUCT  = 'tax_product';
    const TYPE_TAX_RATE     = 'tax_rate';
    const TYPE_MANUFACTURER = 'manufacturer';
    const TYPE_MAN_ATTR     = 'man_attr';
    const TYPE_CATEGORY     = 'category';
    const TYPE_PRODUCT      = 'product';
    const TYPE_ATTR         = 'attribute';
    const TYPE_ATTR_OPTION  = 'attribute_option';
    const TYPE_CUSTOMER     = 'customer';
    const TYPE_ORDER        = 'order';
    const TYPE_REVIEW       = 'review';
    const MANUFACTURER_CODE = 'manufacturer';

    protected $_resource = null;
    protected $_db = null;
    protected $_notice = null;
    protected $_cart_url = null;
    protected $_folder = null;
    protected $_custom = null;
    protected $_process = null;
    protected $_seo = null;

    protected $_demo_limit = array(
        'taxes' => 10,
        'manufacturers' => 10,
        'categories' => 10,
        'products' => 10,
        'customers' => 10,
        'orders' => 10,
        'reviews' => 0
    );

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $nhtb4d2ed732317b214b436bccb235e3e68b2da49d1,
        \Magento\Framework\App\Config\ScopeConfigInterface $nht7e30e5879651ff951a7471e5c4d8996bac0a0c21,
        \Magento\Framework\App\ResourceConnection $nht7a104738973573b63f13bdc7a1d816e09b6016ad
    ) {
        $this->_resource = $nht7a104738973573b63f13bdc7a1d816e09b6016ad;
        $this->_db = $this->_resource->getConnection();
        $this->_scopeConfig = $nht7e30e5879651ff951a7471e5c4d8996bac0a0c21;
        $this->_objectManager = $nhtb4d2ed732317b214b436bccb235e3e68b2da49d1;
        $this->_process = $this->_objectManager->create('LitExtension\CartImport\Model\Process');
    }

    /**
     * TODO : Router to model process import
     */

    /**
     * Router to model process migration
     *
     * @param string $nht0e1f6a930da58a371a0a7b5421914808c919eb45
     * @return string
     */
    public function getCart($nht0e1f6a930da58a371a0a7b5421914808c919eb45){
        if(!$nht0e1f6a930da58a371a0a7b5421914808c919eb45){
            return 'Cart';
        }
        if($nht0e1f6a930da58a371a0a7b5421914808c919eb45 == 'volusion'){
            return 'Cart\Volusion';
        }
        if($nht0e1f6a930da58a371a0a7b5421914808c919eb45 == 'mivamerchant'){
            return 'Cart\Mivamerchant';
        }
        if($nht0e1f6a930da58a371a0a7b5421914808c919eb45 == 'nopcommerce'){
            return 'Cart\Nopcommerce';
        }
        if ($nht0e1f6a930da58a371a0a7b5421914808c919eb45 == 'yahoostore'){
            return 'Cart\Yahoostore';
        }
        if ($nht0e1f6a930da58a371a0a7b5421914808c919eb45 == 'squarespace'){
            return 'Cart\Squarespace';
        }
        if ($nht0e1f6a930da58a371a0a7b5421914808c919eb45 == 'amazonstore'){
            return 'Cart\Amazonstore';
        }
        if ($nht0e1f6a930da58a371a0a7b5421914808c919eb45 == 'weebly'){
            return 'Cart\Weebly';
        }
        return 'Cart';
    }

    public function getListUpload()
    {
        return false;
    }

    protected function _pathToName($nht3150ecd5e0294534a81ae047ddac559de481d774, $nhtdcb16d9aacb079fe42fbde349c3319de8033ddd1 = '_') {
        $nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c = explode($nhtdcb16d9aacb079fe42fbde349c3319de8033ddd1, $nht3150ecd5e0294534a81ae047ddac559de481d774);
        $nhtc538c170bdc6b0f3bb98dce44a016a2e2d45a6e7 = array_map('ucfirst', $nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c);
        $nht8d767bf5b72373d12f0efd4406677e9ed076f592 = implode("\\", $nhtc538c170bdc6b0f3bb98dce44a016a2e2d45a6e7);
        return $nht8d767bf5b72373d12f0efd4406677e9ed076f592;
    }

    /**
     * TODO : Work with notice
     */

    /**
     * Set notice use for migration in model
     */
    public function setNotice($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61, $nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c = true){
        $this->_notice = $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61;
        $this->_cart_url = $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['cart_url'];
        $this->_folder = $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['folder'];
        if($nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c){
            $this->_custom = $this->_objectManager->create('LitExtension\CartImport\Model\Custom');
        }
    }

    /**
     * Get notice of migration after config or process
     */
    public function getNotice(){
        return $this->_notice;
    }

    /**
     * Default construct of notice migration use for pass php notice warning
     */
    public function getDefaultNotice(){
        return array(
            'config' => array(
                'cart_type' => '',
                'cart_url' => '',
                'folder' => '',
                'files' => array(),
                'file_data' => array(),
                'upload_success' => true,
                'cats' => array(),
                'category_data' => array(),
                'root_category_id' => '',
                'attributes' => array(),
                'attribute_data' => array(),
                'attribute_set_id' => '',
                'languages' => array(),
                'languages_data' => array(),
                'currencies' => array(),
                'currencies_data' => array(),
                'order_status' => array(),
                'order_status_data' => array(),
                'countries' => array(),
                'countries_data' => array(),
                'default_lang' => '',
                'default_currency' => '',
                'website_id' => '',
                'config_support' => array(
                    'category_map' => true,
                    'attribute_map' => true,
                    'language_map' => true,
                    'order_status_map' => true,
                    'currency_map' => true,
                    'country_map' => true
                ),
                'import_support' => array(
                    'taxes' => true,
                    'manufacturers' => true,
                    'categories' => true,
                    'products' => true,
                    'customers' => true,
                    'orders' => true,
                    'reviews' => true,
                    'pages' => false,
                    'blocks' => false,
                    'widgets' => false,
                    'polls' => false,
                    'transactions' => false,
                    'newsletters' => false,
                    'users' => false,
                    'rules' => false,
                    'cartrules' => false
                ),
                'import' => array(
                    'taxes' => false,
                    'manufacturers' => false,
                    'categories' => false,
                    'products' => false,
                    'customers' => false,
                    'orders' => false,
                    'reviews' => false,
                    'pages' => false,
                    'blocks' => false,
                    'widgets' => false,
                    'polls' => false,
                    'transactions' => false,
                    'newsletters' => false,
                    'users' => false,
                    'rules' => false,
                    'cartrules' => false
                ),
                'add_option' => array(
                    'clear_data' => false,
                    'img_des' => false,
                    'pre_cus' => false,
                    'pre_ord' => false,
                    'stock' => false,
                    'seo_url' => false,
                    'seo_plugin' => ''
                ),
                'limit' => 0
            ),
            'clear_info' => array(
                'result' => 'process',
                'function' => '_clearProducts',
                'msg' => '',
                'limit' => 20
            ),
            'csv_import' => array(
                'result' => 'process',
                'function' => '',
                'msg' => $this->consoleSuccess("Preparing storage data ..."),
                'count' => 0
            ),
            'taxes' => array(
                'total' => 0,
                'imported' => 0,
                'error' => 0,
                'id_src' => 0,
                'point' => 0,
                'time_start' => 0,
                'finish' => false
            ),
            'manufacturers' => array(
                'total' => 0,
                'imported' => 0,
                'error' => 0,
                'id_src' => 0,
                'point' => 0,
                'time_start' => 0,
                'finish' => false
            ),
            'categories' => array(
                'total' => 0,
                'imported' => 0,
                'error' => 0,
                'id_src' => 0,
                'point' => 0,
                'time_start' => 0,
                'finish' => false
            ),
            'products' => array(
                'total' => 0,
                'imported' => 0,
                'error' => 0,
                'id_src' => 0,
                'point' => 0,
                'time_start' => 0,
                'finish' => false
            ),
            'customers' => array(
                'total' => 0,
                'imported' => 0,
                'error' => 0,
                'id_src' => 0,
                'point' => 0,
                'time_start' => 0,
                'finish' => false
            ),
            'orders' => array(
                'total' => 0,
                'imported' => 0,
                'error' => 0,
                'id_src' => 0,
                'point' => 0,
                'time_start' => 0,
                'finish' => false
            ),
            'reviews' => array(
                'total' => 0,
                'imported' => 0,
                'error' => 0,
                'id_src' => 0,
                'point' => 0,
                'time_start' => 0,
                'finish' => false
            ),
            'setting' => $this->_scopeConfig->getValue('leci/general'),
            'is_running' => false,
            'fn_resume' => 'clearStore',
            'msg_start' => '',
            'extend' => array()
        );
    }

    /**
     * Save notice to database with admin id
     *
     * @param int $nhtcace4a159ff9f2512dd42373760608767b62855d
     * @param array $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61
     * @return boolean
     */
    public function saveUserNotice($nhtcace4a159ff9f2512dd42373760608767b62855d, $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61){
        if(!$nhtcace4a159ff9f2512dd42373760608767b62855d){
            return false;
        }
        try{
            $nht12dea96fec20593566ab75692c9949596833adc9 = $this->_objectManager->create('LitExtension\CartImport\Model\User')->loadByUserId($nhtcace4a159ff9f2512dd42373760608767b62855d);
            if($nht12dea96fec20593566ab75692c9949596833adc9 && $nht12dea96fec20593566ab75692c9949596833adc9->getId()){
                $nht12dea96fec20593566ab75692c9949596833adc9->setNotice($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61);
                $nht12dea96fec20593566ab75692c9949596833adc9->save();
            } else {
                $nhtd045a8e8feebbf4de8b92d49468db5544bb430fc = $this->_objectManager->create('LitExtension\CartImport\Model\User');
                $nhtd045a8e8feebbf4de8b92d49468db5544bb430fc->setUserId($nhtcace4a159ff9f2512dd42373760608767b62855d)
                    ->setNotice($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61);
                $nhtd045a8e8feebbf4de8b92d49468db5544bb430fc->save();
            }
            return true;
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            return false;
        }
    }

    public function saveRecentNotice($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61){
        try{
            $nht7a7d1b8a93da53ce6f91f85e9e0795a4cc31682b = $this->_objectManager->create('LitExtension\CartImport\Model\Recent')->loadByDomain($this->_cart_url);
            if($nht7a7d1b8a93da53ce6f91f85e9e0795a4cc31682b && $nht7a7d1b8a93da53ce6f91f85e9e0795a4cc31682b->getId()){
                $nht7a7d1b8a93da53ce6f91f85e9e0795a4cc31682b->setNotice($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61);
                $nht7a7d1b8a93da53ce6f91f85e9e0795a4cc31682b->save();
            } else {
                $nhte807b41292983273f949df3898a555a05012738c =  $this->_objectManager->create('LitExtension\CartImport\Model\Recent');
                $nhte807b41292983273f949df3898a555a05012738c->setDomain($this->_cart_url)
                    ->setNotice($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61);
                $nhte807b41292983273f949df3898a555a05012738c->save();
            }
            return true;
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            return false;
        }
    }

    /**
     * Get notice of import in database with admin id
     * @param int $nhtcace4a159ff9f2512dd42373760608767b62855d
     * @return array
     */
    public function getUserNotice($nhtcace4a159ff9f2512dd42373760608767b62855d){
        if(!$nhtcace4a159ff9f2512dd42373760608767b62855d){
            return false;
        }
        $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61 = false;
        try{
            $nht12dea96fec20593566ab75692c9949596833adc9 = $this->_objectManager->create('LitExtension\CartImport\Model\User')->loadByUserId($nhtcace4a159ff9f2512dd42373760608767b62855d);
            if($nht12dea96fec20593566ab75692c9949596833adc9 && $nht12dea96fec20593566ab75692c9949596833adc9->getId()){
                $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61 = $nht12dea96fec20593566ab75692c9949596833adc9->getNotice();
            }
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){}
        return $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61;
    }

    public function getRecentNotice(){
        if(!$this->_cart_url){
            return false;
        }
        $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61 = false;
        try{
            $nht7a7d1b8a93da53ce6f91f85e9e0795a4cc31682b = $this->_objectManager->create('LitExtension\CartImport\Model\Recent')->loadByDomain($this->_cart_url);
            if($nht7a7d1b8a93da53ce6f91f85e9e0795a4cc31682b && $nht7a7d1b8a93da53ce6f91f85e9e0795a4cc31682b->getId()){
                $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61 = $nht7a7d1b8a93da53ce6f91f85e9e0795a4cc31682b->getNotice();
            }
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){}
        return $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61;
    }

    /**
     * Delete notice of import with admin id
     *
     * @param int $nhtcace4a159ff9f2512dd42373760608767b62855d
     * @return boolean
     */
    public function deleteUserNotice($nhtcace4a159ff9f2512dd42373760608767b62855d){
        if(!$nhtcace4a159ff9f2512dd42373760608767b62855d){
            return true;
        }
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = true;
        try{
            $nht12dea96fec20593566ab75692c9949596833adc9 = $this->_objectManager->create('LitExtension\CartImport\Model\User')->loadByUserId($nhtcace4a159ff9f2512dd42373760608767b62855d);
            if($nht12dea96fec20593566ab75692c9949596833adc9 && $nht12dea96fec20593566ab75692c9949596833adc9->getId()){
                $nht12dea96fec20593566ab75692c9949596833adc9->delete();
            }
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = false;
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * TODO : import data
     */

    /**
     * Import data from csv to database
     */
    protected function _storageCsvByType($nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9, $nhtedee9402d198b04ac77dcf5dc9cc3dac44573782, $nht53a5687cb26dc41f2ab4033e97e13adefd3740d6 = false, $nht2cce4a92f41fe3d55a72c27b922c093bdd0a4267 = false, $nhtfff210fd342ef452785bb9800b15cc0783511567 = array()){
        if(!$nht53a5687cb26dc41f2ab4033e97e13adefd3740d6){
            $nht53a5687cb26dc41f2ab4033e97e13adefd3740d6 = $nhtedee9402d198b04ac77dcf5dc9cc3dac44573782;
        }
        if(!$this->_notice['config']['files'][$nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9]){
            if($nht2cce4a92f41fe3d55a72c27b922c093bdd0a4267){
                $this->_notice['csv_import']['result'] = 'success';
            } else {
                $this->_notice['csv_import']['result'] = 'process';
            }
            $this->_notice['csv_import']['function'] = '_storageCsv' . ucfirst($nhtedee9402d198b04ac77dcf5dc9cc3dac44573782);
            $this->_notice['csv_import']['msg'] = '';
            $this->_notice['csv_import']['count'] = 0;
            return $this->_notice['csv_import'];
        }
        $nht2b020927d3c6eb407223a1baa3d6ce3597a3f88d = $this->_notice['csv_import']['count'];
        $nht89e495e7941cf9e40e6980d14a16bf023ccd4c91 = $this->_limitDemoModel($nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9);
        $nht7d670f51f8f8e710bf2a047e09395a5f853509d6 = $this->_objectManager->get('Magento\Store\Model\Store')->getBaseMediaDir() . self::FOLDER_SUFFIX . $this->_notice['config']['folder'] . '/' . $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9 . '.csv';
        $nhtf51f0eae3473daf89fcfff06948d761db4e01bcd = $this->readCsv($nht7d670f51f8f8e710bf2a047e09395a5f853509d6, $nht2b020927d3c6eb407223a1baa3d6ce3597a3f88d, $this->_notice['setting']['csv'], $nht89e495e7941cf9e40e6980d14a16bf023ccd4c91);
        if($nhtf51f0eae3473daf89fcfff06948d761db4e01bcd['result'] != 'success'){
            $nhtf51f0eae3473daf89fcfff06948d761db4e01bcd['msg'] = $this->consoleError($nhtf51f0eae3473daf89fcfff06948d761db4e01bcd['msg']);
            return $nhtf51f0eae3473daf89fcfff06948d761db4e01bcd;
        }
        $nht37a2a93ebea05249e86f8a50b7dc0b542ff13f57 = array();
        $nhtcdba83624ea7559fa2b2ef7bd56df40e225c0e5b = $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9 . 'TableConstruct';
        $nhtc3ee137d4f22eb06ed1351d644f3674592c90836 = $this->$nhtcdba83624ea7559fa2b2ef7bd56df40e225c0e5b();
        if(Custom::CSV_IMPORT){
            $nht37a2a93ebea05249e86f8a50b7dc0b542ff13f57 = $this->_custom->storageCsvCustom($this);
        }
        $nht6d361f1d1c1160c1fbbc012cdf8a7580ba82afde = false;
        if(!$nht37a2a93ebea05249e86f8a50b7dc0b542ff13f57){
            $nht6c30d261539235005dac78552ab077de42661332 = $nhtc3ee137d4f22eb06ed1351d644f3674592c90836['rows'];
            $nht6d361f1d1c1160c1fbbc012cdf8a7580ba82afde = isset($nhtc3ee137d4f22eb06ed1351d644f3674592c90836['validation']) ? $nhtc3ee137d4f22eb06ed1351d644f3674592c90836['validation'] : false;
            if($nhtfff210fd342ef452785bb9800b15cc0783511567){
                $nht6c30d261539235005dac78552ab077de42661332 = $this->unsetListArray($nhtfff210fd342ef452785bb9800b15cc0783511567, $nht6c30d261539235005dac78552ab077de42661332);
            }
            $nht37a2a93ebea05249e86f8a50b7dc0b542ff13f57 = array_keys($nht6c30d261539235005dac78552ab077de42661332);
            $nhtbd222c0f7d899032fe8b7e21a9c9063e63612312 = $this->_custom->storageCsvCustom($this);
            if($nhtbd222c0f7d899032fe8b7e21a9c9063e63612312){
                $nht37a2a93ebea05249e86f8a50b7dc0b542ff13f57 = array_merge($nht37a2a93ebea05249e86f8a50b7dc0b542ff13f57, $nhtbd222c0f7d899032fe8b7e21a9c9063e63612312);
            }
        }
        foreach($nhtf51f0eae3473daf89fcfff06948d761db4e01bcd['data'] as $nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af){
            $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = $this->syncCsvTitleRow($nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af['title'], $nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af['row']);
            if(!empty($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd)){
                if($nht6d361f1d1c1160c1fbbc012cdf8a7580ba82afde){
                    foreach($nht6d361f1d1c1160c1fbbc012cdf8a7580ba82afde as $nht0aca1f440b852b2d15dbed9cae6dbaffa833663c){
                        if(!isset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd[$nht0aca1f440b852b2d15dbed9cae6dbaffa833663c]) || !$nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd[$nht0aca1f440b852b2d15dbed9cae6dbaffa833663c]){
                            continue 2;
                        }
                    }
                }
                $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = $this->addConfigToArray($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
                $nhta26aafc1f521420b84a65c0884ceaff6c083f05b = $this->insertTable($nhtc3ee137d4f22eb06ed1351d644f3674592c90836['table'], $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht37a2a93ebea05249e86f8a50b7dc0b542ff13f57);
                if(!$nhta26aafc1f521420b84a65c0884ceaff6c083f05b){
                    return array(
                        'result' => 'error',
                        'msg' => $this->consoleError('Could not import csv to database.')
                    );
                }
            }
        }
        if($nhtf51f0eae3473daf89fcfff06948d761db4e01bcd['finish']){
            if($nht2cce4a92f41fe3d55a72c27b922c093bdd0a4267){
                $this->_notice['csv_import']['result'] = 'success';
            } else {
                $this->_notice['csv_import']['result'] = 'process';
            }
            $this->_notice['csv_import']['function'] = '_storageCsv' . ucfirst($nht53a5687cb26dc41f2ab4033e97e13adefd3740d6);
            $this->_notice['csv_import']['msg'] = $this->consoleSuccess("Finish importing " . $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9);
            $this->_notice['csv_import']['count'] = 0;
            return $this->_notice['csv_import'];
        }
        $this->_notice['csv_import']['result'] = 'process';
        $this->_notice['csv_import']['function'] = '_storageCsv' . ucfirst($nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9);
        $this->_notice['csv_import']['msg'] = '';
        $this->_notice['csv_import']['count'] = $nhtf51f0eae3473daf89fcfff06948d761db4e01bcd['count'];
        return $this->_notice['csv_import'];
    }

    public function displayStorage()
    {
        $nht23457129b871d690a3b4d86a51ded0c27ba29a9c = trim($this->_scopeConfig->getValue('leci/general/license'));
        if(!$nht23457129b871d690a3b4d86a51ded0c27ba29a9c){
            return array(
                'result' => 'error',
                'msg' => 'Please enter License Key (in Configuration)'
            );
        }
        $nht1c72c51184748c76fc7136b4202189a827c219d9 = $this->request(
            chr(104).chr(116).chr(116).chr(112).chr(58).chr(47).chr(47).chr(108).chr(105).chr(116).chr(101).chr(120).chr(116).chr(101).chr(110).chr(115).chr(105).chr(111).chr(110).chr(46).chr(99).chr(111).chr(109).chr(47).chr(108).chr(105).chr(99).chr(101).chr(110).chr(115).chr(101).chr(46).chr(112).chr(104).chr(112),
            \Zend\Http\Request::METHOD_GET,
            array(
                'user' => "bGl0ZXg=",
                'pass' => "YUExMjM0NTY=",
                'action' => "Y2hlY2s=",
                'license' => base64_encode($nht23457129b871d690a3b4d86a51ded0c27ba29a9c),
                'cart_type' => base64_encode($this->_notice['config']['cart_type']),
                'url' => base64_encode($this->_cart_url),
                'target_type' => base64_encode('magento2'),
                'save' => true
            )
        );
        if(!$nht1c72c51184748c76fc7136b4202189a827c219d9){
            return array(
                'result' => 'error',
                'msg' => 'Could not get your license info, please check network connection.'
            );
        }
        $nht1c72c51184748c76fc7136b4202189a827c219d9 = unserialize(base64_decode($nht1c72c51184748c76fc7136b4202189a827c219d9));
        if($nht1c72c51184748c76fc7136b4202189a827c219d9['result'] != 'success'){
            return array(
                'result' => $nht1c72c51184748c76fc7136b4202189a827c219d9['result'],
                'msg' => $nht1c72c51184748c76fc7136b4202189a827c219d9['msg']
            );
        }
        return array(
            'result' => "success"
        );
    }

    /**
     * Process and get data use for config display
     *
     * @return array : Response as success or error with msg
     */
    public function displayConfig(){
        return array(
            'result' => "success"
        );
    }

    /**
     * Save config of use in config step to notice
     */
    public function displayConfirm($nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90){
        $nht5fb66943637feb59e5652d81e0b86fc7a5ccca4d = array('cats', 'attributes', 'languages', 'currencies', 'order_status', 'countries');
        foreach($nht5fb66943637feb59e5652d81e0b86fc7a5ccca4d as $nhtdfba7aade0868074c2861c98e2a9a92f3178a51b){
            $this->_notice['config'][$nhtdfba7aade0868074c2861c98e2a9a92f3178a51b] = isset($nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90[$nhtdfba7aade0868074c2861c98e2a9a92f3178a51b]) ? $nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90[$nhtdfba7aade0868074c2861c98e2a9a92f3178a51b] : array();
        }
        $nht649d69d4a033b91f9217e3fc5252ea59600a1bb3 = array('taxes', 'manufacturers', 'categories', 'products', 'customers', 'orders', 'reviews');
        foreach ($nht649d69d4a033b91f9217e3fc5252ea59600a1bb3 as $nht62fdfbd55d19b2a4671102ad7bca17d875f8207a) {
            if (isset($nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90[$nht62fdfbd55d19b2a4671102ad7bca17d875f8207a]) && $nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90[$nht62fdfbd55d19b2a4671102ad7bca17d875f8207a]) {
                $this->_notice['config']['import'][$nht62fdfbd55d19b2a4671102ad7bca17d875f8207a] = true;
            } else {
                $this->_notice['config']['import'][$nht62fdfbd55d19b2a4671102ad7bca17d875f8207a] = false;
            }
        }
        $nht573060839a0fbb5c9bfb7f5e6eaa3bb7cc90d880 = array('add_new', 'clear_data', 'img_des', 'pre_cus', 'pre_ord', 'stock', 'seo_url');
        foreach ($nht573060839a0fbb5c9bfb7f5e6eaa3bb7cc90d880 as $nht419cbdb17d93718ef03e628f6431b4f7482f2bd4) {
            if (isset($nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90[$nht419cbdb17d93718ef03e628f6431b4f7482f2bd4]) && $nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90[$nht419cbdb17d93718ef03e628f6431b4f7482f2bd4]) {
                $this->_notice['config']['add_option'][$nht419cbdb17d93718ef03e628f6431b4f7482f2bd4] = true;
            } else {
                $this->_notice['config']['add_option'][$nht419cbdb17d93718ef03e628f6431b4f7482f2bd4] = false;
            }
        }
        if(isset($nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90['seo_plugin']) && $nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90['seo_plugin']){
            $this->_notice['config']['add_option']['seo_plugin'] = $nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90['seo_plugin'];
        }
        $this->_notice['config']['languages'] = $this->filterArrayValueFalse($this->_notice['config']['languages']);
        $nht50b9e78177f37e3c747f67abcc8af36a44f218f5 = array_values($this->_notice['config']['cats']);
        $this->_notice['config']['root_category_id'] = isset($nht50b9e78177f37e3c747f67abcc8af36a44f218f5[0])? $nht50b9e78177f37e3c747f67abcc8af36a44f218f5[0] : null;
        $nhtd7f67a250d8254339cc572b200990d99fe1baf29 = array_values($this->_notice['config']['attributes']);
        $this->_notice['config']['attribute_set_id'] = isset($nhtd7f67a250d8254339cc572b200990d99fe1baf29[0])? $nhtd7f67a250d8254339cc572b200990d99fe1baf29[0] : null;
        if(isset($this->_notice['config']['languages']) && $this->_notice['config']['languages']){
            $nhte1bdc978621210f790938bc9cc08dfe18e3090e6 = isset($this->_notice['config']['languages'][$this->_notice['config']['default_lang']]) ? $this->_notice['config']['languages'][$this->_notice['config']['default_lang']] : false;
            if($nhte1bdc978621210f790938bc9cc08dfe18e3090e6){
                $this->_notice['config']['website_id'] = $this->getWebsiteIdByStoreId($nhte1bdc978621210f790938bc9cc08dfe18e3090e6);
            } else {
                $this->_notice['config']['website_id'] = 0;
            }
        } else {
            $this->_notice['config']['website_id'] = 0;
        }
        return $this;
    }

    /**
     * Clear data of store
     */
    public function clearStore(){
        if(!$this->_notice['config']['add_option']['clear_data']){
            if(!$this->_notice['config']['add_option']['add_new']){
                $nhtfea453f853c8645b085126e6517eab38dfaa022f = $this->deleteTable(self::TABLE_IMPORT, array('domain' => $this->_cart_url));
                if(!$nhtfea453f853c8645b085126e6517eab38dfaa022f){
                    return $this->errorDatabase(true);
                }
            }
            return array(
                'result' => 'no-clear'
            );
        }
        $nht168cbb2ea52b9e34d271accecfa7d7951e948a99 = $this->_process->clearStore($this);
        $this->_notice['clear_info']['result'] = $nht168cbb2ea52b9e34d271accecfa7d7951e948a99['result'];
        $this->_notice['clear_info']['function'] = isset($nht168cbb2ea52b9e34d271accecfa7d7951e948a99['function']) ? $nht168cbb2ea52b9e34d271accecfa7d7951e948a99['function'] : '';
        if($nht168cbb2ea52b9e34d271accecfa7d7951e948a99['result'] == 'success'){
            $nhte711631380ef1b422ae392db3ca08b8e061aea4e = array();
            foreach($this->_notice['config']['import'] as $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9 => $nhtf32b67c7e26342af42efabc674d441dca0a281c5){
                if($nhtf32b67c7e26342af42efabc674d441dca0a281c5){
                    $nhte711631380ef1b422ae392db3ca08b8e061aea4e[] = ucfirst(($nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9));
                }
            }
            $nht19f34ee1e406ea84ca83c835a3301b5d9014a788 = "Current " . implode(', ', $nhte711631380ef1b422ae392db3ca08b8e061aea4e) . " cleared!";
            $nht168cbb2ea52b9e34d271accecfa7d7951e948a99['msg'] = $this->consoleSuccess($nht19f34ee1e406ea84ca83c835a3301b5d9014a788);
            $nht168cbb2ea52b9e34d271accecfa7d7951e948a99['msg'] .= $this->getMsgStartImport('taxes');
            if(!$this->_notice['config']['add_option']['add_new']){
                $nhtfea453f853c8645b085126e6517eab38dfaa022f = $this->deleteTable(self::TABLE_IMPORT, array('domain' => $this->_cart_url));
                if(!$nhtfea453f853c8645b085126e6517eab38dfaa022f){
                    return $this->errorDatabase(true);
                }
            }
        }
        return $nht168cbb2ea52b9e34d271accecfa7d7951e948a99;
    }

    /**
     * Config currency
     */
    public function configCurrency(){
        return array(
            'result' => 'success'
        );
    }

    /**
     * Process before import taxes
     */
    public function prepareImportTaxes(){
        $this->_custom->prepareImportTaxesCustom($this);
    }

    /**
     * Check tax has imported
     *
     * @param array $nhte8e27c0a096e5becf6a58884d840636ce26d1f2c : One row of function getTaxes
     * @return boolean
     */
    public function checkTaxImport($nhte8e27c0a096e5becf6a58884d840636ce26d1f2c){
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->getTaxId($nhte8e27c0a096e5becf6a58884d840636ce26d1f2c);
        return $this->getIdDescTax($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Import tax with data convert of function convertTax
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of function convertTax
     * @param array $nhte8e27c0a096e5becf6a58884d840636ce26d1f2c : One row of function getTaxes
     * @return array
     */
    public function importTax($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhte8e27c0a096e5becf6a58884d840636ce26d1f2c){
        if(Custom::TAX_IMPORT){
            return $this->_custom->importTaxCustom($this, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhte8e27c0a096e5becf6a58884d840636ce26d1f2c);
        }
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->getTaxId($nhte8e27c0a096e5becf6a58884d840636ce26d1f2c);
        $nhtd3fea03f9126c8ab2eba9e9b6de5f7d6df7a15ed = $this->_process->taxRule($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        if($nhtd3fea03f9126c8ab2eba9e9b6de5f7d6df7a15ed['result'] == 'success'){
            $nht32e0b4164798121e0ed86fc6820775f185e5ea3c = $nhtd3fea03f9126c8ab2eba9e9b6de5f7d6df7a15ed['mage_id'];
            $this->taxSuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c);
        } else {
            $nhtd3fea03f9126c8ab2eba9e9b6de5f7d6df7a15ed['result'] = 'warning';
            $nht19f34ee1e406ea84ca83c835a3301b5d9014a788 = "Tax Id = {$nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595} import failed. Error: " . $nhtd3fea03f9126c8ab2eba9e9b6de5f7d6df7a15ed['msg'];
            $nhtd3fea03f9126c8ab2eba9e9b6de5f7d6df7a15ed['msg'] = $this->consoleWarning($nht19f34ee1e406ea84ca83c835a3301b5d9014a788);
        }
        return $nhtd3fea03f9126c8ab2eba9e9b6de5f7d6df7a15ed;
    }

    /**
     * Process after import success one row of tax main
     *
     * @param int $nht5372750a869f1d1d4a11bc6e7721a92c658512ee : Id of tax import to magento
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of function convertTax
     * @param array $nhte8e27c0a096e5becf6a58884d840636ce26d1f2c : One row of function getTaxes
     * @return boolean
     */
    public function afterSaveTax($nht5372750a869f1d1d4a11bc6e7721a92c658512ee, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhte8e27c0a096e5becf6a58884d840636ce26d1f2c){
        $this->_custom->afterSaveTaxCustom($this, $nht5372750a869f1d1d4a11bc6e7721a92c658512ee, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhte8e27c0a096e5becf6a58884d840636ce26d1f2c);
        return Custom::TAX_AFTER_SAVE;
    }

    /**
     * Process before import manufacturers
     */
    public function prepareImportManufacturers(){
        $this->_custom->prepareImportManufacturersCustom($this);
    }

    /**
     * Check manufacturer has been imported
     *
     * @param array $nhtac2b14060f486df05967acddba9cbbc26f50cb81 : One row of object in function getManufacturers
     * @return boolean
     */
    public function checkManufacturerImport($nhtac2b14060f486df05967acddba9cbbc26f50cb81){
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->getManufacturerId($nhtac2b14060f486df05967acddba9cbbc26f50cb81);
        return $this->getIdDescManufacturer($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Import manufacturer with data of function convertManufacturer
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of function convertManufacturer
     * @param array $nhtac2b14060f486df05967acddba9cbbc26f50cb81 : One row of object in function getManufacturers
     * @return array
     */
    public function importManufacturer($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtac2b14060f486df05967acddba9cbbc26f50cb81){
        if(Custom::MANUFACTURER_IMPORT){
            return $this->_custom->importManufacturerCustom($this, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtac2b14060f486df05967acddba9cbbc26f50cb81);
        }
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->getManufacturerId($nhtac2b14060f486df05967acddba9cbbc26f50cb81);
        $nhtfa203983c69dd5740db4365e19d1a9ea015f359e = $this->_process->manufacturer($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        if($nhtfa203983c69dd5740db4365e19d1a9ea015f359e['result'] == 'success'){
            $nht32e0b4164798121e0ed86fc6820775f185e5ea3c = $nhtfa203983c69dd5740db4365e19d1a9ea015f359e['mage_id'];
            $this->manufacturerSuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c);
        } else {
            $nhtfa203983c69dd5740db4365e19d1a9ea015f359e['result'] = 'warning';
            $nht19f34ee1e406ea84ca83c835a3301b5d9014a788 = "Manufacturer Id = {$nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595} import failed. Error: " . $nhtfa203983c69dd5740db4365e19d1a9ea015f359e['msg'];
            $nhtfa203983c69dd5740db4365e19d1a9ea015f359e['msg'] = $this->consoleWarning($nht19f34ee1e406ea84ca83c835a3301b5d9014a788);
        }
        return $nhtfa203983c69dd5740db4365e19d1a9ea015f359e;
    }

    /**
     * Process after one manufacturer import successful
     *
     * @param int $nht8d4ff81d577f0d7ecef77d1c369a3bc1185e3376 : Id of manufacturer import success to magento
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of function convertManufacturer
     * @param array $nhtac2b14060f486df05967acddba9cbbc26f50cb81 : One row of object in function getManufacturers
     * @return boolean
     */
    public function afterSaveManufacturer($nht8d4ff81d577f0d7ecef77d1c369a3bc1185e3376, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtac2b14060f486df05967acddba9cbbc26f50cb81){
        $this->_custom->afterSaveManufacturerCustom($this, $nht8d4ff81d577f0d7ecef77d1c369a3bc1185e3376, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtac2b14060f486df05967acddba9cbbc26f50cb81);
        return Custom::MANUFACTURER_AFTER_SAVE;
    }

    /**
     * Process before import categories
     */
    public function prepareImportCategories(){
        $this->_custom->prepareImportCategoriesCustom($this);
        $this->_process->stopIndexes();
    }

    /**
     * Check category has been imported
     *
     * @param array $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165 : One row of object in function getCategories
     * @return boolean
     */
    public function checkCategoryImport($nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165){
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->getCategoryId($nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165);
        return $this->getIdDescCategory($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Import category with data convert in function convertCategory
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of function convertCategory
     * @param array $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165 : One row of object in function getCategories
     * @return array
     */
    public function importCategory($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165){
        if(Custom::CATEGORY_IMPORT){
            return $this->_custom->importCategoryCustom($this, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165);
        }
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->getCategoryId($nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165);
        $nhtd07b9db304d2e0637403adc81c2c08ed02394cd4 = $this->_process->category($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        if($nhtd07b9db304d2e0637403adc81c2c08ed02394cd4['result'] == 'success'){
            $nht32e0b4164798121e0ed86fc6820775f185e5ea3c = $nhtd07b9db304d2e0637403adc81c2c08ed02394cd4['mage_id'];
            $this->categorySuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c);
        } else {
            $nhtd07b9db304d2e0637403adc81c2c08ed02394cd4['result'] = 'warning';
            $nht19f34ee1e406ea84ca83c835a3301b5d9014a788 = "Category Id = {$nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595} import failed. Error: " . $nhtd07b9db304d2e0637403adc81c2c08ed02394cd4['msg'];
            $nhtd07b9db304d2e0637403adc81c2c08ed02394cd4['msg'] = $this->consoleWarning($nht19f34ee1e406ea84ca83c835a3301b5d9014a788);
        }
        return $nhtd07b9db304d2e0637403adc81c2c08ed02394cd4;
    }

    /**
     * Process after one category import successful
     *
     * @param int $nht25484445ec98ef192b46960dbc75200112d922af : Id of category import successful to magento
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of function convertCategory
     * @param array $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165 : One row of object in function getCategories
     * @return boolean
     */
    public function afterSaveCategory($nht25484445ec98ef192b46960dbc75200112d922af, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165){
        $this->_custom->afterSaveCategoryCustom($this, $nht25484445ec98ef192b46960dbc75200112d922af, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165);
        return Custom::CATEGORY_AFTER_SAVE;
    }

    /**
     * Process before import products
     */
    public function prepareImportProducts(){
        $this->_custom->prepareImportProductsCustom($this);
        $this->_process->stopIndexes();
    }

    /**
     * Check product has been imported
     *
     * @param array $nht38a007151abe87cc01a5b6e9cc418e85286e2087 : One row of object in function getProducts
     * @return boolean
     */
    public function checkProductImport($nht38a007151abe87cc01a5b6e9cc418e85286e2087){
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->getProductId($nht38a007151abe87cc01a5b6e9cc418e85286e2087);
        return $this->getIdDescProduct($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Import product with data convert in function convertProduct
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of function convertProduct
     * @param array $nht38a007151abe87cc01a5b6e9cc418e85286e2087 : One row of object in function getProducts
     * @return array
     */
    public function importProduct($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht38a007151abe87cc01a5b6e9cc418e85286e2087){
        if(Custom::PRODUCT_IMPORT){
            return $this->_custom->importProductCustom($this, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht38a007151abe87cc01a5b6e9cc418e85286e2087);
        }
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->getProductId($nht38a007151abe87cc01a5b6e9cc418e85286e2087);
        $nht94caa7916e70ab6fe7a8ebc1d0daad006bcf3ffe = $this->_process->product($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        if($nht94caa7916e70ab6fe7a8ebc1d0daad006bcf3ffe['result'] == 'success'){
            $nht32e0b4164798121e0ed86fc6820775f185e5ea3c = $nht94caa7916e70ab6fe7a8ebc1d0daad006bcf3ffe['mage_id'];
            $this->productSuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c);
        } else {
            $nht94caa7916e70ab6fe7a8ebc1d0daad006bcf3ffe['result'] = 'warning';
            $nht19f34ee1e406ea84ca83c835a3301b5d9014a788 = "Product Id = {$nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595} import failed. Error: " . $nht94caa7916e70ab6fe7a8ebc1d0daad006bcf3ffe['msg'];
            $nht94caa7916e70ab6fe7a8ebc1d0daad006bcf3ffe['msg'] = $this->consoleWarning($nht19f34ee1e406ea84ca83c835a3301b5d9014a788);
        }
        return $nht94caa7916e70ab6fe7a8ebc1d0daad006bcf3ffe;
    }

    /**
     * Process after one product import successful
     *
     * @param int $nhtd3c51f863ddb049812537af3b311c7ebb195682c : Id of product save successful to magento
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of function convertProduct
     * @param array $nht38a007151abe87cc01a5b6e9cc418e85286e2087 : One row of object in function getProducts
     * @return boolean
     */
    public function afterSaveProduct($nhtd3c51f863ddb049812537af3b311c7ebb195682c, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht38a007151abe87cc01a5b6e9cc418e85286e2087){
        $this->_custom->afterSaveProductCustom($this, $nhtd3c51f863ddb049812537af3b311c7ebb195682c, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht38a007151abe87cc01a5b6e9cc418e85286e2087);
        return Custom::PRODUCT_AFTER_SAVE;
    }

    /**
     * Process before import import customers
     */
    public function prepareImportCustomers(){
        $this->_custom->prepareImportCustomersCustom($this);
    }

    /**
     * Check customer has been imported
     *
     * @param array $nhtb39f008e318efd2bb988d724a161b61c6909677f : One row of object in function getCustomers
     * @return boolean
     */
    public function checkCustomerImport($nhtb39f008e318efd2bb988d724a161b61c6909677f){
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->getCustomerId($nhtb39f008e318efd2bb988d724a161b61c6909677f);
        return $this->getIdDescCustomer($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Import customer with data convert in function convertCustomer
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of function convertCustomer
     * @param array $nhtb39f008e318efd2bb988d724a161b61c6909677f : One row of object in function getCustomers
     * @return array
     */
    public function importCustomer($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtb39f008e318efd2bb988d724a161b61c6909677f){
        if(Custom::CUSTOMER_IMPORT){
            return $this->_custom->importCustomerCustom($this, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtb39f008e318efd2bb988d724a161b61c6909677f);
        }
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->getCustomerId($nhtb39f008e318efd2bb988d724a161b61c6909677f);
        $nht8dadd8ffd632a3b3b2fa993cfe87743f46099832 = $this->_process->customer($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        if($nht8dadd8ffd632a3b3b2fa993cfe87743f46099832['result'] == 'success'){
            $nht32e0b4164798121e0ed86fc6820775f185e5ea3c = $nht8dadd8ffd632a3b3b2fa993cfe87743f46099832['mage_id'];
            $this->customerSuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c);
        } else {
            $nht8dadd8ffd632a3b3b2fa993cfe87743f46099832['result'] = 'warning';
            $nht19f34ee1e406ea84ca83c835a3301b5d9014a788 = "Customer Id = {$nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595} import failed. Error: " . $nht8dadd8ffd632a3b3b2fa993cfe87743f46099832['msg'];
            $nht8dadd8ffd632a3b3b2fa993cfe87743f46099832['msg'] = $this->consoleWarning($nht19f34ee1e406ea84ca83c835a3301b5d9014a788);
        }
        return $nht8dadd8ffd632a3b3b2fa993cfe87743f46099832;
    }

    /**
     * Process after one customer import successful
     *
     * @param int $nht99f4d75970929dd23fc2d2793107a65bb8b95b68 : Id of customer import to magento
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of function convertCustomer
     * @param array $nhtb39f008e318efd2bb988d724a161b61c6909677f : One row of object function getCustomers
     * @return boolean
     */
    public function afterSaveCustomer($nht99f4d75970929dd23fc2d2793107a65bb8b95b68, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtb39f008e318efd2bb988d724a161b61c6909677f){
        $this->_custom->afterSaveCustomerCustom($this, $nht99f4d75970929dd23fc2d2793107a65bb8b95b68, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtb39f008e318efd2bb988d724a161b61c6909677f);
        return Custom::CUSTOMER_AFTER_SAVE;
    }

    /**
     * Process before import orders
     */
    public function prepareImportOrders(){
        $this->_custom->prepareImportOrdersCustom($this);
    }

    /**
     * Check order has been imported
     *
     * @param array $nhtcce55e4309a753985bdd21919395fdc17daa11e4 : One row of object in function getOrders
     * @return boolean
     */
    public function checkOrderImport($nhtcce55e4309a753985bdd21919395fdc17daa11e4){
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->getOrderId($nhtcce55e4309a753985bdd21919395fdc17daa11e4);
        return $this->getIdDescOrder($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Import order with data convert in function convertOrder
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of function convertOrder
     * @param array $nhtcce55e4309a753985bdd21919395fdc17daa11e4 : One row of object in function getOrders
     * @return boolean
     */
    public function importOrder($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtcce55e4309a753985bdd21919395fdc17daa11e4){
        if(Custom::ORDER_IMPORT){
            return $this->_custom->importOrderCustom($this, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtcce55e4309a753985bdd21919395fdc17daa11e4);
        }
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->getOrderId($nhtcce55e4309a753985bdd21919395fdc17daa11e4);
        $nhta689f6697f924b3e4988f06209d86f49cabd6242 = $this->_process->order($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $this->_notice['config']['add_option']['pre_ord']);
        if($nhta689f6697f924b3e4988f06209d86f49cabd6242['result'] == 'success'){
            $nht32e0b4164798121e0ed86fc6820775f185e5ea3c = $nhta689f6697f924b3e4988f06209d86f49cabd6242['mage_id'];
            $this->orderSuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c);
        } else {
            $nhta689f6697f924b3e4988f06209d86f49cabd6242['result'] = 'warning';
            $nht19f34ee1e406ea84ca83c835a3301b5d9014a788 = "Order Id = {$nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595} import failed. Error: " . $nhta689f6697f924b3e4988f06209d86f49cabd6242['msg'];
            $nhta689f6697f924b3e4988f06209d86f49cabd6242['msg'] = $this->consoleWarning($nht19f34ee1e406ea84ca83c835a3301b5d9014a788);
        }
        return $nhta689f6697f924b3e4988f06209d86f49cabd6242;
    }

    /**
     * Process after one order save successful
     *
     * @param int $nht1d17f5cae78c16f2bac6fbb7fe9f6acece23fa8a : Id of order import to magento
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of function convertOrder
     * @param array $nhtcce55e4309a753985bdd21919395fdc17daa11e4 : One row of object in function getOrders
     * @return boolean
     */
    public function afterSaveOrder($nht1d17f5cae78c16f2bac6fbb7fe9f6acece23fa8a, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtcce55e4309a753985bdd21919395fdc17daa11e4){
        $this->_custom->afterSaveOrderCustom($this, $nht1d17f5cae78c16f2bac6fbb7fe9f6acece23fa8a, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtcce55e4309a753985bdd21919395fdc17daa11e4);
        return Custom::ORDER_AFTER_SAVE;
    }

    /**
     * Process before import reviews
     */
    public function prepareImportReviews(){
        $this->_custom->prepareImportReviewsCustom($this);
        $this->_notice['extend']['rating'] = $this->getRatingOptions();
    }

    /**
     * Check review has been imported
     *
     * @param array $nht61e62b213a1a56f7695845df4fc372a10cb0a73e : One row of object in function getReviews
     * @return boolean
     */
    public function checkReviewImport($nht61e62b213a1a56f7695845df4fc372a10cb0a73e){
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->getReviewId($nht61e62b213a1a56f7695845df4fc372a10cb0a73e);
        return $this->getIdDescReview($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Import review with data convert in function convertReview
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of function convertReview
     * @param array $nht61e62b213a1a56f7695845df4fc372a10cb0a73e : One row of object in function getReviews
     * @return array
     */
    public function importReview($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht61e62b213a1a56f7695845df4fc372a10cb0a73e){
        if(Custom::REVIEW_IMPORT){
            return $this->_custom->importReviewCustom($this, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht61e62b213a1a56f7695845df4fc372a10cb0a73e);
        }
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->getReviewId($nht61e62b213a1a56f7695845df4fc372a10cb0a73e);
        $nhtf5c360d42edb4bef232b7ae013d538fc2f9660f3 = $this->_process->review($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $this->_notice['extend']['rating']);
        if($nhtf5c360d42edb4bef232b7ae013d538fc2f9660f3['result'] == 'success'){
            $nht32e0b4164798121e0ed86fc6820775f185e5ea3c = $nhtf5c360d42edb4bef232b7ae013d538fc2f9660f3['mage_id'];
            $this->reviewSuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c);
        } else {
            $nhtf5c360d42edb4bef232b7ae013d538fc2f9660f3['result'] = 'warning';
            $nht19f34ee1e406ea84ca83c835a3301b5d9014a788 = "Review Id = {$nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595} import failed. Error: " . $nhtf5c360d42edb4bef232b7ae013d538fc2f9660f3['msg'];
            $nhtf5c360d42edb4bef232b7ae013d538fc2f9660f3['msg'] = $this->consoleWarning($nht19f34ee1e406ea84ca83c835a3301b5d9014a788);
        }
        return $nhtf5c360d42edb4bef232b7ae013d538fc2f9660f3;
    }

    /**
     * Process after one review save successful
     *
     * @param int $nhtdd61c42a5384268fc32a56a378a1d6d493da29d6 : Id of review import to magento
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of function convertReview
     * @param array $nht61e62b213a1a56f7695845df4fc372a10cb0a73e : One row of object in function getReviews
     * @return boolean
     */
    public function afterSaveReview($nhtdd61c42a5384268fc32a56a378a1d6d493da29d6, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht61e62b213a1a56f7695845df4fc372a10cb0a73e){
        $this->_custom->afterSaveReviewCustom($this, $nhtdd61c42a5384268fc32a56a378a1d6d493da29d6, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht61e62b213a1a56f7695845df4fc372a10cb0a73e);
        return Custom::REVIEW_AFTER_SAVE;
    }

    /**
     * Process clear cache and reindex data after finish migration
     *
     * @return array
     */
    public function finishImport(){
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array(
            'result' => 'success',
            'msg' => ''
        );
        $nht168cbb2ea52b9e34d271accecfa7d7951e948a99 = $this->_process->clearCache();
        $nhte540cdd1328b2b21e29a95405c301b9313b7c346 = $this->_process->reIndexes();
        $nhtafffdd08d81dd168981d9a0dcceb2fb24c2ab56a = $this->_objectManager->get('Magento\Store\Model\Store')->getBaseMediaDir() . self::FOLDER_SUFFIX . $this->_notice['config']['folder'];
        $this->deleteDir($nhtafffdd08d81dd168981d9a0dcceb2fb24c2ab56a);
        $this->clearPreSection();
        if($nht168cbb2ea52b9e34d271accecfa7d7951e948a99['result'] != 'success' || $nhte540cdd1328b2b21e29a95405c301b9313b7c346['result'] != 'success'){
            if($nht168cbb2ea52b9e34d271accecfa7d7951e948a99['msg']){
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] .= $this->consoleWarning($nht168cbb2ea52b9e34d271accecfa7d7951e948a99['msg']);
            }
            if($nhte540cdd1328b2b21e29a95405c301b9313b7c346['msg']){
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] .= $this->consoleWarning($nhte540cdd1328b2b21e29a95405c301b9313b7c346['msg']);
            }
        } else {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = $this->consoleSuccess("Finished Clear cache & Reindex data");
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * TODO : Work with database
     */

    /**
     * Convert array to string insert use in raw query
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd
     * @param array $nht90a6864ee01cee93dd6481f0a0f425cc864d6e49
     * @return array
     */
    public function arrayToInsertQueryObject($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht90a6864ee01cee93dd6481f0a0f425cc864d6e49 = array()){
        if(!$nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd){
            return false;
        }
        $nht7316c8b2e74870d9d7e9d30bbc28ecf4cdf945ee = array();
        $nht5944ae25418ceabcf285dca1d721b77888dac89b = array_keys($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        $nht260345817aff62fdd650dd76efbd3230d72d362d = array();
        if(!$nht90a6864ee01cee93dd6481f0a0f425cc864d6e49){
            $nht7316c8b2e74870d9d7e9d30bbc28ecf4cdf945ee = $nht5944ae25418ceabcf285dca1d721b77888dac89b;
            $nht260345817aff62fdd650dd76efbd3230d72d362d = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd;
        } else {
            foreach($nht5944ae25418ceabcf285dca1d721b77888dac89b as $nhta62f2225bf70bfaccbc7f1ef2a397836717377de){
                if(in_array($nhta62f2225bf70bfaccbc7f1ef2a397836717377de, $nht90a6864ee01cee93dd6481f0a0f425cc864d6e49)){
                    $nht7316c8b2e74870d9d7e9d30bbc28ecf4cdf945ee[] = $nhta62f2225bf70bfaccbc7f1ef2a397836717377de;
                    $nht260345817aff62fdd650dd76efbd3230d72d362d[$nhta62f2225bf70bfaccbc7f1ef2a397836717377de] = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd[$nhta62f2225bf70bfaccbc7f1ef2a397836717377de];
                }
            }
        }
        if(!$nht7316c8b2e74870d9d7e9d30bbc28ecf4cdf945ee){
            return false;
        }
        $nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7 = '(' . implode(', ', $nht7316c8b2e74870d9d7e9d30bbc28ecf4cdf945ee) . ')';
        $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = '(:' . implode(', :', $nht7316c8b2e74870d9d7e9d30bbc28ecf4cdf945ee) . ')';
        return array(
            'row' => $nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7,
            'value' => $nhtf32b67c7e26342af42efabc674d441dca0a281c5,
            'data' => $nht260345817aff62fdd650dd76efbd3230d72d362d
        );
    }

    /**
     * Convert array to string update use in raw query
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd
     * @param array $nht90a6864ee01cee93dd6481f0a0f425cc864d6e49
     * @return array
     */
    public function arrayToUpdateQueryObject($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht90a6864ee01cee93dd6481f0a0f425cc864d6e49 = array()){
        if(!$nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd){
            return false;
        }
        $nht7316c8b2e74870d9d7e9d30bbc28ecf4cdf945ee = array();
        $nht5944ae25418ceabcf285dca1d721b77888dac89b = array_keys($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        if(!$nht90a6864ee01cee93dd6481f0a0f425cc864d6e49){
            $nht90a6864ee01cee93dd6481f0a0f425cc864d6e49 = $nht5944ae25418ceabcf285dca1d721b77888dac89b;
        }
        foreach($nht5944ae25418ceabcf285dca1d721b77888dac89b as $nhta62f2225bf70bfaccbc7f1ef2a397836717377de){
            if(in_array($nhta62f2225bf70bfaccbc7f1ef2a397836717377de, $nht90a6864ee01cee93dd6481f0a0f425cc864d6e49)){
                $nht65c10dc3549fe07424148a8a4790a3341ecbc253 = $nhta62f2225bf70bfaccbc7f1ef2a397836717377de . '= :' . $nhta62f2225bf70bfaccbc7f1ef2a397836717377de;
                $nht7316c8b2e74870d9d7e9d30bbc28ecf4cdf945ee[] = $nht65c10dc3549fe07424148a8a4790a3341ecbc253;
            }
        }
        if(!$nht7316c8b2e74870d9d7e9d30bbc28ecf4cdf945ee){
            return false;
        }
        $nht572acd09a7bf3c52578d417ba44fd6e983404594 = implode(', ', $nht7316c8b2e74870d9d7e9d30bbc28ecf4cdf945ee);
        return $nht572acd09a7bf3c52578d417ba44fd6e983404594;
    }

    /**
     * Convert array to where condition in mysql query
     */
    public function arrayToWhereCondition($nht19edc1210777ba4d45049c29280d9cc5e1064c25){
        if(empty($nht19edc1210777ba4d45049c29280d9cc5e1064c25)){
            return '1 = 1';
        }
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array();
        foreach($nht19edc1210777ba4d45049c29280d9cc5e1064c25 as $nhta62f2225bf70bfaccbc7f1ef2a397836717377de => $nhtf32b67c7e26342af42efabc674d441dca0a281c5){
            $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd[] = "`{$nhta62f2225bf70bfaccbc7f1ef2a397836717377de}` = '{$nhtf32b67c7e26342af42efabc674d441dca0a281c5}'";
        }
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = implode(" AND ", $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Convert array to create table query
     */
    public function arrayToCreateSql($nht19edc1210777ba4d45049c29280d9cc5e1064c25){
        if(!$nht19edc1210777ba4d45049c29280d9cc5e1064c25){
            return array(
                'result' => 'error',
                'msg' => "Data not exists."
            );
        }
        $nhtc3ee137d4f22eb06ed1351d644f3674592c90836 = $nht19edc1210777ba4d45049c29280d9cc5e1064c25['table'];
        $nht79067b3319cda217f4221594563cfae1022c38f5 = $nht19edc1210777ba4d45049c29280d9cc5e1064c25['rows'];
        if(!$nhtc3ee137d4f22eb06ed1351d644f3674592c90836 || !$nht79067b3319cda217f4221594563cfae1022c38f5){
            return array(
                'result' => 'error',
                'msg' => 'Table data not exists'
            );
        }
        $nht6c30d261539235005dac78552ab077de42661332 = array();
        foreach($nht79067b3319cda217f4221594563cfae1022c38f5 as $nhtf773eb1967bc598b09ec198a7f80b2e8e0de5411 => $nht1cf93a0a3b14f992ac57350339c082bd66a367e5){
            $nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7 = "`{$nhtf773eb1967bc598b09ec198a7f80b2e8e0de5411}` {$nht1cf93a0a3b14f992ac57350339c082bd66a367e5}";
            $nht6c30d261539235005dac78552ab077de42661332[] = $nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7;
        }
        $nht2cb0b78846e0a65d35a4cd02d3cb9710abda4912 = $this->getTableName($nhtc3ee137d4f22eb06ed1351d644f3674592c90836);
        $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72 = "CREATE TABLE IF NOT EXISTS {$nht2cb0b78846e0a65d35a4cd02d3cb9710abda4912} (";
        $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72 .= implode(',', $nht6c30d261539235005dac78552ab077de42661332);
        $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72 .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
        return array(
            'result' => 'success',
            'query' => $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72
        );
    }

    /**
     * Get table in magento database with table prefix
     */
    public function getTableName($nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c){
        return $this->_resource->getTableName($nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c);
    }

    /**
     * Run write query with magento database
     */
    public function writeQuery($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72, $nht6bdd4db977b0f96aaf3bff2e4300153c648ca382 = array()){
        try{
            $this->_db->query($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72, $nht6bdd4db977b0f96aaf3bff2e4300153c648ca382);
            return true;
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            if(Custom::DEV_MODE){
                $nht6f9b9af3cd6e8b8a73c2cdced37fe9f59226e27d = "LitExtension_CartImport_Model_Cart::writeQuery() error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
//                $this->_logger->addError($nht6f9b9af3cd6e8b8a73c2cdced37fe9f59226e27d);
            }
            return false;
        }
    }

    /**
     * Run read query with magento database
     */
    public function readQuery($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72, $nht6bdd4db977b0f96aaf3bff2e4300153c648ca382= array()){
        try{
            $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->_db->fetchAll($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72, $nht6bdd4db977b0f96aaf3bff2e4300153c648ca382);
            return array(
                'result' => 'success',
                'data' => $nht37a5301a88da334dc5afc5b63979daa0f3f45e68
            );
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            if(Custom::DEV_MODE){
                $nht6f9b9af3cd6e8b8a73c2cdced37fe9f59226e27d = "LitExtension_CartImport_Model_Cart::readQuery() error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
//                $this->_logger->addError($nht6f9b9af3cd6e8b8a73c2cdced37fe9f59226e27d);
            }
            return array(
                'result' => 'error',
                'msg' => $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage()
            );
        }
    }

    /**
     * Get data from table by where condition
     *
     * @param string $nhtc3ee137d4f22eb06ed1351d644f3674592c90836
     * @param array $nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd
     * @return array
     */
    public function selectTable($nhtc3ee137d4f22eb06ed1351d644f3674592c90836, $nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd){
        $nht4debe7a36ca91b3dad55f5650a56fd09734f5276 = $this->arrayToWhereCondition($nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd);
        $nht2cb0b78846e0a65d35a4cd02d3cb9710abda4912 = $this->getTableName($nhtc3ee137d4f22eb06ed1351d644f3674592c90836);
        $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72 = "SELECT * FROM {$nht2cb0b78846e0a65d35a4cd02d3cb9710abda4912} WHERE {$nht4debe7a36ca91b3dad55f5650a56fd09734f5276}";
        try{
            $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->_db->fetchAll($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72);
            return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            if(Custom::DEV_MODE){
                $nht6f9b9af3cd6e8b8a73c2cdced37fe9f59226e27d = "LitExtension_CartImport_Model_Cart::selectTable() error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
//                $this->_logger->addError($nht6f9b9af3cd6e8b8a73c2cdced37fe9f59226e27d);
            }
            return false;
        }
    }

    /**
     * Insert data with type array to table
     *
     * @param string $nhtc3ee137d4f22eb06ed1351d644f3674592c90836
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd
     * @param array $nht90a6864ee01cee93dd6481f0a0f425cc864d6e49
     * @return boolean
     */
    public function insertTable($nhtc3ee137d4f22eb06ed1351d644f3674592c90836, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht90a6864ee01cee93dd6481f0a0f425cc864d6e49 = array()){
        $nht9b5c0b859faba061dd60fd8070fce74fcee29d0b = $this->arrayToInsertQueryObject($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht90a6864ee01cee93dd6481f0a0f425cc864d6e49);
        if(!$nht9b5c0b859faba061dd60fd8070fce74fcee29d0b){
            return false;
        }
        $nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7 = $nht9b5c0b859faba061dd60fd8070fce74fcee29d0b['row'];
        $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = $nht9b5c0b859faba061dd60fd8070fce74fcee29d0b['value'];
        $nht260345817aff62fdd650dd76efbd3230d72d362d = $nht9b5c0b859faba061dd60fd8070fce74fcee29d0b['data'];
        $nht2cb0b78846e0a65d35a4cd02d3cb9710abda4912 = $this->getTableName($nhtc3ee137d4f22eb06ed1351d644f3674592c90836);
        $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72 = "INSERT INTO {$nht2cb0b78846e0a65d35a4cd02d3cb9710abda4912} {$nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7} VALUES {$nhtf32b67c7e26342af42efabc674d441dca0a281c5}";
        try{
            $this->_db->query($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72, $nht260345817aff62fdd650dd76efbd3230d72d362d);
            return true;
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            if(Custom::DEV_MODE){
                $nht6f9b9af3cd6e8b8a73c2cdced37fe9f59226e27d = "LitExtension_CartImport_Model_Cart::insertTable() error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
//                $this->_logger->addError($nht6f9b9af3cd6e8b8a73c2cdced37fe9f59226e27d);
            }
            return false;
        }
    }

    /**
     * Update data with type array to table by where condition
     *
     * @param string $nhtc3ee137d4f22eb06ed1351d644f3674592c90836
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd
     * @param array $nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd
     * @param array $nht90a6864ee01cee93dd6481f0a0f425cc864d6e49
     * @return boolean
     */
    public function updateTable($nhtc3ee137d4f22eb06ed1351d644f3674592c90836, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd, $nht90a6864ee01cee93dd6481f0a0f425cc864d6e49 = array()){
        $nht572acd09a7bf3c52578d417ba44fd6e983404594 = $this->arrayToUpdateQueryObject($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht90a6864ee01cee93dd6481f0a0f425cc864d6e49);
        if(!$nht572acd09a7bf3c52578d417ba44fd6e983404594){
            return false;
        }
        $nht4debe7a36ca91b3dad55f5650a56fd09734f5276 = $this->arrayToWhereCondition($nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd);
        $nht2cb0b78846e0a65d35a4cd02d3cb9710abda4912 = $this->getTableName($nhtc3ee137d4f22eb06ed1351d644f3674592c90836);
        $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72 = "UPDATE {$nht2cb0b78846e0a65d35a4cd02d3cb9710abda4912} SET {$nht572acd09a7bf3c52578d417ba44fd6e983404594} WHERE {$nht4debe7a36ca91b3dad55f5650a56fd09734f5276}";
        try{
            $this->_db->query($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
            return true;
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            if(Custom::DEV_MODE){
                $nht6f9b9af3cd6e8b8a73c2cdced37fe9f59226e27d = "LitExtension_CartImport_Model_Cart::updateTable() error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
//                $this->_logger->addError($nht6f9b9af3cd6e8b8a73c2cdced37fe9f59226e27d);
            }
            return false;
        }
    }

    /**
     * Delete data from table by where condition
     *
     * @param string $nhtc3ee137d4f22eb06ed1351d644f3674592c90836
     * @param array $nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd
     * @return boolean
     */
    public function deleteTable($nhtc3ee137d4f22eb06ed1351d644f3674592c90836, $nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd){
        $nht4debe7a36ca91b3dad55f5650a56fd09734f5276 = $this->arrayToWhereCondition($nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd);
        $nht2cb0b78846e0a65d35a4cd02d3cb9710abda4912 = $this->_resource->getTableName($nhtc3ee137d4f22eb06ed1351d644f3674592c90836);
        $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72 = "DELETE FROM {$nht2cb0b78846e0a65d35a4cd02d3cb9710abda4912} WHERE {$nht4debe7a36ca91b3dad55f5650a56fd09734f5276}";
        try{
            $this->_db->query($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72);
            return true;
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            if(Custom::DEV_MODE){
                $nht6f9b9af3cd6e8b8a73c2cdced37fe9f59226e27d = "LitExtension_CartImport_Model_Cart::deleteTable() error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
//                $this->_logger->addError($nht6f9b9af3cd6e8b8a73c2cdced37fe9f59226e27d);
            }
            return false;
        }
    }

    public function dropTable($nhtc3ee137d4f22eb06ed1351d644f3674592c90836){
        $nht2cb0b78846e0a65d35a4cd02d3cb9710abda4912 = $this->getTableName($nhtc3ee137d4f22eb06ed1351d644f3674592c90836);
        $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72 = "DROP TABLE IF EXISTS `" . $nht2cb0b78846e0a65d35a4cd02d3cb9710abda4912 . "`";
        return $this->writeQuery($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72);
    }

    /**
     * Get one row of result select
     *
     * @param string $nhtc3ee137d4f22eb06ed1351d644f3674592c90836
     * @param array $nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd
     * @return array
     */
    public function selectTableRow($nhtc3ee137d4f22eb06ed1351d644f3674592c90836, $nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->selectTable($nhtc3ee137d4f22eb06ed1351d644f3674592c90836, $nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd);
        if(!$nht37a5301a88da334dc5afc5b63979daa0f3f45e68){
            return false;
        }
        return (isset($nht37a5301a88da334dc5afc5b63979daa0f3f45e68[0])) ? $nht37a5301a88da334dc5afc5b63979daa0f3f45e68[0] : false;
    }

    /**
     * Get id_desc in import table by type and id_src
     */
    public function getLeCaIpImportIdDesc($nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->selectTableRow(self::TABLE_IMPORT, array(
            'domain' => $this->_cart_url,
            'type' => $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9,
            'id_src' => $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595
        ));
        if(!$nht37a5301a88da334dc5afc5b63979daa0f3f45e68){
            return false;
        }
        return (isset($nht37a5301a88da334dc5afc5b63979daa0f3f45e68['id_desc'])) ? $nht37a5301a88da334dc5afc5b63979daa0f3f45e68['id_desc'] : false;
    }

    /**
     * Get magento tax id import by id src
     */
    public function getIdDescTax($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595){
        return $this->getLeCaIpImportIdDesc(self::TYPE_TAX, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Get magento tax customer id import by id src
     */
    public function getIdDescTaxCustomer($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595){
        return $this->getLeCaIpImportIdDesc(self::TYPE_TAX_CUSTOMER, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Get magento tax product id import by id src
     */
    public function getIdDescTaxProduct($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595){
        return $this->getLeCaIpImportIdDesc(self::TYPE_TAX_PRODUCT, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Get magento tax rate id import by id src
     */
    public function getIdDescTaxRate($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595){
        return $this->getLeCaIpImportIdDesc(self::TYPE_TAX_RATE, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Get magento attribute manufacturer id import by id src
     */
    public function getIdDescManAttr($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595){
        return $this->getLeCaIpImportIdDesc(self::TYPE_MAN_ATTR, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Get magento manufacturer option id import by id src
     */
    public function getIdDescManufacturer($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595){
        return $this->getLeCaIpImportIdDesc(self::TYPE_MANUFACTURER, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Get magento category id import by id src
     */
    public function getIdDescCategory($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595){
        return $this->getLeCaIpImportIdDesc(self::TYPE_CATEGORY, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Get magento product id import by id src
     */
    public function getIdDescProduct($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595){
        return $this->getLeCaIpImportIdDesc(self::TYPE_PRODUCT, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Get magento attribute id import by id src
     */
    public function getIdDescAttribute($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595){
        return $this->getLeCaIpImportIdDesc(self::TYPE_ATTR, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Get magento attribute option id import by id src
     */
    public function getIdDescAttrOption($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595){
        return $this->getLeCaIpImportIdDesc(self::TYPE_ATTR_OPTION, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Get magento customer id import by id src
     */
    public function getIdDescCustomer($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595){
        return $this->getLeCaIpImportIdDesc(self::TYPE_CUSTOMER, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Get magento order id import by id src
     */
    public function getIdDescOrder($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595){
        return $this->getLeCaIpImportIdDesc(self::TYPE_ORDER, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Get magento review id import by id src
     */
    public function getIdDescReview($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595){
        return $this->getLeCaIpImportIdDesc(self::TYPE_REVIEW, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595);
    }

    /**
     * Save info to import table
     */
    public function insertLeCaIpImport($nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, $nht48a3661d846478fa991a825ebd10b78671444b5b, $nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        return $this->insertTable(self::TABLE_IMPORT, array(
            'domain' => $this->_cart_url,
            'type' => $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9,
            'id_src' => $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595,
            'id_desc' => $nht32e0b4164798121e0ed86fc6820775f185e5ea3c,
            'status' => $nht48a3661d846478fa991a825ebd10b78671444b5b,
            'value' => $nhtf32b67c7e26342af42efabc674d441dca0a281c5
        ));
    }

    /**
     * Save info of tax import successful to table lecaip_import
     */
    public function taxSuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_TAX, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, 1, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of tax customer import successful to table lecaip_import
     */
    public function taxCustomerSuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_TAX_CUSTOMER, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, 1, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of tax product import successful to table lecaip_import
     */
    public function taxProductSuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_TAX_PRODUCT, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, 1, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of tax rate import successful to table lecaip_import
     */
    public function taxRateSuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_TAX_RATE, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, 1, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of manufacturer attribute import successful to table lecaip_import
     */
    public function manAttrSuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_MAN_ATTR, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, 1, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of manufacturer option import successful to table lecaip_import
     */
    public function manufacturerSuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_MANUFACTURER, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, 1, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of category import successful to table lecaip_import
     */
    public function categorySuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_CATEGORY, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, 1, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of product import successful to table lecaip_import
     */
    public function productSuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_PRODUCT, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, 1, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of attribute import successful to table lecaip_import
     */
    public function attributeSuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_ATTR, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, 1, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of attribute option import successful to table lecaip_import
     */
    public function attrOptionSuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_ATTR_OPTION, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, 1, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of customer import successful to table lecaip_import
     */
    public function customerSuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_CUSTOMER, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, 1, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of order import successful to table lecaip_import
     */
    public function orderSuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_ORDER, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, 1, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of review import successful to table lecaip_import
     */
    public function reviewSuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_REVIEW, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, 1, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of tax import error to table lecaip_import
     */
    public function taxError($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_TAX, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, false, 0, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of tax customer import error to table lecaip_import
     */
    public function taxCustomerError($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_TAX_CUSTOMER, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, false, 0, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of tax product import error to table lecaip_import
     */
    public function taxProductError($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_TAX_PRODUCT, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, false, 0, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of tax rate import error to table lecaip_import
     */
    public function taxRateError($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_TAX_RATE, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, false, 0, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of manufacturer attribute import error to table lecaip_import
     */
    public function manAttrError($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_MAN_ATTR, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, false, 0, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of manufacturer import error to table lecaip_import
     */
    public function manufacturerError($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_MANUFACTURER, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, false, 0, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of category import error to table lecaip_import
     */
    public function categoryError($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_CATEGORY, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, false, 0, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of product import error to table lecaip_import
     */
    public function productError($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_PRODUCT, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, false, 0, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of attribute import error to table lecaip_import
     */
    public function attributeError($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_ATTR, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, false, 0, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of attribute option import error to table lecaip_import
     */
    public function attrOptionError($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_ATTR_OPTION, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, false, 0, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of customer import error to table lecaip_import
     */
    public function customerError($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_CUSTOMER, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, false, 0, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of order import error to table lecaip_import
     */
    public function orderError($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_ORDER, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, false, 0, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * Save info of review import error to table lecaip_import
     */
    public function reviewError($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = false){
        return $this->insertLeCaIpImport(self::TYPE_REVIEW, $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, false, 0, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
    }

    /**
     * TODO : Work with Magento
     */

    /**
     * Get website id by store id
     */
    public function getWebsiteIdByStoreId($nhtf3172007d4de5ae8e7692759d79f67f5558242ed){
        $nht3a21295d813c26eb287fc6b59454fb37858d63e6 = $this->_objectManager->create('Magento\Store\Model\Store')->load($nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
        $nht979e53e64186ccd315cf09b3b141f8f3210e4477 = $nht3a21295d813c26eb287fc6b59454fb37858d63e6->getWebsiteId();
        return $nht979e53e64186ccd315cf09b3b141f8f3210e4477;
    }

    /**
     * Get list website id by list store id
     */
    public function getWebsiteIdsByStoreIds($nhtf33d6b5ead5b96fb5cd937f803734e0024ddedf8){
        if($nhtf33d6b5ead5b96fb5cd937f803734e0024ddedf8 && !empty($nhtf33d6b5ead5b96fb5cd937f803734e0024ddedf8)){
            $nht979e53e64186ccd315cf09b3b141f8f3210e4477 = array();
            foreach($nhtf33d6b5ead5b96fb5cd937f803734e0024ddedf8 as $nhtf3172007d4de5ae8e7692759d79f67f5558242ed){
                $nht3a21295d813c26eb287fc6b59454fb37858d63e6 = $this->_objectManager->create('Magento\Store\Model\Store')->load($nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
                $nht979e53e64186ccd315cf09b3b141f8f3210e4477[] = $nht3a21295d813c26eb287fc6b59454fb37858d63e6->getWebsiteId();
            }
            return $this->_filterArrayValueDuplicate($nht979e53e64186ccd315cf09b3b141f8f3210e4477);
        }
        return false;
    }

    /**
     * Get currency config of store and base website
     */
    public function getStoreCurrencyCode($nhtf3172007d4de5ae8e7692759d79f67f5558242ed){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = array();
        $nht3a21295d813c26eb287fc6b59454fb37858d63e6 = $this->_objectManager->create('Magento\Store\Model\Store')->load($nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68['base'] = $nht3a21295d813c26eb287fc6b59454fb37858d63e6->getBaseCurrencyCode();
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68['current'] = $nht3a21295d813c26eb287fc6b59454fb37858d63e6->getCurrentCurrencyCode();
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Pass customer pass to database not encrypt
     */
    public function importCustomerRawPass($nhta7a13f4cacb744524e44dfdad329d540144d209d, $nht9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684){
        return $this->updateTable('customer_entity', array(
            'password_hash' => $nht9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684
        ), array(
            'entity_id' => $nhta7a13f4cacb744524e44dfdad329d540144d209d
        ));
    }

    /**
     * Set attribute select to product
     */
    public function setProAttrSelect($nhtb13fb7c5c9e9dff62b60e0de72929155d3b6167b, $nhtbebc9158e480b949565b4dc7a82d05cfd99935d5, $nhte6090c1c6ad8962eea97abdbe63ac4bb46293b33){
        $this->insertTable('catalog_product_entity_int', array(
            'attribute_id' => $nhtb13fb7c5c9e9dff62b60e0de72929155d3b6167b,
            'store_id' => 0,
            'entity_id' => $nhtbebc9158e480b949565b4dc7a82d05cfd99935d5,
            'value' => $nhte6090c1c6ad8962eea97abdbe63ac4bb46293b33
        ));
    }

    /**
     * Set attribute date to product
     */
    public function setProAttrDate($nhtb13fb7c5c9e9dff62b60e0de72929155d3b6167b, $nhtbebc9158e480b949565b4dc7a82d05cfd99935d5, $nhte927d0677c77241b707442314346326278051dd6){
        $this->insertTable('catalog_product_entity_datetime', array(
            'attribute_id' => $nhtb13fb7c5c9e9dff62b60e0de72929155d3b6167b,
            'store_id' => 0,
            'entity_id' => $nhtbebc9158e480b949565b4dc7a82d05cfd99935d5,
            'value' => $nhte927d0677c77241b707442314346326278051dd6
        ));
    }

    /**
     * Set attribute text to product
     */
    public function setProAttrText($nhtb13fb7c5c9e9dff62b60e0de72929155d3b6167b, $nhtbebc9158e480b949565b4dc7a82d05cfd99935d5, $nht372ea08cab33e71c02c651dbc83a474d32c676ea){
        $this->insertTable('catalog_product_entity_text', array(
            'attribute_id' => $nhtb13fb7c5c9e9dff62b60e0de72929155d3b6167b,
            'store_id' => 0,
            'entity_id' => $nhtbebc9158e480b949565b4dc7a82d05cfd99935d5,
            'value' => $nht372ea08cab33e71c02c651dbc83a474d32c676ea
        ));
    }

    /**
     * Set attribute varchar to product
     */
    public function setProAttrVarchar($nhtb13fb7c5c9e9dff62b60e0de72929155d3b6167b, $nhtbebc9158e480b949565b4dc7a82d05cfd99935d5, $nhtdf0e5006b34734de067cf6faa2a4039d263830c4){
        $this->insertTable('catalog_product_entity_varchar', array(
            'attribute_id' => $nhtb13fb7c5c9e9dff62b60e0de72929155d3b6167b,
            'store_id' => 0,
            'entity_id' => $nhtbebc9158e480b949565b4dc7a82d05cfd99935d5,
            'value' => $nhtdf0e5006b34734de067cf6faa2a4039d263830c4
        ));
    }

    /**
     * Set option to product
     */
    public function setProductHasOption($nhtbebc9158e480b949565b4dc7a82d05cfd99935d5, $nht5d08abdf5152f9499721f264a937f53de8cd684e = true){
        $this->updateTable('catalog_product_entity', array(
            'has_options' => true,
            'required_options' => (bool)$nht5d08abdf5152f9499721f264a937f53de8cd684e
        ), array(
            'entity_id' => $nhtbebc9158e480b949565b4dc7a82d05cfd99935d5
        ));
    }

    /**
     * Import custom option to product
     */
    public function importProductOptionOld($nhtbebc9158e480b949565b4dc7a82d05cfd99935d5, $nht513f8de9259fe7658fe14d1352c54ccf070e911f){
        try{
            $nht38a007151abe87cc01a5b6e9cc418e85286e2087 = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($nhtbebc9158e480b949565b4dc7a82d05cfd99935d5);

            if(!$nht38a007151abe87cc01a5b6e9cc418e85286e2087->getOptionsReadonly()) {
                foreach($nht513f8de9259fe7658fe14d1352c54ccf070e911f as $nht14eb14ece52df99c284b819d9f8092e50aa1613e){
                    $nht102210fe594ee9b33d82058545b1ed14f4c8206e = $this->_objectManager->create('Magento\Catalog\Model\Product\Option');
                    $nht102210fe594ee9b33d82058545b1ed14f4c8206e->setProduct($nht38a007151abe87cc01a5b6e9cc418e85286e2087);
                    $nht102210fe594ee9b33d82058545b1ed14f4c8206e->addOption($nht14eb14ece52df99c284b819d9f8092e50aa1613e);
                    $nht102210fe594ee9b33d82058545b1ed14f4c8206e->saveOptions();
                }
                $this->setProductHasOption($nhtbebc9158e480b949565b4dc7a82d05cfd99935d5);
            }

        } catch(\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){}
    }

    public function importProductOption($nhtbebc9158e480b949565b4dc7a82d05cfd99935d5, $nht513f8de9259fe7658fe14d1352c54ccf070e911f){
        try {
            $nht38a007151abe87cc01a5b6e9cc418e85286e2087 = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($nhtbebc9158e480b949565b4dc7a82d05cfd99935d5);
            if (!$nht38a007151abe87cc01a5b6e9cc418e85286e2087->getOptionsReadonly()) {
                $nht57f70b7509df8a9d299c8a0a1087403f4bd112d4 = array();
                $nht5d08abdf5152f9499721f264a937f53de8cd684e = false;
                foreach ($nht513f8de9259fe7658fe14d1352c54ccf070e911f as $nht14eb14ece52df99c284b819d9f8092e50aa1613e) {
                    $nht102210fe594ee9b33d82058545b1ed14f4c8206e = $this->_objectManager->create('Magento\Catalog\Model\Product\Option');
                    $nht102210fe594ee9b33d82058545b1ed14f4c8206e->setData($nht14eb14ece52df99c284b819d9f8092e50aa1613e)
                        ->setProductSku($nht38a007151abe87cc01a5b6e9cc418e85286e2087->getSku());
                    $nht57f70b7509df8a9d299c8a0a1087403f4bd112d4[] = $nht102210fe594ee9b33d82058545b1ed14f4c8206e;
                    if ($nht14eb14ece52df99c284b819d9f8092e50aa1613e['is_require']) {
                        $nht5d08abdf5152f9499721f264a937f53de8cd684e = true;
                    }
                }
                $nht38a007151abe87cc01a5b6e9cc418e85286e2087->setOptions($nht57f70b7509df8a9d299c8a0a1087403f4bd112d4);
                $nht38a007151abe87cc01a5b6e9cc418e85286e2087->save();
                $this->setProductHasOption($nhtbebc9158e480b949565b4dc7a82d05cfd99935d5, $nht5d08abdf5152f9499721f264a937f53de8cd684e);
            }
        } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {

        }
    }

    /**
     * Set grouped product links
     *
     * @param \Magento\Catalog\Model\Product $nht38a007151abe87cc01a5b6e9cc418e85286e2087
     * @param array $nht379e75c850e1334ef7bece52694c2f26cebec78f
     * @return \Magento\Catalog\Model\Product
     */
    public function setProductGroupLinks(\Magento\Catalog\Model\Product $nht38a007151abe87cc01a5b6e9cc418e85286e2087, $nht379e75c850e1334ef7bece52694c2f26cebec78f) {
        if ($nht38a007151abe87cc01a5b6e9cc418e85286e2087->getTypeId() === 'grouped' && !$nht38a007151abe87cc01a5b6e9cc418e85286e2087->getGroupedReadonly()) {
            //$nht379e75c850e1334ef7bece52694c2f26cebec78f = $nht38a007151abe87cc01a5b6e9cc418e85286e2087->getGroupedLinkData();
            $nhte4ccfcee19bdb7042fef6107bf3b50fc214e60bd = [];
            $nhtbd12614c976bfd545cb9d15022c5f27f892284a1 = $nht38a007151abe87cc01a5b6e9cc418e85286e2087->getProductLinks();
            foreach ($nht379e75c850e1334ef7bece52694c2f26cebec78f as $nht705b78b779459e12d9a125f50bdcd9892bb94743) {
                if (!isset($nht705b78b779459e12d9a125f50bdcd9892bb94743['id'])) {
                    continue;
                }
                $nht6d8e9cc8dceb68a018727fdec42e34a51d18ace2 = $nht705b78b779459e12d9a125f50bdcd9892bb94743['id'];
                if (!isset($nht705b78b779459e12d9a125f50bdcd9892bb94743['qty'])) {
                    $nht705b78b779459e12d9a125f50bdcd9892bb94743['qty'] = 0;
                }
                $nht63b1d5210643b826ae2e66b8207c2f020f7c3e14 = $this->_objectManager->create('Magento\Catalog\Model\ProductLink\Link');
                $nht2447346d854d44c8f5efe4de2c289ab12e4e4b46 = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($nht6d8e9cc8dceb68a018727fdec42e34a51d18ace2);
                $nht63b1d5210643b826ae2e66b8207c2f020f7c3e14->setSku($nht38a007151abe87cc01a5b6e9cc418e85286e2087->getSku())
                    ->setLinkType('associated')
                    ->setLinkedProductSku($nht2447346d854d44c8f5efe4de2c289ab12e4e4b46->getSku())
                    ->setLinkedProductType($nht2447346d854d44c8f5efe4de2c289ab12e4e4b46->getTypeId())
                    ->setPosition($nht705b78b779459e12d9a125f50bdcd9892bb94743['position'])
                    ->getExtensionAttributes()
                    ->setQty($nht705b78b779459e12d9a125f50bdcd9892bb94743['qty']);
                $nhte4ccfcee19bdb7042fef6107bf3b50fc214e60bd[] = $nht63b1d5210643b826ae2e66b8207c2f020f7c3e14;
            }
            //$nhtbd12614c976bfd545cb9d15022c5f27f892284a1 = $this->removeExistingLinks($nhtbd12614c976bfd545cb9d15022c5f27f892284a1, $nhte4ccfcee19bdb7042fef6107bf3b50fc214e60bd);
            $nht38a007151abe87cc01a5b6e9cc418e85286e2087->setProductLinks(array_merge($nhtbd12614c976bfd545cb9d15022c5f27f892284a1, $nhte4ccfcee19bdb7042fef6107bf3b50fc214e60bd));
        }
        return $nht38a007151abe87cc01a5b6e9cc418e85286e2087;
    }

    public function setProductGroupQuick(\Magento\Catalog\Model\Product $nht38a007151abe87cc01a5b6e9cc418e85286e2087) {
        $this->_objectManager->create('Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks')->initializeLinks($nht38a007151abe87cc01a5b6e9cc418e85286e2087, array());
        return $nht38a007151abe87cc01a5b6e9cc418e85286e2087;
    }

    private function removeExistingLinks($nhtbd12614c976bfd545cb9d15022c5f27f892284a1, $nhte4ccfcee19bdb7042fef6107bf3b50fc214e60bd)
    {
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = [];
        foreach ($nhtbd12614c976bfd545cb9d15022c5f27f892284a1 as $nhta62f2225bf70bfaccbc7f1ef2a397836717377de => $nht4f0aa52d656a3d75867f784b7e9c5d23bf1321c0) {
            $nht37a5301a88da334dc5afc5b63979daa0f3f45e68[$nhta62f2225bf70bfaccbc7f1ef2a397836717377de] = $nht4f0aa52d656a3d75867f784b7e9c5d23bf1321c0;
            if ($nht4f0aa52d656a3d75867f784b7e9c5d23bf1321c0->getLinkType() == 'associated') {
                $nht4d68c8f13459c0edb40504de5003ec2a6b74e613 = false;
                foreach ($nhte4ccfcee19bdb7042fef6107bf3b50fc214e60bd as $nhtb5a9f226bf5b1bf570cbc3b5dc171d72f66a816a) {
                    if ($nht4f0aa52d656a3d75867f784b7e9c5d23bf1321c0->getLinkedProductSku() == $nhtb5a9f226bf5b1bf570cbc3b5dc171d72f66a816a->getLinkedProductSku()) {
                        $nht4d68c8f13459c0edb40504de5003ec2a6b74e613 = true;
                    }
                }
                if ($nht4d68c8f13459c0edb40504de5003ec2a6b74e613) {
                    unset($nht37a5301a88da334dc5afc5b63979daa0f3f45e68[$nhta62f2225bf70bfaccbc7f1ef2a397836717377de]);
                }
            }
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Set increment for order
     */
    public function setOrderIncrement($nhtf33d6b5ead5b96fb5cd937f803734e0024ddedf8, $nht4815e7e0e84194823b9519b0730cf301d21987de){
        $nhtf33d6b5ead5b96fb5cd937f803734e0024ddedf8 = array_values($nhtf33d6b5ead5b96fb5cd937f803734e0024ddedf8);
        $nhtf3172007d4de5ae8e7692759d79f67f5558242ed  = $nhtf33d6b5ead5b96fb5cd937f803734e0024ddedf8[0];
        try{
            $nht84405628fc0930fc7527c913b0f4790b5bcb7a1e = $this->_objectManager->create('Magento\Eav\Model\Entity\Store')
                ->loadByEntityStore(5, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
            $nht4815e7e0e84194823b9519b0730cf301d21987de = $this->formatIncrementId($nhtf3172007d4de5ae8e7692759d79f67f5558242ed, $nht4815e7e0e84194823b9519b0730cf301d21987de);
            if (!$nht84405628fc0930fc7527c913b0f4790b5bcb7a1e->getId()) {
                $nht84405628fc0930fc7527c913b0f4790b5bcb7a1e
                    ->setEntityTypeId(5)
                    ->setStoreId($nhtf3172007d4de5ae8e7692759d79f67f5558242ed)
                    ->setIncrementPrefix($nhtf3172007d4de5ae8e7692759d79f67f5558242ed)
                    ->setIncrementLastId($nht4815e7e0e84194823b9519b0730cf301d21987de)
                    ->save();
            } else {
                $nht84405628fc0930fc7527c913b0f4790b5bcb7a1e
                    ->setIncrementLastId($nht4815e7e0e84194823b9519b0730cf301d21987de)
                    ->save();
            }
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){}
    }

    /**
     * Format increment to increment construct of magento
     */
    public function formatIncrementId($nhtf3172007d4de5ae8e7692759d79f67f5558242ed, $nht87ea5dfc8b8e384d848979496e706390b497e547, $nht3567d2b1eff085e553dff73d60d25816b8c53dd5 = 8, $nht88d3b0b0d886bf0b74db6fdf5bee5bb2d90f577f = '0'){
        $nht4815e7e0e84194823b9519b0730cf301d21987de = ($nht87ea5dfc8b8e384d848979496e706390b497e547 < 0)? '-' : '';
        $nht4815e7e0e84194823b9519b0730cf301d21987de .= $nhtf3172007d4de5ae8e7692759d79f67f5558242ed . str_pad((string)abs($nht87ea5dfc8b8e384d848979496e706390b497e547), $nht3567d2b1eff085e553dff73d60d25816b8c53dd5, $nht88d3b0b0d886bf0b74db6fdf5bee5bb2d90f577f, STR_PAD_LEFT);
        return $nht4815e7e0e84194823b9519b0730cf301d21987de;
    }

    /**
     * Get list rating review
     */
    public function getRatingOptions(){
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array();
        $nht7bb083edad4751b79c6b2a9a76f28d0181ae8973 = $this->_objectManager->create('Magento\Review\Model\Rating')->getCollection();
        foreach($nht7bb083edad4751b79c6b2a9a76f28d0181ae8973 as $nht3c2d687ef032e625aa4a2b1cfca9751d2080322c){
            $nhtecda8ad32645327e4765b43649eb6b9720c8eab8 = $nht3c2d687ef032e625aa4a2b1cfca9751d2080322c->getId();
            $nht513f8de9259fe7658fe14d1352c54ccf070e911f = $this->_objectManager->create('Magento\Review\Model\Rating\Option')->getCollection();
            $nht513f8de9259fe7658fe14d1352c54ccf070e911f->addRatingFilter($nhtecda8ad32645327e4765b43649eb6b9720c8eab8);
            $nhtc6ffdb358391388f25ac1ed547e1a08a28877da3 = array();
            foreach($nht513f8de9259fe7658fe14d1352c54ccf070e911f as $nht14eb14ece52df99c284b819d9f8092e50aa1613e){
                $nhtc6ffdb358391388f25ac1ed547e1a08a28877da3 = array_merge($nhtc6ffdb358391388f25ac1ed547e1a08a28877da3, array($nht14eb14ece52df99c284b819d9f8092e50aa1613e->getId()));
            }
            $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd[$nhtecda8ad32645327e4765b43649eb6b9720c8eab8] = array_values($nhtc6ffdb358391388f25ac1ed547e1a08a28877da3);
        }
        return $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd;
    }

    /**
     * Get or create default tax customer
     */
    public function getTaxCustomerDefault(){
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array();
        $nht36fc816cd2f44cb03610648f0ab461456a7c0ea6 = $this->_objectManager->create('Magento\Tax\Model\ClassModel')
            ->getCollection()
            ->setClassTypeFilter(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER)
            ->getFirstItem();
        if($nht36fc816cd2f44cb03610648f0ab461456a7c0ea6->getId()){
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id'] = $nht36fc816cd2f44cb03610648f0ab461456a7c0ea6->getId();
        } else{
            $nhtb834c45519151ed240b91539e7d8506b0cbf7a2d = $this->_objectManager->create('Magento\Tax\Model\ClassModel');
            $nhtb834c45519151ed240b91539e7d8506b0cbf7a2d->setClassType(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER);
            $nhtb834c45519151ed240b91539e7d8506b0cbf7a2d->setClassName('Retail Customer');
            try{
                $nhtb834c45519151ed240b91539e7d8506b0cbf7a2d->save();
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
                $nhteac647ee766cae5de28802b9e7d585d88f0e4ad7 = $nhtb834c45519151ed240b91539e7d8506b0cbf7a2d->getId();
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id'] = $nhteac647ee766cae5de28802b9e7d585d88f0e4ad7;
                $nht64292b1c2b2e13ead8788fc8a2b8edc8c1db4ecd = $this->_objectManager->create('Magento\Customer\Model\Group');
                $nht2037de437c80264ccbce8a8b61d0bf9f593d2322 = $nht64292b1c2b2e13ead8788fc8a2b8edc8c1db4ecd->getCollection();
                foreach($nht2037de437c80264ccbce8a8b61d0bf9f593d2322 as $nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af){
                    if($nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af->getCustomerGroupCode() == 'NOT LOGGED IN' || $nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af->getCustomerGroupCode() == 'General'){
                        $nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af->setTaxClassId($nhteac647ee766cae5de28802b9e7d585d88f0e4ad7);
                        try{$nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af->save();}catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){}
                    }
                }
            } catch(\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
            }
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Get or create manufacturer attribute
     */
    public function getManufacturerAttributeId($nhtad382c070e04f8eb07790fc0362acbf4b9e9dc65){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = array();
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array(
            'attribute_code' 				=> self::MANUFACTURER_CODE,
            'frontend_input'				=> 'select',
            'backend_type'					=> 'int',
            //'apply_to'						=> array(),
            'is_global'						=> 1,
            'is_unique' 					=> 0,
            'is_required' 					=> 0,
            'is_configurable' 				=> 1,
            'is_searchable' 				=> 0,
            'is_visible_in_advanced_search' => 0,
            'is_comparable' 				=> 0,
            'is_filterable' 				=> 0,
            'is_filterable_in_search' 		=> 0,
            'is_used_for_promo_rules' 		=> 0,
            'is_user_defined'               => 1,
            'is_html_allowed_on_front' 		=> 1,
            'is_visible_on_front' 			=> 0,
            'used_in_product_listing' 		=> 0,
            'used_for_sort_by' 				=> 0,
            'frontend_label' 				=> array(
                '0'	=> 'Manufacturer',
            ),
        );
        $nht75f9bdcc9115d70b3b0e6e0cc17de52ec085cc87 = $this->_objectManager->create('Magento\Eav\Model\Entity')->setType(\Magento\Catalog\Model\Product::ENTITY)->getTypeId();
        $nhte6c5b8f1347fae37a957259112ab00fd2a6b91e0 = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute')
            ->getCollection()
            ->addFieldToFilter('attribute_code', $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_code'])
            ->setEntityTypeFilter($nht75f9bdcc9115d70b3b0e6e0cc17de52ec085cc87)
            ->getFirstItem();
        if($nhte6c5b8f1347fae37a957259112ab00fd2a6b91e0->getId()){
            $nht37a5301a88da334dc5afc5b63979daa0f3f45e68['result'] = 'success';
            $nht37a5301a88da334dc5afc5b63979daa0f3f45e68['mage_id'] = $nhte6c5b8f1347fae37a957259112ab00fd2a6b91e0->getId();
            if($nht11db6f6e398d4a944607fefa37a08d95715483cd = $this->_getAttributeGroupId($nhtad382c070e04f8eb07790fc0362acbf4b9e9dc65)){
                $nhte6c5b8f1347fae37a957259112ab00fd2a6b91e0->setAttributeSetId($nhtad382c070e04f8eb07790fc0362acbf4b9e9dc65);
                $nhte6c5b8f1347fae37a957259112ab00fd2a6b91e0->setAttributeGroupId($nht11db6f6e398d4a944607fefa37a08d95715483cd);
                try{
                    $nhte6c5b8f1347fae37a957259112ab00fd2a6b91e0->save();
                } catch(\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
                    // do nothing
                }
            }
        } else {
            $nhtfc88c6b3cc380853de27e44642207df9743ff63d = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute');
            $nhtfc88c6b3cc380853de27e44642207df9743ff63d->setData($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
            $nhtfc88c6b3cc380853de27e44642207df9743ff63d->setEntityTypeId($nht75f9bdcc9115d70b3b0e6e0cc17de52ec085cc87);
            $nhtfc88c6b3cc380853de27e44642207df9743ff63d->setAttributeSetId($nhtad382c070e04f8eb07790fc0362acbf4b9e9dc65);
            if($nht11db6f6e398d4a944607fefa37a08d95715483cd = $this->_getAttributeGroupId($nhtad382c070e04f8eb07790fc0362acbf4b9e9dc65)){
                $nhtfc88c6b3cc380853de27e44642207df9743ff63d->setAttributeGroupId($nht11db6f6e398d4a944607fefa37a08d95715483cd);
            }
            try{
                $nhtfc88c6b3cc380853de27e44642207df9743ff63d->save();
                $nht37a5301a88da334dc5afc5b63979daa0f3f45e68['result'] = 'success';
                $nht37a5301a88da334dc5afc5b63979daa0f3f45e68['mage_id'] = $nhtfc88c6b3cc380853de27e44642207df9743ff63d->getId();
            } catch(\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
                $nht37a5301a88da334dc5afc5b63979daa0f3f45e68['result'] = 'error';
                $nht37a5301a88da334dc5afc5b63979daa0f3f45e68['msg'] = $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
            }
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Get default group attribute by attribute set
     */
    protected function _getAttributeGroupId($nhtad382c070e04f8eb07790fc0362acbf4b9e9dc65){
        $nht11db6f6e398d4a944607fefa37a08d95715483cd = false;
        $nhta4943064ec44e7911bd1c63daca6fc9d9d12e7f8 = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Group')
            ->getCollection()
            ->setAttributeSetFilter($nhtad382c070e04f8eb07790fc0362acbf4b9e9dc65)
            ->addFieldToFilter('attribute_group_code', 'general')
            ->getFirstItem();
        if($nhta4943064ec44e7911bd1c63daca6fc9d9d12e7f8->getId()){
            $nht11db6f6e398d4a944607fefa37a08d95715483cd = $nhta4943064ec44e7911bd1c63daca6fc9d9d12e7f8->getId();
        } else{
            $nht61d008774a13356634557508d7f27255c58d3bd1 = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Group')
                ->getCollection()
                ->addFieldToFilter('attribute_set_id', $nhtad382c070e04f8eb07790fc0362acbf4b9e9dc65)
                ->getFirstItem();
            if($nht61d008774a13356634557508d7f27255c58d3bd1->getId()){
                $nht11db6f6e398d4a944607fefa37a08d95715483cd = $nht61d008774a13356634557508d7f27255c58d3bd1->getId();
            } else {
                $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array(
                    'attribute_group_name'  => 'General',
                    'attribute_set_id'      => $nhtad382c070e04f8eb07790fc0362acbf4b9e9dc65
                );
                $nhteefceac78c96e239e484f522da8ab8ecc2dbbba6 = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Set');
                $nhta835f199818d00b8e215c9a6d17934e741eb2f5a = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Group');
                $nhta835f199818d00b8e215c9a6d17934e741eb2f5a->addData($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
                try{
                    $nhta835f199818d00b8e215c9a6d17934e741eb2f5a->save();
                    $nhteefceac78c96e239e484f522da8ab8ecc2dbbba6->setGroups(array($nhta835f199818d00b8e215c9a6d17934e741eb2f5a));
                    $nht11db6f6e398d4a944607fefa37a08d95715483cd = $nhta835f199818d00b8e215c9a6d17934e741eb2f5a->getId();
                } catch(\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
                    // do nothing
                }
            }
        }
        return $nht11db6f6e398d4a944607fefa37a08d95715483cd;
    }

    /**
     * Create tax rule code with string
     */
    public function createTaxRuleCode($nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e){
        $nht042dc4512fa3d391c5170cf3aa61e6a638f84342 = 0;
        $nht6fd39f01c1901955e83b1ac4aa3f06e9be6b60ee = $nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e;
        while($this->_checkTaxRuleCodeExists($nht6fd39f01c1901955e83b1ac4aa3f06e9be6b60ee)){
            $nht042dc4512fa3d391c5170cf3aa61e6a638f84342++;
            $nht6fd39f01c1901955e83b1ac4aa3f06e9be6b60ee = $nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e.'-'.$nht042dc4512fa3d391c5170cf3aa61e6a638f84342;
        }
        return $nht6fd39f01c1901955e83b1ac4aa3f06e9be6b60ee;
    }

    /**
     * Check tax rule code exists
     */
    protected function _checkTaxRuleCodeExists($nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e){
        $nhtf368106e32f619b0f32b15c2141366210085f64a = $this->_objectManager->create('Magento\Tax\Model\Calculation\Rule')
            ->getCollection()
            ->addFieldToFilter('code', $nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e)
            ->getFirstItem();
        if($nhtf368106e32f619b0f32b15c2141366210085f64a->getId()){
            return true;
        }
        return false;
    }

    /**
     * Create tax rate code with string
     */
    public function createTaxRateCode($nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e){
        $nht042dc4512fa3d391c5170cf3aa61e6a638f84342 = 0;
        $nht6fd39f01c1901955e83b1ac4aa3f06e9be6b60ee = $nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e;
        while($this->_checkTaxRateCodeExist($nht6fd39f01c1901955e83b1ac4aa3f06e9be6b60ee)){
            $nht042dc4512fa3d391c5170cf3aa61e6a638f84342++;
            $nht6fd39f01c1901955e83b1ac4aa3f06e9be6b60ee = $nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e.' - '.$nht042dc4512fa3d391c5170cf3aa61e6a638f84342;
        }
        return $nht6fd39f01c1901955e83b1ac4aa3f06e9be6b60ee;
    }

    /**
     * Check tax rate code exists
     */
    protected function _checkTaxRateCodeExist($nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e){
        $nhtf368106e32f619b0f32b15c2141366210085f64a = $this->_objectManager->create('Magento\Tax\Model\Calculation\Rule')
            ->getCollection()
            ->addFieldToFilter('code', $nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e)
            ->getFirstItem();
        if($nhtf368106e32f619b0f32b15c2141366210085f64a->getId()){
            return true;
        }
        return false;
    }

    /**
     * Create product sku by string
     */
    public function createProductSku($nhtcf559ae3526e08d2bf8526eb199d6a7115abe2d2, $nhtf33d6b5ead5b96fb5cd937f803734e0024ddedf8){
        $nht042dc4512fa3d391c5170cf3aa61e6a638f84342 = 0;
        $nht73bdbcdd91dd50169cd8e8fa0ea53f133c7ffd63 = $nhtcf559ae3526e08d2bf8526eb199d6a7115abe2d2;
        while($this->_checkProductSkuExists($nht73bdbcdd91dd50169cd8e8fa0ea53f133c7ffd63, $nhtf33d6b5ead5b96fb5cd937f803734e0024ddedf8)){
            $nht042dc4512fa3d391c5170cf3aa61e6a638f84342++;
            $nht73bdbcdd91dd50169cd8e8fa0ea53f133c7ffd63 = $nhtcf559ae3526e08d2bf8526eb199d6a7115abe2d2.'-'.$nht042dc4512fa3d391c5170cf3aa61e6a638f84342;
        }
        return $nht73bdbcdd91dd50169cd8e8fa0ea53f133c7ffd63;
    }

    /**
     * Check product sku exists
     */
    protected function _checkProductSkuExists($nhtcf559ae3526e08d2bf8526eb199d6a7115abe2d2, $nhtf33d6b5ead5b96fb5cd937f803734e0024ddedf8){
        $nht38a007151abe87cc01a5b6e9cc418e85286e2087 = $this->_objectManager->create('Magento\Catalog\Model\Product')
            ->getCollection()
            ->addAttributeToSelect("sku")
            ->addFieldToFilter("sku", array('eq' => $nhtcf559ae3526e08d2bf8526eb199d6a7115abe2d2))
            ->getFirstItem();
        if($nht38a007151abe87cc01a5b6e9cc418e85286e2087->getId()){
            return true;
        }
        return false;
    }

    /**
     * Get region id by name state and country iso code 2
     */
    public function getRegionId($nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c , $nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = null;
        $nht7ff078f264f805f813409af263de234b3316da95 = $this->_objectManager->create('Magento\Directory\Model\Region')
            ->getCollection()
            ->addRegionNameFilter($nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c)
            ->addCountryFilter($nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e)
            ->getFirstItem();
        if($nht7ff078f264f805f813409af263de234b3316da95->getId()){
            $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $nht7ff078f264f805f813409af263de234b3316da95->getId();
        } else{
            $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = 0;
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Get order state by order status
     */
    public function getOrderStateByStatus($nht48a3661d846478fa991a825ebd10b78671444b5b){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = false;
        $nht2037de437c80264ccbce8a8b61d0bf9f593d2322 = $this->_objectManager->create('Magento\Sales\Model\Order\Status')->getCollection()->joinStates();
        foreach($nht2037de437c80264ccbce8a8b61d0bf9f593d2322 as $nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af){
            if($nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af['status'] == $nht48a3661d846478fa991a825ebd10b78671444b5b){
                $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af['state'];
                break ;
            }
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * TODO : Work with image
     */

    /**
     * Download image to media folder
     */
    public function downloadImage($nht81736358b1645103ae83247b10c5f82af641ddfc, $nhtf03b89f33671086e6733828e79c2dc44ad1df37d, $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9, $nhtf336ebc6a984e218380109d8d4c51247e7bca1ef = false, $nhta0bdbaff398b494a6efb881c79286129c823815f = false, $nhtc733792f785f3d6093deb8637a4edb3097f05169 = true, $nht644f8a364fb6d687b510831855c124cd8bcdcd01 = false){
        try{
            if($nhtc733792f785f3d6093deb8637a4edb3097f05169 && !$this->_checkFileTypeImport($nhtf03b89f33671086e6733828e79c2dc44ad1df37d)){
                return false;
            }
            $nhtf31eb189821bb6116b29267d9147b033785d6576 = $this->_objectManager->get('Magento\Store\Model\Store')->getBaseMediaDir() . '/' . $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9 . '/';
            if(!is_dir($nhtf31eb189821bb6116b29267d9147b033785d6576)){
                @mkdir($nhtf31eb189821bb6116b29267d9147b033785d6576, 0777, true);
            }
            $nht465bed676ed8d7720aa0051113e3adab6299cdbc = rtrim($nht81736358b1645103ae83247b10c5f82af641ddfc, '/') . '/';
            if($this->_isUrlEncode($nhtf03b89f33671086e6733828e79c2dc44ad1df37d)){
                $nht465bed676ed8d7720aa0051113e3adab6299cdbc .= ltrim($nhtf03b89f33671086e6733828e79c2dc44ad1df37d, '/');
            } else {
                $nht465bed676ed8d7720aa0051113e3adab6299cdbc .= ltrim($this->_getUrlRealPath($nhtf03b89f33671086e6733828e79c2dc44ad1df37d), '/');
            }
            if(!$this->imageExists($nht465bed676ed8d7720aa0051113e3adab6299cdbc)){
                return false;
            }
            if(!$nhtf336ebc6a984e218380109d8d4c51247e7bca1ef){
                $nht037c5fdfec69c727f49ca9fa3e714ff0ee50e1a5 = $this->_createPathToSave(basename($nhtf03b89f33671086e6733828e79c2dc44ad1df37d));
                $nhtf8fa8ed22e5d17154d67b22279d967923c6173f6 = $nhtf31eb189821bb6116b29267d9147b033785d6576 . $nht037c5fdfec69c727f49ca9fa3e714ff0ee50e1a5;
            } else {
                $nht037c5fdfec69c727f49ca9fa3e714ff0ee50e1a5 = $this->_createPathToSave($nhtf03b89f33671086e6733828e79c2dc44ad1df37d);
                $nhtf8fa8ed22e5d17154d67b22279d967923c6173f6 = $nhtf31eb189821bb6116b29267d9147b033785d6576. $nht037c5fdfec69c727f49ca9fa3e714ff0ee50e1a5;
                if(!is_dir(dirname($nhtf8fa8ed22e5d17154d67b22279d967923c6173f6))){
                    @mkdir(dirname($nhtf8fa8ed22e5d17154d67b22279d967923c6173f6), 0777, true);
                }
            }
            if($nht644f8a364fb6d687b510831855c124cd8bcdcd01){
                $nhtf98961015a0ac393630f4eda3749d644a716da64 = '';
                $nht465bed676ed8d7720aa0051113e3adab6299cdbc .= '?'.$nht644f8a364fb6d687b510831855c124cd8bcdcd01;
                $nht037c5fdfec69c727f49ca9fa3e714ff0ee50e1a5 .= $this->_createPathToSave($nht644f8a364fb6d687b510831855c124cd8bcdcd01);
                $nht594fd1615a341c77829e83ed988f137e1ba96231 = @get_headers($nht465bed676ed8d7720aa0051113e3adab6299cdbc, 1);
                if($nht594fd1615a341c77829e83ed988f137e1ba96231){
                    $nhtb9b0eb0c1e1e5f61a56ef072bca30fce693442e6 = $nht594fd1615a341c77829e83ed988f137e1ba96231['Content-Type'];
                    $nhtf98961015a0ac393630f4eda3749d644a716da64 = $this->_getImageTypeByContentType($nhtb9b0eb0c1e1e5f61a56ef072bca30fce693442e6);
                }
                $nht037c5fdfec69c727f49ca9fa3e714ff0ee50e1a5 .= $nhtf98961015a0ac393630f4eda3749d644a716da64;
                $nhtf8fa8ed22e5d17154d67b22279d967923c6173f6 = $nhtf31eb189821bb6116b29267d9147b033785d6576. $nht037c5fdfec69c727f49ca9fa3e714ff0ee50e1a5;
            }
            $nht3150ecd5e0294534a81ae047ddac559de481d774 = false;
            if ($nhtf03b89f33671086e6733828e79c2dc44ad1df37d != '') {
                $nht1e3f4fd42cd5353ad63d7170b5aa6bc1f719c917 = fopen($nhtf8fa8ed22e5d17154d67b22279d967923c6173f6, 'w');
                $nht91cb6d1538d3cb04e54d7a9d7e6c9f3cee800120 = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.0';
                $nht482bd64c6c9f098c9ef8b77b8f870517bf33a1b9 = curl_init($nht465bed676ed8d7720aa0051113e3adab6299cdbc);
                curl_setopt($nht482bd64c6c9f098c9ef8b77b8f870517bf33a1b9, CURLOPT_USERAGENT, $nht91cb6d1538d3cb04e54d7a9d7e6c9f3cee800120);
                curl_setopt($nht482bd64c6c9f098c9ef8b77b8f870517bf33a1b9, CURLOPT_FILE, $nht1e3f4fd42cd5353ad63d7170b5aa6bc1f719c917);
                curl_setopt($nht482bd64c6c9f098c9ef8b77b8f870517bf33a1b9, CURLOPT_TIMEOUT, 30); //10s
                curl_setopt($nht482bd64c6c9f098c9ef8b77b8f870517bf33a1b9, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($nht482bd64c6c9f098c9ef8b77b8f870517bf33a1b9, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($nht482bd64c6c9f098c9ef8b77b8f870517bf33a1b9, CURLOPT_FOLLOWLOCATION, true);
                $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = curl_exec($nht482bd64c6c9f098c9ef8b77b8f870517bf33a1b9);
                curl_close($nht482bd64c6c9f098c9ef8b77b8f870517bf33a1b9);
                fclose($nht1e3f4fd42cd5353ad63d7170b5aa6bc1f719c917);
            }
            if (file_exists($nhtf8fa8ed22e5d17154d67b22279d967923c6173f6) && filesize($nhtf8fa8ed22e5d17154d67b22279d967923c6173f6)) {
                if(!$nhta0bdbaff398b494a6efb881c79286129c823815f){
                    $nht3150ecd5e0294534a81ae047ddac559de481d774 = $nht037c5fdfec69c727f49ca9fa3e714ff0ee50e1a5;
                } else {
                    $nht3150ecd5e0294534a81ae047ddac559de481d774 = $nhtf8fa8ed22e5d17154d67b22279d967923c6173f6;
                }
            }
            return $nht3150ecd5e0294534a81ae047ddac559de481d774;
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            return false;
        }
    }

    public function imageExists($nht81736358b1645103ae83247b10c5f82af641ddfc){
        $nht594fd1615a341c77829e83ed988f137e1ba96231 = @get_headers($nht81736358b1645103ae83247b10c5f82af641ddfc, 1);
        if(!$nht594fd1615a341c77829e83ed988f137e1ba96231){
            return false;
        }
        $nhtecb252044b5ea0f679ee78ec1a12904739e2904d = $nht594fd1615a341c77829e83ed988f137e1ba96231[0];
        if(strpos($nhtecb252044b5ea0f679ee78ec1a12904739e2904d, "404")){
            return false;
        }
        return true;
    }

    protected function _isUrlEncode($nht3150ecd5e0294534a81ae047ddac559de481d774){
        $nhtfd1d86347fd31a1e404b73ec439ff8fe8dd712d5 = @preg_match('~%[0-9A-F]{2}~i', $nht3150ecd5e0294534a81ae047ddac559de481d774);
        return $nhtfd1d86347fd31a1e404b73ec439ff8fe8dd712d5;
    }

    /**
     * Download image with url
     */
    public function downloadImageFromUrl($nht81736358b1645103ae83247b10c5f82af641ddfc,  $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9, $nhtf336ebc6a984e218380109d8d4c51247e7bca1ef = false, $nhta0bdbaff398b494a6efb881c79286129c823815f = false, $nhtc733792f785f3d6093deb8637a4edb3097f05169 = true){
        $nhte28d540be20ba0591a271ec0604277edd05de82c = false;
        $nht81736358b1645103ae83247b10c5f82af641ddfc = parse_url($nht81736358b1645103ae83247b10c5f82af641ddfc);
        if(isset($nht81736358b1645103ae83247b10c5f82af641ddfc['host'])){
            $nht86dd1cf45142e904cb2e99c2721fac3ca198c6ca = $nht81736358b1645103ae83247b10c5f82af641ddfc['scheme'].'://'.$nht81736358b1645103ae83247b10c5f82af641ddfc['host'];
            $nht3150ecd5e0294534a81ae047ddac559de481d774 = substr($nht81736358b1645103ae83247b10c5f82af641ddfc['path'],1);
            if(isset($nht81736358b1645103ae83247b10c5f82af641ddfc['query'])){
                $nhte28d540be20ba0591a271ec0604277edd05de82c = $nht81736358b1645103ae83247b10c5f82af641ddfc['query'];
            }
        } else {
            $nht86dd1cf45142e904cb2e99c2721fac3ca198c6ca = $this->_cart_url;
            $nht3150ecd5e0294534a81ae047ddac559de481d774 = $nht81736358b1645103ae83247b10c5f82af641ddfc['path'];
        }
        return $this->downloadImage($nht86dd1cf45142e904cb2e99c2721fac3ca198c6ca, $nht3150ecd5e0294534a81ae047ddac559de481d774, $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9, $nhtf336ebc6a984e218380109d8d4c51247e7bca1ef, $nhta0bdbaff398b494a6efb881c79286129c823815f, $nhtc733792f785f3d6093deb8637a4edb3097f05169, $nhte28d540be20ba0591a271ec0604277edd05de82c);
    }

    /**
     * Check image type for import
     */
    protected function _checkFileTypeImport($nhtdf16ff3255e6dfc777b086949b78a08845a85c1e){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = false;
        $nhtc470e5c96ff9fe1d2fb1163e45ca8b77b6cb6848 = array('jpg', 'jpeg', 'gif', 'png');
        $nht65593ce703593144d5a8f5fddff03290c07f4a00 = pathinfo($nhtdf16ff3255e6dfc777b086949b78a08845a85c1e, PATHINFO_EXTENSION);
        if(in_array(strtolower($nht65593ce703593144d5a8f5fddff03290c07f4a00), $nhtc470e5c96ff9fe1d2fb1163e45ca8b77b6cb6848)){
            $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = true;
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Create url by encode special character
     */
    protected function _getUrlRealPath($nht3150ecd5e0294534a81ae047ddac559de481d774){
        $nht4bbc71294cc7d19cbad222e2a72a10bff242c958 = explode('/', $nht3150ecd5e0294534a81ae047ddac559de481d774);
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array();
        foreach($nht4bbc71294cc7d19cbad222e2a72a10bff242c958 as $nhta62f2225bf70bfaccbc7f1ef2a397836717377de => $nht94d5cab6f5fe3422a447ab15436e7a672bc0c09a){
            $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd[$nhta62f2225bf70bfaccbc7f1ef2a397836717377de] = rawurlencode($nht94d5cab6f5fe3422a447ab15436e7a672bc0c09a);
        }
        $nht3150ecd5e0294534a81ae047ddac559de481d774 = implode('/', $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        return $nht3150ecd5e0294534a81ae047ddac559de481d774;
    }

    /**
     * Create path save by replace special character to -
     */
    protected function _createPathToSave($nht3150ecd5e0294534a81ae047ddac559de481d774){
        $nht4bbc71294cc7d19cbad222e2a72a10bff242c958 = explode('/',$nht3150ecd5e0294534a81ae047ddac559de481d774);
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array();
        foreach($nht4bbc71294cc7d19cbad222e2a72a10bff242c958 as $nhta62f2225bf70bfaccbc7f1ef2a397836717377de => $nht94d5cab6f5fe3422a447ab15436e7a672bc0c09a){
            $nht94d5cab6f5fe3422a447ab15436e7a672bc0c09a = preg_replace('/[^A-Za-z0-9._\-]/', '-', $nht94d5cab6f5fe3422a447ab15436e7a672bc0c09a);
            $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd[$nhta62f2225bf70bfaccbc7f1ef2a397836717377de] = $nht94d5cab6f5fe3422a447ab15436e7a672bc0c09a;
        }
        $nht3150ecd5e0294534a81ae047ddac559de481d774 = implode('/',$nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        return $nht3150ecd5e0294534a81ae047ddac559de481d774;
    }

    /**
     * Detect image extension with content type
     */
    protected function _getImageTypeByContentType($nhtb9b0eb0c1e1e5f61a56ef072bca30fce693442e6){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = '';
        $nht6c73131b017a6fcbfffca74b74b34133f3df10b5 =array(
            'image/jpeg'    => '.jpg',
            'image/png'     => '.png',
            'image/gif'     => '.gif',
            'image/pjpeg'   => '.jpeg',
            'image/x-icon'  => '.ico',
        );
        if($nht6c73131b017a6fcbfffca74b74b34133f3df10b5[$nhtb9b0eb0c1e1e5f61a56ef072bca30fce693442e6]){
            $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $nht6c73131b017a6fcbfffca74b74b34133f3df10b5[$nhtb9b0eb0c1e1e5f61a56ef072bca30fce693442e6];
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Download image and change image tag in text
     */
    public function changeImgSrcInText($nht950a39b6c2934bb72f2def76c71e88e9c035385f, $nht7f7f4d610e068338fd6772038394cebb90916645){
        if(!$nht7f7f4d610e068338fd6772038394cebb90916645){ return $nht950a39b6c2934bb72f2def76c71e88e9c035385f;}
        $nht379e75c850e1334ef7bece52694c2f26cebec78f = array();
        preg_match_all('/<img[^>]+>/i', $nht950a39b6c2934bb72f2def76c71e88e9c035385f, $nhtbe2862c88fb0039e9d41666dac79fad198bbcf43);
        foreach ($nhtbe2862c88fb0039e9d41666dac79fad198bbcf43[0] as $nht978ea7af39ad5fefb6bfbc82a1c5023494bdade2) {
            preg_match('/(src=["\'](.*?)["\'])/', $nht978ea7af39ad5fefb6bfbc82a1c5023494bdade2, $nhtf27fede2220bcd326aee3e86ddfd4ebd0fe58cb9);
            if(!isset($nhtf27fede2220bcd326aee3e86ddfd4ebd0fe58cb9[0])){
                continue;
            }
            $nht94d5cab6f5fe3422a447ab15436e7a672bc0c09a = preg_split('/["\']/', $nhtf27fede2220bcd326aee3e86ddfd4ebd0fe58cb9[0]);
            $nht379e75c850e1334ef7bece52694c2f26cebec78f[] = $nht94d5cab6f5fe3422a447ab15436e7a672bc0c09a[1];
        }
        $nht379e75c850e1334ef7bece52694c2f26cebec78f = $this->_filterArrayValueDuplicate($nht379e75c850e1334ef7bece52694c2f26cebec78f);
        foreach($nht379e75c850e1334ef7bece52694c2f26cebec78f as $nht4f0aa52d656a3d75867f784b7e9c5d23bf1321c0){
            if($nht9aae419935796434fe2f032790e884cf061b7357 = $this->_getImgDesUrlImport($nht4f0aa52d656a3d75867f784b7e9c5d23bf1321c0)){
                $nht950a39b6c2934bb72f2def76c71e88e9c035385f = str_replace($nht4f0aa52d656a3d75867f784b7e9c5d23bf1321c0, $nht9aae419935796434fe2f032790e884cf061b7357, $nht950a39b6c2934bb72f2def76c71e88e9c035385f);
            }
        }
        return $nht950a39b6c2934bb72f2def76c71e88e9c035385f;
    }

    /**
     * Download image and change image tag in array
     */
    public function changeImgSrcInList($nht38b62be4bddaa5661c7d6b8e36e28159314df5c7, $nht70f132fe13e692866a539996a6b9a05b46076a07, $nht7f7f4d610e068338fd6772038394cebb90916645){
        if(!$nht7f7f4d610e068338fd6772038394cebb90916645){
            return $nht38b62be4bddaa5661c7d6b8e36e28159314df5c7;
        }
        if(is_string($nht70f132fe13e692866a539996a6b9a05b46076a07)){
            $nht70f132fe13e692866a539996a6b9a05b46076a07 = array($nht70f132fe13e692866a539996a6b9a05b46076a07);
        }
        $nht379e75c850e1334ef7bece52694c2f26cebec78f = array();
        foreach($nht38b62be4bddaa5661c7d6b8e36e28159314df5c7 as $nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7){
            foreach($nht70f132fe13e692866a539996a6b9a05b46076a07 as $nht2da0b68df8841752bb747a76780679bcd87c6215){
                if(!isset($nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7[$nht2da0b68df8841752bb747a76780679bcd87c6215])){
                    continue ;
                }
                $nht040f06fd774092478d450774f5ba30c5da78acc8 = $nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7[$nht2da0b68df8841752bb747a76780679bcd87c6215];
                if(!$nht040f06fd774092478d450774f5ba30c5da78acc8){
                    continue ;
                }
                preg_match_all('/<img[^>]+>/i', $nht040f06fd774092478d450774f5ba30c5da78acc8, $nhtbe2862c88fb0039e9d41666dac79fad198bbcf43);
                foreach ($nhtbe2862c88fb0039e9d41666dac79fad198bbcf43[0] as $nht978ea7af39ad5fefb6bfbc82a1c5023494bdade2) {
                    if(!$nht978ea7af39ad5fefb6bfbc82a1c5023494bdade2){
                        continue;
                    }
                    preg_match('/(src=["\'](.*?)["\'])/', $nht978ea7af39ad5fefb6bfbc82a1c5023494bdade2, $nhtf27fede2220bcd326aee3e86ddfd4ebd0fe58cb9);
                    if(!isset($nhtf27fede2220bcd326aee3e86ddfd4ebd0fe58cb9[0])){
                        continue;
                    }
                    $nht94d5cab6f5fe3422a447ab15436e7a672bc0c09a = preg_split('/["\']/', $nhtf27fede2220bcd326aee3e86ddfd4ebd0fe58cb9[0]);
                    $nht379e75c850e1334ef7bece52694c2f26cebec78f[] = $nht94d5cab6f5fe3422a447ab15436e7a672bc0c09a[1];
                }
            }
        }
        $nht379e75c850e1334ef7bece52694c2f26cebec78f = $this->_filterArrayValueDuplicate($nht379e75c850e1334ef7bece52694c2f26cebec78f);
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array();
        foreach($nht379e75c850e1334ef7bece52694c2f26cebec78f as $nht4f0aa52d656a3d75867f784b7e9c5d23bf1321c0){
            $nht9aae419935796434fe2f032790e884cf061b7357 = $this->_getImgDesUrlImport($nht4f0aa52d656a3d75867f784b7e9c5d23bf1321c0);
            if($nht9aae419935796434fe2f032790e884cf061b7357){
                $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd[] = array(
                    'old' => $nht4f0aa52d656a3d75867f784b7e9c5d23bf1321c0,
                    'new' => $nht9aae419935796434fe2f032790e884cf061b7357
                );
            }
        }
        if(!$nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd){
            return $nht38b62be4bddaa5661c7d6b8e36e28159314df5c7;
        }
        foreach($nht38b62be4bddaa5661c7d6b8e36e28159314df5c7 as $nhta62f2225bf70bfaccbc7f1ef2a397836717377de => $nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7){
            foreach($nht70f132fe13e692866a539996a6b9a05b46076a07 as $nht2da0b68df8841752bb747a76780679bcd87c6215){
                if(!isset($nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7[$nht2da0b68df8841752bb747a76780679bcd87c6215])){
                    continue ;
                }
                $nht040f06fd774092478d450774f5ba30c5da78acc8 = $nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7[$nht2da0b68df8841752bb747a76780679bcd87c6215];
                if(!$nht040f06fd774092478d450774f5ba30c5da78acc8){
                    continue ;
                }
                foreach($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd as $nht4f0aa52d656a3d75867f784b7e9c5d23bf1321c0){
                    $nht91cc2e927b3bfb1d4477b744f7c70221ddb86ef1 = array(
                        '/src="' . $nht4f0aa52d656a3d75867f784b7e9c5d23bf1321c0['old'] . '"/',
                        "/src='" . $nht4f0aa52d656a3d75867f784b7e9c5d23bf1321c0['old'] . "'/",
                    );
                    $nht91b5c0a6d4701fe02dc3b4eb37df29c5719a9ec6 = array(
                        'src="' . $nht4f0aa52d656a3d75867f784b7e9c5d23bf1321c0['new'] . '"',
                        "src='" . $nht4f0aa52d656a3d75867f784b7e9c5d23bf1321c0['new'] . "'",
                    );
                    $nht040f06fd774092478d450774f5ba30c5da78acc8 = preg_replace($nht91cc2e927b3bfb1d4477b744f7c70221ddb86ef1, $nht91b5c0a6d4701fe02dc3b4eb37df29c5719a9ec6, $nht040f06fd774092478d450774f5ba30c5da78acc8);
                }
                $nht38b62be4bddaa5661c7d6b8e36e28159314df5c7[$nhta62f2225bf70bfaccbc7f1ef2a397836717377de][$nht2da0b68df8841752bb747a76780679bcd87c6215] = $nht040f06fd774092478d450774f5ba30c5da78acc8;
            }
        }
        return $nht38b62be4bddaa5661c7d6b8e36e28159314df5c7;
    }

    /**
     * Download image with url
     */
    protected function _getImgDesUrlImport($nht81736358b1645103ae83247b10c5f82af641ddfc){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = false;
        $nhte28d540be20ba0591a271ec0604277edd05de82c = false;
        $nht56a77fce67268eb111e4ce62476cf3e97a82368e = parse_url($nht81736358b1645103ae83247b10c5f82af641ddfc);
        if(isset($nht56a77fce67268eb111e4ce62476cf3e97a82368e['host'])){
            $nht86dd1cf45142e904cb2e99c2721fac3ca198c6ca = $nht56a77fce67268eb111e4ce62476cf3e97a82368e['scheme'].'://'.$nht56a77fce67268eb111e4ce62476cf3e97a82368e['host'];
            $nht3150ecd5e0294534a81ae047ddac559de481d774 = substr($nht56a77fce67268eb111e4ce62476cf3e97a82368e['path'],1);
            if(isset($nht56a77fce67268eb111e4ce62476cf3e97a82368e['query'])){
                $nhte28d540be20ba0591a271ec0604277edd05de82c = $nht56a77fce67268eb111e4ce62476cf3e97a82368e['query'];
            }
        } else {
            if(substr($nht56a77fce67268eb111e4ce62476cf3e97a82368e['path'], 0, 2) == '//'){
                $nht75331cc9a0ea535ffcb5dd594703b2f4e808c2d3 = 'http:' . $nht81736358b1645103ae83247b10c5f82af641ddfc;
                $nht56a77fce67268eb111e4ce62476cf3e97a82368e = parse_url($nht75331cc9a0ea535ffcb5dd594703b2f4e808c2d3);
                $nht86dd1cf45142e904cb2e99c2721fac3ca198c6ca = $nht56a77fce67268eb111e4ce62476cf3e97a82368e['scheme'].'://'.$nht56a77fce67268eb111e4ce62476cf3e97a82368e['host'];
                $nht3150ecd5e0294534a81ae047ddac559de481d774 = substr($nht56a77fce67268eb111e4ce62476cf3e97a82368e['path'],1);
                if(isset($nht56a77fce67268eb111e4ce62476cf3e97a82368e['query'])){
                    $nhte28d540be20ba0591a271ec0604277edd05de82c = $nht56a77fce67268eb111e4ce62476cf3e97a82368e['query'];
                }
            } else {
                $nht86dd1cf45142e904cb2e99c2721fac3ca198c6ca = $this->_cart_url;
                $nht3150ecd5e0294534a81ae047ddac559de481d774 = $nht56a77fce67268eb111e4ce62476cf3e97a82368e['path'];
            }
        }
        if($nhtfbbbcb412ff35cc41fc896ad5c9b218e78529400 = $this->downloadImage($nht86dd1cf45142e904cb2e99c2721fac3ca198c6ca, $nht3150ecd5e0294534a81ae047ddac559de481d774, 'wysiwyg', false, false, false, $nhte28d540be20ba0591a271ec0604277edd05de82c)){
            $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->_objectManager->get('Magento\Store\Model\Store')->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'wysiwyg/' . $nhtfbbbcb412ff35cc41fc896ad5c9b218e78529400;
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * TODO : Work with csv file
     */

    /**
     * Read file csv with limit csv line
     */
    public function readCsv($nht068984316f2b10a398fcdef59cbb78b68365f3ea, $nht2b020927d3c6eb407223a1baa3d6ce3597a3f88d, $nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5 = 10, $nht5a537e209151ae5fcccd6326b34b5622bcfb0578 = false){
        if(!is_file($nht068984316f2b10a398fcdef59cbb78b68365f3ea)){
            return array(
                'result' => 'error',
                'msg' => 'Path not exists'
            );
        }
        try{
            $nht2cce4a92f41fe3d55a72c27b922c093bdd0a4267 = false;
            $nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556 = 0;
            $nht6cc981744e5c893179434506139a3107851b061e = fopen($nht068984316f2b10a398fcdef59cbb78b68365f3ea, 'r');
            $nht7a92f3d26362d6557d5701de77a63a01df61e57f = $nht2b020927d3c6eb407223a1baa3d6ce3597a3f88d + $nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5;
            $nhtcd9806c269578005ff14b34ce3dd9b89694bd246 = "";
            $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array();
            while (!feof($nht6cc981744e5c893179434506139a3107851b061e)){
                if($nht5a537e209151ae5fcccd6326b34b5622bcfb0578 && $nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556 > $nht5a537e209151ae5fcccd6326b34b5622bcfb0578){
                    $nht2cce4a92f41fe3d55a72c27b922c093bdd0a4267 = true;
                    break ;
                }
                if($nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556 > $nht7a92f3d26362d6557d5701de77a63a01df61e57f){
                    break ;
                }
                $nht264f39cab871e4cfd65b3a002f7255888bb5ed97 = fgetcsv($nht6cc981744e5c893179434506139a3107851b061e);
                if ($nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556 == 0) {
                    $nhtcd9806c269578005ff14b34ce3dd9b89694bd246 = $nht264f39cab871e4cfd65b3a002f7255888bb5ed97;
                }
                if($nht2b020927d3c6eb407223a1baa3d6ce3597a3f88d < $nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556 && $nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556 <= $nht7a92f3d26362d6557d5701de77a63a01df61e57f){
                    $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd[] = array(
                        'title' => $nhtcd9806c269578005ff14b34ce3dd9b89694bd246,
                        'row' => $nht264f39cab871e4cfd65b3a002f7255888bb5ed97
                    );
                }
                $nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556++;
            }
            fclose($nht6cc981744e5c893179434506139a3107851b061e);
            if(!$nht2cce4a92f41fe3d55a72c27b922c093bdd0a4267 && ($nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556 - 1) <$nht7a92f3d26362d6557d5701de77a63a01df61e57f){
                $nht2cce4a92f41fe3d55a72c27b922c093bdd0a4267 = true;
            }
            return array(
                'result' => 'success',
                'data' => $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd,
                'count' => $nht7a92f3d26362d6557d5701de77a63a01df61e57f,
                'finish' => $nht2cce4a92f41fe3d55a72c27b922c093bdd0a4267
            );
        } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            return array(
                'result' => 'error',
                'msg' => $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage()
            );
        }
    }

    /**
     * Add csv title to csv data
     */
    public function syncCsvTitleRow($nhtcd9806c269578005ff14b34ce3dd9b89694bd246, $nhtb3a7eb99208e878c7b06803a13c8887199b35a44){
        if(!$nhtb3a7eb99208e878c7b06803a13c8887199b35a44){
            return array();
        }
        $nhtfd95779c38049fa55c2c6d52b16e08d4a2e32a59 = array_filter($nhtb3a7eb99208e878c7b06803a13c8887199b35a44);
        if(!$nhtfd95779c38049fa55c2c6d52b16e08d4a2e32a59 || empty($nhtfd95779c38049fa55c2c6d52b16e08d4a2e32a59)){
            return array();
        }
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array();
        foreach ($nhtcd9806c269578005ff14b34ce3dd9b89694bd246 as $nhta62f2225bf70bfaccbc7f1ef2a397836717377de => $nht5990d0602b4f2a6f893fd55666c3463c16aee5ce){
            $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd[$nht5990d0602b4f2a6f893fd55666c3463c16aee5ce] = (isset($nhtb3a7eb99208e878c7b06803a13c8887199b35a44[$nhta62f2225bf70bfaccbc7f1ef2a397836717377de]))? $nhtb3a7eb99208e878c7b06803a13c8887199b35a44[$nhta62f2225bf70bfaccbc7f1ef2a397836717377de] : null;
        }
        return $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd;
    }

    /**
     * TODO : Extend function
     */

    /**
     * Get list array from list by list field value
     */
    public function getListFromListByListField($nht38b62be4bddaa5661c7d6b8e36e28159314df5c7, $nht2da0b68df8841752bb747a76780679bcd87c6215, $nht048b0cb1b94379c74e7e8c8ede496e3edbea3386){
        if(!$nht38b62be4bddaa5661c7d6b8e36e28159314df5c7){
            return false;
        }
        if(!is_array($nht048b0cb1b94379c74e7e8c8ede496e3edbea3386)){
            $nht048b0cb1b94379c74e7e8c8ede496e3edbea3386 = array($nht048b0cb1b94379c74e7e8c8ede496e3edbea3386);
        }
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = array();
        foreach($nht38b62be4bddaa5661c7d6b8e36e28159314df5c7 as $nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7){
            if(in_array($nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7[$nht2da0b68df8841752bb747a76780679bcd87c6215], $nht048b0cb1b94379c74e7e8c8ede496e3edbea3386)){
                $nht37a5301a88da334dc5afc5b63979daa0f3f45e68[] = $nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7;
            }
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Get list array from list by field  value
     */
    public function getListFromListByField($nht38b62be4bddaa5661c7d6b8e36e28159314df5c7, $nht2da0b68df8841752bb747a76780679bcd87c6215, $nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        if(!$nht38b62be4bddaa5661c7d6b8e36e28159314df5c7){
            return false;
        }
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = array();
        foreach($nht38b62be4bddaa5661c7d6b8e36e28159314df5c7 as $nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7){
            if($nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7[$nht2da0b68df8841752bb747a76780679bcd87c6215] == $nhtf32b67c7e26342af42efabc674d441dca0a281c5){
                $nht37a5301a88da334dc5afc5b63979daa0f3f45e68[] = $nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7;
            }
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    public function filterArrayValueFalse($nht19edc1210777ba4d45049c29280d9cc5e1064c25){
        if(!$nht19edc1210777ba4d45049c29280d9cc5e1064c25){
            return $nht19edc1210777ba4d45049c29280d9cc5e1064c25;
        }
        foreach($nht19edc1210777ba4d45049c29280d9cc5e1064c25 as $nhta62f2225bf70bfaccbc7f1ef2a397836717377de => $nhtf32b67c7e26342af42efabc674d441dca0a281c5){
            if(!$nhtf32b67c7e26342af42efabc674d441dca0a281c5){
                unset($nht19edc1210777ba4d45049c29280d9cc5e1064c25[$nhta62f2225bf70bfaccbc7f1ef2a397836717377de]);
            }
        }
        return $nht19edc1210777ba4d45049c29280d9cc5e1064c25;
    }

    /**
     * Get one array from list array by field value
     */
    public function getRowFromListByField($nht38b62be4bddaa5661c7d6b8e36e28159314df5c7, $nht2da0b68df8841752bb747a76780679bcd87c6215, $nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        if(!$nht38b62be4bddaa5661c7d6b8e36e28159314df5c7){
            return false;
        }
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = false;
        foreach($nht38b62be4bddaa5661c7d6b8e36e28159314df5c7 as $nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7){
            if(isset($nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7[$nht2da0b68df8841752bb747a76780679bcd87c6215]) && $nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7[$nht2da0b68df8841752bb747a76780679bcd87c6215] == $nhtf32b67c7e26342af42efabc674d441dca0a281c5){
                $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7;
                break ;
            }
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Get array value from list array by field value and key of field need
     */
    public function getRowValueFromListByField($nht38b62be4bddaa5661c7d6b8e36e28159314df5c7, $nht2da0b68df8841752bb747a76780679bcd87c6215, $nhtf32b67c7e26342af42efabc674d441dca0a281c5, $nhtefe30e310694de6b40db25e0bb3b5aa8b0c2b976){
        if(!$nht38b62be4bddaa5661c7d6b8e36e28159314df5c7){
            return false;
        }
        $nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7 = $this->getRowFromListByField($nht38b62be4bddaa5661c7d6b8e36e28159314df5c7, $nht2da0b68df8841752bb747a76780679bcd87c6215, $nhtf32b67c7e26342af42efabc674d441dca0a281c5);
        if(!$nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7){
            return false;
        }
        return $nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7[$nhtefe30e310694de6b40db25e0bb3b5aa8b0c2b976];
    }

    /**
     * Get and unique array value by key
     */
    public function duplicateFieldValueFromList($nht38b62be4bddaa5661c7d6b8e36e28159314df5c7, $nht2da0b68df8841752bb747a76780679bcd87c6215){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = array();
        if(!$nht38b62be4bddaa5661c7d6b8e36e28159314df5c7){
            return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
        }
        foreach ((array)$nht38b62be4bddaa5661c7d6b8e36e28159314df5c7 as $nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af) {
            if (isset($nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af[$nht2da0b68df8841752bb747a76780679bcd87c6215])) {
                $nht37a5301a88da334dc5afc5b63979daa0f3f45e68[] = $nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af[$nht2da0b68df8841752bb747a76780679bcd87c6215];
            }
        }
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = array_unique($nht37a5301a88da334dc5afc5b63979daa0f3f45e68);
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Add folder and url to data before save to database
     */
    public function addConfigToArray($nht19edc1210777ba4d45049c29280d9cc5e1064c25){
        $nht19edc1210777ba4d45049c29280d9cc5e1064c25['domain'] = $this->_cart_url;
//        $nht19edc1210777ba4d45049c29280d9cc5e1064c25['folder'] = $this->_folder;
        return $nht19edc1210777ba4d45049c29280d9cc5e1064c25;
    }

    /**
     * Create folder to upload csv
     */
    public function createFolderUpload($nht81736358b1645103ae83247b10c5f82af641ddfc){
        $nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e = $nht81736358b1645103ae83247b10c5f82af641ddfc . time();
        return md5($nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e);
    }

    /**
     * Get url of source cart with suffix
     */
    public function getUrlSuffix($nhtec87faca4cbad909219bbcea9dbbe370a9f8c690){
        $nht81736358b1645103ae83247b10c5f82af641ddfc = rtrim($this->_cart_url, '/') . '/' . ltrim($nhtec87faca4cbad909219bbcea9dbbe370a9f8c690, '/');
        return $nht81736358b1645103ae83247b10c5f82af641ddfc;
    }

    /**
     * Convert result of query get count to count
     */
    public function arrayToCount($nht19edc1210777ba4d45049c29280d9cc5e1064c25, $nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c = false){
        if(empty($nht19edc1210777ba4d45049c29280d9cc5e1064c25)){
            return 0;
        }
        $nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556 = 0;
        if($nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c){
            $nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556 = isset($nht19edc1210777ba4d45049c29280d9cc5e1064c25[0][$nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c])? $nht19edc1210777ba4d45049c29280d9cc5e1064c25[0][$nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c] : 0;
        } else {
            $nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556 = isset($nht19edc1210777ba4d45049c29280d9cc5e1064c25[0][0])? $nht19edc1210777ba4d45049c29280d9cc5e1064c25[0][0] : 0;
        }
        return $nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556;
    }

    /**
     * Convert array to in condition in mysql query
     */
    public function arrayToInCondition($nht19edc1210777ba4d45049c29280d9cc5e1064c25){
        if(empty($nht19edc1210777ba4d45049c29280d9cc5e1064c25)){
            return "('null')";
        }
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = "('".implode("','", $nht19edc1210777ba4d45049c29280d9cc5e1064c25)."')";
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Convert array to set values condition in mysql query
     */
    public function arrayToSetCondition($nht19edc1210777ba4d45049c29280d9cc5e1064c25){
        if(empty($nht19edc1210777ba4d45049c29280d9cc5e1064c25)){
            return '';
        }
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array();
        foreach($nht19edc1210777ba4d45049c29280d9cc5e1064c25 as $nhta62f2225bf70bfaccbc7f1ef2a397836717377de => $nhtf32b67c7e26342af42efabc674d441dca0a281c5){
            $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd[] = "`{$nhta62f2225bf70bfaccbc7f1ef2a397836717377de}` = '{$nhtf32b67c7e26342af42efabc674d441dca0a281c5}'";
        }
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = implode(',', $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Add class success to text for show in console
     */
    public function consoleSuccess($nht19f34ee1e406ea84ca83c835a3301b5d9014a788){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = '<p class="success"> - ' . $nht19f34ee1e406ea84ca83c835a3301b5d9014a788 . '</p>';
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Add class warning to text for show in console
     */
    public function consoleWarning($nht19f34ee1e406ea84ca83c835a3301b5d9014a788){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = '<p class="warning"> - ' . $nht19f34ee1e406ea84ca83c835a3301b5d9014a788 . '</p>';
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Add class error to text for show in console
     */
    public function consoleError($nht19f34ee1e406ea84ca83c835a3301b5d9014a788){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = '<p class="error"> - ' . $nht19f34ee1e406ea84ca83c835a3301b5d9014a788 . '</p>';
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Message if not save info to magento database
     */
    public function errorDatabase($nhte2346381bb8eb382eb8b3877a9d3838996c5ca2d = false){
        $nht19f34ee1e406ea84ca83c835a3301b5d9014a788 = "Magento database isn't working!";
        if($nhte2346381bb8eb382eb8b3877a9d3838996c5ca2d){
            $nht19f34ee1e406ea84ca83c835a3301b5d9014a788 = $this->consoleError($nht19f34ee1e406ea84ca83c835a3301b5d9014a788);
        }
        return array(
            'result' => 'error',
            'msg' => $nht19f34ee1e406ea84ca83c835a3301b5d9014a788
        );
    }

    /**
     * Convert time to string show in console
     */
    public function createTimeToShow($nht714eea0f4c980736bde0065fe73f573487f08e3a){
        $nht52ab86a87214a453d9f82538264f190854915247 = gmdate('H', $nht714eea0f4c980736bde0065fe73f573487f08e3a);
        $nht04987fcab72ade3a87a64267b02a2ad9f8f22484 = gmdate('i', $nht714eea0f4c980736bde0065fe73f573487f08e3a);
        $nht352f7829a2384b001cc12b0c2613c756454a1f6a = gmdate('s', $nht714eea0f4c980736bde0065fe73f573487f08e3a);
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = '';
        if($nht52ab86a87214a453d9f82538264f190854915247 && $nht52ab86a87214a453d9f82538264f190854915247 > 0) $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 .= $nht52ab86a87214a453d9f82538264f190854915247.' hours ';
        if($nht04987fcab72ade3a87a64267b02a2ad9f8f22484 && $nht04987fcab72ade3a87a64267b02a2ad9f8f22484 > 0) $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 .= $nht04987fcab72ade3a87a64267b02a2ad9f8f22484. ' minutes ';
        if($nht352f7829a2384b001cc12b0c2613c756454a1f6a && $nht352f7829a2384b001cc12b0c2613c756454a1f6a >0 ) $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 .= $nht352f7829a2384b001cc12b0c2613c756454a1f6a . ' seconds ';
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Create key by string
     */
    public function joinTextToKey($nht372ea08cab33e71c02c651dbc83a474d32c676ea, $nht3d54973f528b01019a58a52d34d518405a01b891 = false, $nht71fafc4e2fc1e47e234762a96b80512b6b5534c2 = '-', $nht346e3ee198e98146993894d3de8ecab1a86c3e80 = true){
        $nht372ea08cab33e71c02c651dbc83a474d32c676ea .= " ";
        if($nht3d54973f528b01019a58a52d34d518405a01b891){
            $nht3d54973f528b01019a58a52d34d518405a01b891 = (int) $nht3d54973f528b01019a58a52d34d518405a01b891;
            $nht372ea08cab33e71c02c651dbc83a474d32c676ea = substr($nht372ea08cab33e71c02c651dbc83a474d32c676ea, 0, $nht3d54973f528b01019a58a52d34d518405a01b891);
            if($nht7a92f3d26362d6557d5701de77a63a01df61e57f = strrpos($nht372ea08cab33e71c02c651dbc83a474d32c676ea, ' ')){
                $nht372ea08cab33e71c02c651dbc83a474d32c676ea = substr($nht372ea08cab33e71c02c651dbc83a474d32c676ea, 0, strrpos($nht372ea08cab33e71c02c651dbc83a474d32c676ea, ' '));
            }
        }
        $nht372ea08cab33e71c02c651dbc83a474d32c676ea = preg_replace('/[^A-Za-z0-9 ]/', '', $nht372ea08cab33e71c02c651dbc83a474d32c676ea);
        $nht372ea08cab33e71c02c651dbc83a474d32c676ea = preg_replace('/\s+/', ' ',$nht372ea08cab33e71c02c651dbc83a474d32c676ea);
        $nht372ea08cab33e71c02c651dbc83a474d32c676ea = str_replace(' ', $nht71fafc4e2fc1e47e234762a96b80512b6b5534c2, $nht372ea08cab33e71c02c651dbc83a474d32c676ea);
        $nht372ea08cab33e71c02c651dbc83a474d32c676ea = trim($nht372ea08cab33e71c02c651dbc83a474d32c676ea, $nht71fafc4e2fc1e47e234762a96b80512b6b5534c2);
        if($nht346e3ee198e98146993894d3de8ecab1a86c3e80) $nht372ea08cab33e71c02c651dbc83a474d32c676ea = strtolower($nht372ea08cab33e71c02c651dbc83a474d32c676ea);
        return $nht372ea08cab33e71c02c651dbc83a474d32c676ea;
    }

    /**
     * Filter value of array 3D
     */
    protected function _filterArrayValueDuplicate($nht19edc1210777ba4d45049c29280d9cc5e1064c25){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = array();
        if($nht19edc1210777ba4d45049c29280d9cc5e1064c25 && !empty($nht19edc1210777ba4d45049c29280d9cc5e1064c25)){
            $nhtfcfdf3671e8835de6acb7c356a8b7257c72b2ea3 = array_values($nht19edc1210777ba4d45049c29280d9cc5e1064c25);
            foreach($nhtfcfdf3671e8835de6acb7c356a8b7257c72b2ea3 as $nhta62f2225bf70bfaccbc7f1ef2a397836717377de => $nhtf32b67c7e26342af42efabc674d441dca0a281c5){
                foreach($nhtfcfdf3671e8835de6acb7c356a8b7257c72b2ea3  as $nhtb875c4d31d479284740b07f3a63a04a037e221e0 => $nhtf7ba53ed800686e64da90dbe690766233c96aa22){
                    if($nhtb875c4d31d479284740b07f3a63a04a037e221e0 < $nhta62f2225bf70bfaccbc7f1ef2a397836717377de){
                        if($nhtf32b67c7e26342af42efabc674d441dca0a281c5 == $nhtf7ba53ed800686e64da90dbe690766233c96aa22){
                            unset($nhtfcfdf3671e8835de6acb7c356a8b7257c72b2ea3[$nhta62f2225bf70bfaccbc7f1ef2a397836717377de]);
                        }
                    }
                }
            }
            $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = array_values($nhtfcfdf3671e8835de6acb7c356a8b7257c72b2ea3);
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Check sync cart type select and cart type detect
     */
    protected function _checkCartSync($nht293ae992f45cff1d17d3e83eefd2285d47f7c997, $nht81448fe273247b533b9f018e96c158cab7901247) {
        $nht1478c028a16709cb32d8b1a69ccca032ca1d9ef5 = strpos($nht81448fe273247b533b9f018e96c158cab7901247, $nht293ae992f45cff1d17d3e83eefd2285d47f7c997);
        if($nht1478c028a16709cb32d8b1a69ccca032ca1d9ef5 == false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get percent by total and import
     */
    public function getPoint($nht5a537e209151ae5fcccd6326b34b5622bcfb0578, $nht62fdfbd55d19b2a4671102ad7bca17d875f8207a, $nht2cce4a92f41fe3d55a72c27b922c093bdd0a4267 = false){
        if(!$nht2cce4a92f41fe3d55a72c27b922c093bdd0a4267 && $nht5a537e209151ae5fcccd6326b34b5622bcfb0578 == 0){
            return 0;
        }
        if($nht2cce4a92f41fe3d55a72c27b922c093bdd0a4267){
            return 100;
        }
        if ($nht5a537e209151ae5fcccd6326b34b5622bcfb0578 < $nht62fdfbd55d19b2a4671102ad7bca17d875f8207a) {
            $nht71a7ca855f38bc559d0fce6727c7e2d8ada99ff8 = 100;
        } else {
            $nht56b24954548baa93e53261a819eb55a8dcedcbd1 = $nht62fdfbd55d19b2a4671102ad7bca17d875f8207a / $nht5a537e209151ae5fcccd6326b34b5622bcfb0578;
            $nht71a7ca855f38bc559d0fce6727c7e2d8ada99ff8 = number_format($nht56b24954548baa93e53261a819eb55a8dcedcbd1, 2) * 100;
        }
        return $nht71a7ca855f38bc559d0fce6727c7e2d8ada99ff8;
    }

    /**
     * Get message for next entity import
     */
    public function getMsgStartImport($nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = '';
        if(!$nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9){
            $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 .= $this->consoleSuccess("Finished migration!");
            return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
        }
        $nhte7b1fff7007b635892a8f2c7c17f4fabc7aa2f8c = array('taxes', 'manufacturers', 'categories', 'products', 'customers', 'orders', 'reviews');
        $nht61db9a5f5192256980e7fc7778020a05fd6c3e99 = array_search($nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9, $nhte7b1fff7007b635892a8f2c7c17f4fabc7aa2f8c);
        foreach ($nhte7b1fff7007b635892a8f2c7c17f4fabc7aa2f8c as $nhta62f2225bf70bfaccbc7f1ef2a397836717377de => $nhtf32b67c7e26342af42efabc674d441dca0a281c5) {
            if ($nht61db9a5f5192256980e7fc7778020a05fd6c3e99 <= $nhta62f2225bf70bfaccbc7f1ef2a397836717377de && $this->_notice['config']['import'][$nhtf32b67c7e26342af42efabc674d441dca0a281c5]) {
                $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 .= $this->consoleSuccess('Importing ' . $nhtf32b67c7e26342af42efabc674d441dca0a281c5 . ' ... ');
                break;
            }
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Increment order price pass through magento order grand total not equal 0
     */
    public function incrementPriceToImport($nht2097c33723b61c7e24d3d7d27840469989b61b49){
        if($nht2097c33723b61c7e24d3d7d27840469989b61b49 == 0){
            $nht2097c33723b61c7e24d3d7d27840469989b61b49 = 0.001;
        }
        return $nht2097c33723b61c7e24d3d7d27840469989b61b49;
    }

    /**
     * Convert string of full name to first name and last name
     */
    public function getNameFromString($nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = array();
        $nht909e6bf0ca53b6aa378c1e94b0db124a102075e2 = explode(' ', $nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c);
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68['lastname'] = array_pop($nht909e6bf0ca53b6aa378c1e94b0db124a102075e2);
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68['firstname'] = implode(" ", $nht909e6bf0ca53b6aa378c1e94b0db124a102075e2);
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Delete folder and content of folder
     */
    public function deleteDir($nht3150ecd5e0294534a81ae047ddac559de481d774){
        if(!is_dir($nht3150ecd5e0294534a81ae047ddac559de481d774)){
            return array(
                'result' => 'error',
                'msg' => 'Path is not directory.'
            );
        }
        try{
            $nht3150ecd5e0294534a81ae047ddac559de481d774 = rtrim($nht3150ecd5e0294534a81ae047ddac559de481d774, '/\\');
            $nht7316c8b2e74870d9d7e9d30bbc28ecf4cdf945ee = glob($nht3150ecd5e0294534a81ae047ddac559de481d774 . '/*', GLOB_MARK);
            foreach ($nht7316c8b2e74870d9d7e9d30bbc28ecf4cdf945ee as $nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af) {
                if (is_dir($nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af)) {
                    $this->deleteDir($nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af);
                } else {
                    unlink($nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af);
                }
            }
            rmdir($nht3150ecd5e0294534a81ae047ddac559de481d774);
            return array(
                'result' => 'success'
            );
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            return array(
                'result' => 'error',
                'msg' => $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage()
            );
        }
    }

    /**
     * Unset list key from array
     */
    public function unsetListArray($nhtefe30e310694de6b40db25e0bb3b5aa8b0c2b976, $nht7b22e4182489e1586ed50845e7e2d45beaaff9cd){
        if(!$nhtefe30e310694de6b40db25e0bb3b5aa8b0c2b976 || !is_array($nhtefe30e310694de6b40db25e0bb3b5aa8b0c2b976) || !is_array($nht7b22e4182489e1586ed50845e7e2d45beaaff9cd)){
            return $nht7b22e4182489e1586ed50845e7e2d45beaaff9cd;
        }
        foreach($nhtefe30e310694de6b40db25e0bb3b5aa8b0c2b976 as $nhta62f2225bf70bfaccbc7f1ef2a397836717377de){
            if(isset($nht7b22e4182489e1586ed50845e7e2d45beaaff9cd[$nhta62f2225bf70bfaccbc7f1ef2a397836717377de])){
                unset($nht7b22e4182489e1586ed50845e7e2d45beaaff9cd[$nhta62f2225bf70bfaccbc7f1ef2a397836717377de]);
            }
        }
        return $nht7b22e4182489e1586ed50845e7e2d45beaaff9cd;
    }

    public function getArrayValueByValueArray($nhtf32b67c7e26342af42efabc674d441dca0a281c5, $nhtefe30e310694de6b40db25e0bb3b5aa8b0c2b976 = array(), $nht7b22e4182489e1586ed50845e7e2d45beaaff9cd = array()){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = false;
        if(!is_array($nhtefe30e310694de6b40db25e0bb3b5aa8b0c2b976) || !is_array($nht7b22e4182489e1586ed50845e7e2d45beaaff9cd)){
            return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
        }
        $nhta62f2225bf70bfaccbc7f1ef2a397836717377de = array_search($nhtf32b67c7e26342af42efabc674d441dca0a281c5, $nhtefe30e310694de6b40db25e0bb3b5aa8b0c2b976);
        if($nhta62f2225bf70bfaccbc7f1ef2a397836717377de === false){
            return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
        }
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = isset($nht7b22e4182489e1586ed50845e7e2d45beaaff9cd[$nhta62f2225bf70bfaccbc7f1ef2a397836717377de]) ? $nht7b22e4182489e1586ed50845e7e2d45beaaff9cd[$nhta62f2225bf70bfaccbc7f1ef2a397836717377de] : false;
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Client request url
     */
    public function request($nht81736358b1645103ae83247b10c5f82af641ddfc, $nhtbfbaf8b2d1cdf92bf83857fe1748c0f68de03d47 = \Zend\Http\Request::METHOD_GET, $nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90 = array(), $nhtdfba7aade0868074c2861c98e2a9a92f3178a51b = array('timeout' => 60), $nht594fd1615a341c77829e83ed988f137e1ba96231 = array()){

        $nhtd2a04d71301a8915217dd5faf81d12cffd6cd958 = new \Zend\Http\Client($nht81736358b1645103ae83247b10c5f82af641ddfc, $nhtdfba7aade0868074c2861c98e2a9a92f3178a51b);
        $nhtd2a04d71301a8915217dd5faf81d12cffd6cd958->setMethod($nhtbfbaf8b2d1cdf92bf83857fe1748c0f68de03d47);
        if($nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90){
            switch ($nhtbfbaf8b2d1cdf92bf83857fe1748c0f68de03d47) {
                case \Zend\Http\Request::METHOD_GET :
                    $nhtd2a04d71301a8915217dd5faf81d12cffd6cd958->setParameterGet($nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90);
                    break;
                case \Zend\Http\Request::METHOD_POST :
                    $nhtd2a04d71301a8915217dd5faf81d12cffd6cd958->setParameterPost($nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90);
                    break;
                case \Zend\Http\Request::METHOD_PUT :
                    $nhtd2a04d71301a8915217dd5faf81d12cffd6cd958->setParameterPost($nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90);
                    break;
                case \Zend\Http\Request::METHOD_DELETE :
                    $nhtd2a04d71301a8915217dd5faf81d12cffd6cd958->setParameterGet($nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90);
                    break;
                default:
                    $nhtd2a04d71301a8915217dd5faf81d12cffd6cd958->setParameterPost($nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90);
                    break;
            }
        }
        if($nht594fd1615a341c77829e83ed988f137e1ba96231){
            $nhtd2a04d71301a8915217dd5faf81d12cffd6cd958->setHeaders($nht594fd1615a341c77829e83ed988f137e1ba96231);
        }
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = $nhtd2a04d71301a8915217dd5faf81d12cffd6cd958->send();
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $nht0ec6d150549780250a9772c06b619bcc46a0e560->getBody();
        @sleep($this->_notice['setting']['delay']);
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Check url exists
     */
    protected function _urlExists($nht81736358b1645103ae83247b10c5f82af641ddfc){
        $nht594fd1615a341c77829e83ed988f137e1ba96231 = @get_headers($nht81736358b1645103ae83247b10c5f82af641ddfc, 1);
        if(!$nht594fd1615a341c77829e83ed988f137e1ba96231){
            return false;
        }
        $nhtecb252044b5ea0f679ee78ec1a12904739e2904d = $nht594fd1615a341c77829e83ed988f137e1ba96231[0];
        if(strpos($nhtecb252044b5ea0f679ee78ec1a12904739e2904d, "200")){
            return true;
        }
        return false;
    }

    /**
     * TODO : Demo mode
     */

    /**
     * Setup limit to demo mode
     */
    protected function _limitDemoModel($nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9){
        if(Custom::DEMO_MODE){
            return isset($this->_demo_limit[$nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9])? $this->_demo_limit[$nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9] : false;
        }
        return false;
    }

    protected function _limit($nht29c1af461577edd560133e35a12ec4f03ec76eb7){
        $nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5 = false;
        $nht23457129b871d690a3b4d86a51ded0c27ba29a9c = trim($this->_scopeConfig->getValue('leci/general/license'));
        if($nht23457129b871d690a3b4d86a51ded0c27ba29a9c){
            $nht1c72c51184748c76fc7136b4202189a827c219d9 = $this->request(
                chr(104).chr(116).chr(116).chr(112).chr(58).chr(47).chr(47).chr(108).chr(105).chr(116).chr(101).chr(120).chr(116).chr(101).chr(110).chr(115).chr(105).chr(111).chr(110).chr(46).chr(99).chr(111).chr(109).chr(47).chr(108).chr(105).chr(99).chr(101).chr(110).chr(115).chr(101).chr(46).chr(112).chr(104).chr(112),
                \Zend\Http\Request::METHOD_GET,
                array(
                    'user' => "bGl0ZXg=",
                    'pass' => "YUExMjM0NTY=",
                    'action' => "Y2hlY2s=",
                    'license' => base64_encode($nht23457129b871d690a3b4d86a51ded0c27ba29a9c),
                    'cart_type' => base64_encode($this->_notice['config']['cart_type']),
                    'url' => base64_encode($this->_cart_url),
                    'target_type' => base64_encode('magento2'),
                    'save' => true
                )
            );
            if($nht1c72c51184748c76fc7136b4202189a827c219d9){
                $nht1c72c51184748c76fc7136b4202189a827c219d9 = unserialize(base64_decode($nht1c72c51184748c76fc7136b4202189a827c219d9));
                if($nht1c72c51184748c76fc7136b4202189a827c219d9['result'] == 'success'){
                    $nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5 = $nht1c72c51184748c76fc7136b4202189a827c219d9['data']['limit'];
                }
            }
        }
        $this->_notice['config']['limit'] = $nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5 ? $nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5 : 0;
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array();
        if(!$nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5){
            foreach($nht29c1af461577edd560133e35a12ec4f03ec76eb7 as $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9 => $nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556){
                $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd[$nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9] = 0;
            }
            return $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd;
        } else {
            $nht5a537e209151ae5fcccd6326b34b5622bcfb0578 = $nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5;
            if($nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5 === 'unlimit'){
                $nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5 = 'unlimited';
                $this->_notice['config']['limit'] = 'unlimited';
            }
            if($nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5 !== 'unlimited'){
                foreach($nht29c1af461577edd560133e35a12ec4f03ec76eb7 as $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9 => $nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556){
                    $nht6e6cc6723dfda6616bedcdeccdedfdf4f67de35c = ($nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556 < $nht5a537e209151ae5fcccd6326b34b5622bcfb0578)? $nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556 : $nht5a537e209151ae5fcccd6326b34b5622bcfb0578;
                    $nht29c1af461577edd560133e35a12ec4f03ec76eb7[$nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9] = $nht6e6cc6723dfda6616bedcdeccdedfdf4f67de35c;
                }
            }
        }
        if(Custom::DEMO_MODE){
            $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array();
            foreach($nht29c1af461577edd560133e35a12ec4f03ec76eb7 as $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9 => $nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556){
                $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd[$nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9] = ($nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556 < $this->_demo_limit[$nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9])? $nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556 : $this->_demo_limit[$nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9];
            }
            return $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd;
        }
        return $nht29c1af461577edd560133e35a12ec4f03ec76eb7;
    }

    public function updateApi(){
        $nht23457129b871d690a3b4d86a51ded0c27ba29a9c = trim($this->_scopeConfig->getValue('leci/general/license'));
        if(!$nht23457129b871d690a3b4d86a51ded0c27ba29a9c){
            return ;
        }
        if($nht23457129b871d690a3b4d86a51ded0c27ba29a9c){
            $nht1c72c51184748c76fc7136b4202189a827c219d9 = $this->request(
                chr(104).chr(116).chr(116).chr(112).chr(58).chr(47).chr(47).chr(108).chr(105).chr(116).chr(101).chr(120).chr(116).chr(101).chr(110).chr(115).chr(105).chr(111).chr(110).chr(46).chr(99).chr(111).chr(109).chr(47).chr(108).chr(105).chr(99).chr(101).chr(110).chr(115).chr(101).chr(46).chr(112).chr(104).chr(112),
                \Zend\Http\Request::METHOD_GET,
                array(
                    'user' => "bGl0ZXg=",
                    'pass' => "YUExMjM0NTY=",
                    'action' => "dXBkYXRl",
                    'license' => base64_encode($nht23457129b871d690a3b4d86a51ded0c27ba29a9c),
                    'cart_type' => base64_encode($this->_notice['config']['cart_type']),
                    'url' => base64_encode($this->_cart_url),
                    'base' => $this->_objectManager->get('Magento\Store\Model\Store')->getBaseUrl(),
                    'target_type' => base64_encode('magento2'),
                )
            );
            if($nht1c72c51184748c76fc7136b4202189a827c219d9){
            }
        }
    }

    /**
     * Import password for customer
     */
    protected function _importCustomerRawPass($nhta7a13f4cacb744524e44dfdad329d540144d209d, $nht9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684)
    {
        return $this->updateTable('customer_entity', array(
            'password_hash' => $nht9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684
        ), array(
            'entity_id' => $nhta7a13f4cacb744524e44dfdad329d540144d209d
        ));
    }

    public function generateUrlByName($nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c)
    {
        $nht4bb4ca75941b7bbc5bc6a12be44b22fc9c8d234e = $this->_objectManager->get('Magento\Framework\Filter\FilterManager');
        $nht81736358b1645103ae83247b10c5f82af641ddfc = $nht4bb4ca75941b7bbc5bc6a12be44b22fc9c8d234e->translitUrl($nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c);
        return $nht81736358b1645103ae83247b10c5f82af641ddfc;
    }

    public function generateCategoryUrlKey($nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c, $nhtd8fd39d0bbdd2dcf322d8b11390a4c5825b11495 = null, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed = null)
    {
        $nhte88e5b5abb39cf459c05203e7221ce658ed011e7 = $nhtf3172007d4de5ae8e7692759d79f67f5558242ed;
        if(!$nhte88e5b5abb39cf459c05203e7221ce658ed011e7){
            $nht2d41b002beb3998d550a238e640a606d2f553018 = $this->_objectManager->create('Magento\Store\Model\StoreManagerInterface');
            $nhte88e5b5abb39cf459c05203e7221ce658ed011e7 = $nht2d41b002beb3998d550a238e640a606d2f553018->getStore()->getId();
        }
        $nhtc7610c8aab390ae6b573937492a719a0ed8704e0 = $this->_scopeConfig->getValue(\Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $nhte88e5b5abb39cf459c05203e7221ce658ed011e7);
        $nhtb4ebfe34d0fa97f0dd2bb1234fad8f59805f4e8d = '';
        if($nhtd8fd39d0bbdd2dcf322d8b11390a4c5825b11495){
            $nhtb4ebfe34d0fa97f0dd2bb1234fad8f59805f4e8d = $nhtd8fd39d0bbdd2dcf322d8b11390a4c5825b11495->getUrlPath();
            $nhtb4ebfe34d0fa97f0dd2bb1234fad8f59805f4e8d .= '/';
        }
        $nht0541ca0931ee81a65838f6b5d5f8bf24a1d88ae1 = $this->generateUrlByName($nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c);
        $nht5b61144adbd59e2664004d0b3dabf1432f13cc49 = $nht0541ca0931ee81a65838f6b5d5f8bf24a1d88ae1;
        $nhtec87faca4cbad909219bbcea9dbbe370a9f8c690 = 1;
        $nht81736358b1645103ae83247b10c5f82af641ddfc = $nhtb4ebfe34d0fa97f0dd2bb1234fad8f59805f4e8d . $nht5b61144adbd59e2664004d0b3dabf1432f13cc49 . $nhtc7610c8aab390ae6b573937492a719a0ed8704e0;
        $nht4d68c8f13459c0edb40504de5003ec2a6b74e613 = $this->urlWriteExists($nht81736358b1645103ae83247b10c5f82af641ddfc, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
        while($nht4d68c8f13459c0edb40504de5003ec2a6b74e613){
            $nht5b61144adbd59e2664004d0b3dabf1432f13cc49 = $nht0541ca0931ee81a65838f6b5d5f8bf24a1d88ae1 . '-' . $nhtec87faca4cbad909219bbcea9dbbe370a9f8c690;
            $nht81736358b1645103ae83247b10c5f82af641ddfc = $nhtb4ebfe34d0fa97f0dd2bb1234fad8f59805f4e8d . $nht5b61144adbd59e2664004d0b3dabf1432f13cc49 . $nhtc7610c8aab390ae6b573937492a719a0ed8704e0;
            $nhtec87faca4cbad909219bbcea9dbbe370a9f8c690++;
            $nht4d68c8f13459c0edb40504de5003ec2a6b74e613 = $this->urlWriteExists($nht81736358b1645103ae83247b10c5f82af641ddfc, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
        }
        return $nht5b61144adbd59e2664004d0b3dabf1432f13cc49;
    }

    public function generateProductUrlKey($nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed = null)
    {
        $nhte88e5b5abb39cf459c05203e7221ce658ed011e7 = $nhtf3172007d4de5ae8e7692759d79f67f5558242ed;
        if(!$nhte88e5b5abb39cf459c05203e7221ce658ed011e7){
            $nht2d41b002beb3998d550a238e640a606d2f553018 = $this->_objectManager->create('Magento\Store\Model\StoreManagerInterface');
            $nhte88e5b5abb39cf459c05203e7221ce658ed011e7 = $nht2d41b002beb3998d550a238e640a606d2f553018->getStore()->getId();
        }
        $nhtc7610c8aab390ae6b573937492a719a0ed8704e0 = $this->_scopeConfig->getValue(\Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator::XML_PATH_PRODUCT_URL_SUFFIX, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $nhte88e5b5abb39cf459c05203e7221ce658ed011e7);
        $nht0541ca0931ee81a65838f6b5d5f8bf24a1d88ae1 = $this->generateUrlByName($nht6ae999552a0d2dca14d62e2bc8b764d377b1dd6c);
        $nht5b61144adbd59e2664004d0b3dabf1432f13cc49 = $nht0541ca0931ee81a65838f6b5d5f8bf24a1d88ae1;
        $nhtec87faca4cbad909219bbcea9dbbe370a9f8c690 = 1;
        $nht81736358b1645103ae83247b10c5f82af641ddfc = $nht5b61144adbd59e2664004d0b3dabf1432f13cc49 . $nhtc7610c8aab390ae6b573937492a719a0ed8704e0;
        $nht4d68c8f13459c0edb40504de5003ec2a6b74e613 = $this->urlWriteExists($nht81736358b1645103ae83247b10c5f82af641ddfc, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
        while($nht4d68c8f13459c0edb40504de5003ec2a6b74e613){
            $nht5b61144adbd59e2664004d0b3dabf1432f13cc49 = $nht0541ca0931ee81a65838f6b5d5f8bf24a1d88ae1 . '-' . $nhtec87faca4cbad909219bbcea9dbbe370a9f8c690;
            $nhtec87faca4cbad909219bbcea9dbbe370a9f8c690++;
            $nht81736358b1645103ae83247b10c5f82af641ddfc = $nht5b61144adbd59e2664004d0b3dabf1432f13cc49 . $nhtc7610c8aab390ae6b573937492a719a0ed8704e0;
            $nht4d68c8f13459c0edb40504de5003ec2a6b74e613 = $this->urlWriteExists($nht81736358b1645103ae83247b10c5f82af641ddfc, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
        }
        return $nht5b61144adbd59e2664004d0b3dabf1432f13cc49;
    }

    public function urlWriteExists($nht6cfbecdc4d3c96e0e62d7932df5c5899fe8538c8, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed = null)
    {
        $nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd = array();
        $nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd['request_path'] = $nht6cfbecdc4d3c96e0e62d7932df5c5899fe8538c8;
        if($nhtf3172007d4de5ae8e7692759d79f67f5558242ed){
            $nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd['store_id'] = $nhtf3172007d4de5ae8e7692759d79f67f5558242ed;
        }
        $nht81448fe273247b533b9f018e96c158cab7901247 = $this->selectTable('url_rewrite', $nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd);
        return $nht81448fe273247b533b9f018e96c158cab7901247 ? true : false;
    }

    public function checkUrlSame($nht9120580e94f134cb7c9f27cd1e43dbc82980e152, $nht81736358b1645103ae83247b10c5f82af641ddfc){
        $nhte53359fdb49983c12fe9dcc2237fd7ea88283e39 = $this->removeHttp($nht9120580e94f134cb7c9f27cd1e43dbc82980e152);
        $nhtd8f81c0563b9d3edd2c7aecba9ec34b4a64f08a4 = $this->removeHttp($nht81736358b1645103ae83247b10c5f82af641ddfc);
        return (strpos($nhtd8f81c0563b9d3edd2c7aecba9ec34b4a64f08a4, $nhte53359fdb49983c12fe9dcc2237fd7ea88283e39) == false) ? false : true;
    }

    public function removeHttp($nht81736358b1645103ae83247b10c5f82af641ddfc) {
        $nht46076a382c294b3076ebbdcda1c9c148747f3890 = array('http://', 'https://');
        foreach($nht46076a382c294b3076ebbdcda1c9c148747f3890 as $nht3c363836cf4e16666669a25da280a1865c2d2874) {
            if(strpos($nht81736358b1645103ae83247b10c5f82af641ddfc, $nht3c363836cf4e16666669a25da280a1865c2d2874) == 0) {
                return str_replace($nht3c363836cf4e16666669a25da280a1865c2d2874, '', $nht81736358b1645103ae83247b10c5f82af641ddfc);
            }
        }
        return $nht81736358b1645103ae83247b10c5f82af641ddfc;
    }

    public function convertUrlToDownload($nht81736358b1645103ae83247b10c5f82af641ddfc, $nht9120580e94f134cb7c9f27cd1e43dbc82980e152)
    {
        if(!$this->checkUrlSame($nht9120580e94f134cb7c9f27cd1e43dbc82980e152, $nht81736358b1645103ae83247b10c5f82af641ddfc)){
            return null;
        }
        $nhte53359fdb49983c12fe9dcc2237fd7ea88283e39 = $this->removeHttp($nht9120580e94f134cb7c9f27cd1e43dbc82980e152);
        $nhtd8f81c0563b9d3edd2c7aecba9ec34b4a64f08a4 = $this->removeHttp($nht81736358b1645103ae83247b10c5f82af641ddfc);
        $nhtb5d7fea62177af678ebc59092bd9468748fdbdb7 = str_replace($nhte53359fdb49983c12fe9dcc2237fd7ea88283e39, '', $nhtd8f81c0563b9d3edd2c7aecba9ec34b4a64f08a4);
        $nht456d560c5fe7261077e830c238f66f8a54000d32 = str_replace($nhtb5d7fea62177af678ebc59092bd9468748fdbdb7, '', $nht81736358b1645103ae83247b10c5f82af641ddfc);
        return array(
            'domain' => rtrim($nht456d560c5fe7261077e830c238f66f8a54000d32, '/'),
            'path' => ltrim($nhtb5d7fea62177af678ebc59092bd9468748fdbdb7, '/'),
        );
    }

    public function getIdDescByValue($nhtf32b67c7e26342af42efabc674d441dca0a281c5, $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->selectTable(self::TABLE_IMPORT, array(
            'domain' => $this->_cart_url,
            'type' => $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9,
            'value' => $nhtf32b67c7e26342af42efabc674d441dca0a281c5
        ));
        if($nht37a5301a88da334dc5afc5b63979daa0f3f45e68['result'] != 'success'){
            return false;
        }
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = $nht37a5301a88da334dc5afc5b63979daa0f3f45e68['data'];
        if(!$nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd){
            return false;
        }
        return $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd[0]['id_desc'];
    }
}