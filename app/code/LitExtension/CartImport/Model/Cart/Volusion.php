<?php
/**
 * @project: CartImport
 * @author : LitExtension
 * @url    : http://litextension.com
 * @email  : litextension@gmail.com
 */

namespace LitExtension\CartImport\Model\Cart;

class Volusion extends \LitExtension\CartImport\Model\Cart
{

    const VLS_CUR       = 'lecaip_volusion_currency';
    const VLS_TAX       = 'lecaip_volusion_tax';
    const VLS_CAT       = 'lecaip_volusion_category';
    const VLS_PRO       = 'lecaip_volusion_product';
    const VLS_OPT_CAT   = 'lecaip_volusion_option_category';
    const VLS_OPT       = 'lecaip_volusion_option';
    const VLS_KIT       = 'lecaip_volusion_kit';
    const VLS_KIT_LNK   = 'lecaip_volusion_kit_lnk';
    const VLS_CUS       = 'lecaip_volusion_customer';
    const VLS_ORD       = 'lecaip_volusion_order';
    const VLS_ORD_DTL   = 'lecaip_volusion_order_detail';
    const VLS_REV       = 'lecaip_volusion_review';
    const VLS_VERSION   = 1;

    protected $_demo_limit = array(
        'exchangeRates' => 100,
        'taxes' => 100,
        'manufacturers' => 100,
        'categories' => 100,
        'products' => 100,
        'optionCategories' => 100,
        'options' => 100,
        'kits' => 100,
        'kitLinks' => 100,
        'customers' => 100,
        'orders' => 100,
        'orderDetails' => 100,
        'reviews' => 0
    );

    /**
     * List file to upload
     */
    public function getListUpload(){
        $nhtbb73aaafa1596e5425dc514a361ad4ef658f2758 = array(
            array('value' => 'exchangeRates', 'label' => "ExchangeRates"),
            array('value' => 'taxes', 'label' => "Tax"),
            array('value' => 'categories', 'label' => "Categories"),
            array('value' => 'products', 'label' => "Products"),
            array('value' => 'optionCategories', 'label' => "OptionCategories"),
            array('value' => 'options', 'label' => "Options"),
            array('value' => 'kits', 'label' => "KITS"),
            array('value' => 'kitLinks', 'label' => "KITLNKS"),
            array('value' => 'customers', 'label' => "Customers"),
            array('value' => 'orders', 'label' => "Orders"),
            array('value' => 'orderDetails', 'label' => "OrderDetails"),
            array('value' => 'reviews', 'label' => "Reviews")
        );
        return $nhtbb73aaafa1596e5425dc514a361ad4ef658f2758;
    }

    /**
     * Clear database of previous import
     */
    public function clearPreSection(){
        if(!$this->_scopeConfig->getValue('leci/setup/volusion')){
            return ;
        }
        $nht3915396b5fe58dce8505b3e62c31b4f79b3ccc2c = array(
            self::VLS_CUR,
            self::VLS_TAX,
            self::VLS_CAT,
            self::VLS_PRO,
            self::VLS_OPT_CAT,
            self::VLS_OPT,
            self::VLS_KIT,
            self::VLS_KIT_LNK,
            self::VLS_CUS,
            self::VLS_ORD,
            self::VLS_ORD_DTL,
            self::VLS_REV
        );
        foreach($nht3915396b5fe58dce8505b3e62c31b4f79b3ccc2c as $nhtc3ee137d4f22eb06ed1351d644f3674592c90836){
            $this->deleteTable($nhtc3ee137d4f22eb06ed1351d644f3674592c90836, array(
                'domain' => $this->_cart_url
            ));
        }
    }

    /**
     * List allow extensions file upload
     */
    public function getAllowExtensions()
    {
        return array('csv');
    }

    /**
     * Get file name upload by value list upload
     */
    public function getUploadFileName($nht311d027bc7c3111f52564b5b28c56f23f6ceb462)
    {
        return $nht311d027bc7c3111f52564b5b28c56f23f6ceb462 . '.csv';
    }

    /**
     * Config and show warning after user upload file
     */
    public function getUploadInfo($nhtdc9cdbac35c7ca0930709a1c801ecae9c75483b0){
        $nhta1f13b3bc20a296e08c212be9c56c706c10abc4f = array_filter($this->_notice['config']['files']);
        if(!empty($nhta1f13b3bc20a296e08c212be9c56c706c10abc4f)){
            $this->_notice['config']['import_support']['manufacturers'] = false;
            if(!$this->_notice['config']['files']['exchangeRates']){
                $this->_notice['config']['config_support']['currency_map'] = false;
            }
            if(!$this->_notice['config']['files']['taxes']){
                $this->_notice['config']['import_support']['taxes'] = false;
            }
            if(!$this->_notice['config']['files']['categories']){
                $this->_notice['config']['config_support']['category_map'] = false;
                $this->_notice['config']['import_support']['categories'] = false;
            }
            if(!$this->_notice['config']['files']['products']){
                $this->_notice['config']['config_support']['attribute_map'] = false;
                $this->_notice['config']['import_support']['products'] = false;
            }
            if(!$this->_notice['config']['files']['reviews']){
                $this->_notice['config']['import_support']['reviews'] = false;
            }
            if(!$this->_notice['config']['files']['customers']){
                $this->_notice['config']['config_support']['order_status_map'] = false;
                $this->_notice['config']['import_support']['customers'] = false;
                $this->_notice['config']['import_support']['orders'] = false;
            }
            if(!$this->_notice['config']['files']['orders']){
                $this->_notice['config']['config_support']['order_status_map'] = false;
                $this->_notice['config']['import_support']['orders'] = false;
            }
            if(!$this->_notice['config']['files']['taxes']
                && !$this->_notice['config']['files']['customers']
                && !$this->_notice['config']['files']['orders']){
                $this->_notice['config']['config_support']['country_map'] = false;
            }
            foreach($nhta1f13b3bc20a296e08c212be9c56c706c10abc4f as $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9 => $nhtbb73aaafa1596e5425dc514a361ad4ef658f2758){
                if($nhtbb73aaafa1596e5425dc514a361ad4ef658f2758){
                    $nht197d334969b0d3e8741e4beb3aa9f13ba7a0e017 = $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9 . "TableConstruct";
                    $nhtb012113a2ee8300b6d1ed3f9b62d06efc19024ba = $this->$nht197d334969b0d3e8741e4beb3aa9f13ba7a0e017();
                    $nhte204d28a2874f6123747650d3e4003d4357d75eb = isset($nhtb012113a2ee8300b6d1ed3f9b62d06efc19024ba['validation']) ? $nhtb012113a2ee8300b6d1ed3f9b62d06efc19024ba['validation'] : false;
                    $nht7d670f51f8f8e710bf2a047e09395a5f853509d6 = $this->_objectManager->get('Magento\Store\Model\Store')->getBaseMediaDir() . self::FOLDER_SUFFIX . $this->_notice['config']['folder'] . '/' . $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9 . '.csv';
                    $nhtf51f0eae3473daf89fcfff06948d761db4e01bcd = $this->readCsv($nht7d670f51f8f8e710bf2a047e09395a5f853509d6, 0, 1, false);
                    if($nhtf51f0eae3473daf89fcfff06948d761db4e01bcd['result'] == 'success'){
                        foreach($nhtf51f0eae3473daf89fcfff06948d761db4e01bcd['data'] as $nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af){
                            if($nhte204d28a2874f6123747650d3e4003d4357d75eb){
                                foreach($nhte204d28a2874f6123747650d3e4003d4357d75eb as $nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7){
                                    if(!in_array($nhte8cdc05b346aa0d4a91a2bf6d7c6a0941a6555a7, $nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af['title'])){
                                        $nhtdc9cdbac35c7ca0930709a1c801ecae9c75483b0[$nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9] = array(
                                            'elm' => '#ur-' . $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9,
                                            'msg' => "<div class='uir-warning'> File uploaded has incorrect structure</div>"
                                        );
                                    }
                                }
                            }
                        }
                    }

                }
            }
            if(isset($nhta1f13b3bc20a296e08c212be9c56c706c10abc4f['products']) && (!isset($nhta1f13b3bc20a296e08c212be9c56c706c10abc4f['optionCategories']) || !isset($nhta1f13b3bc20a296e08c212be9c56c706c10abc4f['options']))){
                $nhtdc9cdbac35c7ca0930709a1c801ecae9c75483b0['products'] = array(
                    'elm' => '#ur-products',
                    'msg' => "<div class='uir-warning'> Product option not uploaded.</div>"
                );
            }
            if(!isset($nhta1f13b3bc20a296e08c212be9c56c706c10abc4f['products']) && isset($nhta1f13b3bc20a296e08c212be9c56c706c10abc4f['reviews'])){
                $nhtdc9cdbac35c7ca0930709a1c801ecae9c75483b0['reviews'] = array(
                    'elm' => '#ur-reviews',
                    'msg' => "<div class='uir-warning'> Product not uploaded.</div>"
                );
            }
            if(isset($nhta1f13b3bc20a296e08c212be9c56c706c10abc4f['orders']) && !isset($nhta1f13b3bc20a296e08c212be9c56c706c10abc4f['customers'])){
                $nhtdc9cdbac35c7ca0930709a1c801ecae9c75483b0['orders'] = array(
                    'elm' => '#ur-orders',
                    'msg' => "<div class='uir-warning'> Customer not uploaded.</div>"
                );
            }
            if(isset($nhta1f13b3bc20a296e08c212be9c56c706c10abc4f['orders']) && !isset($nhta1f13b3bc20a296e08c212be9c56c706c10abc4f['orderDetails'])){
                $nhtdc9cdbac35c7ca0930709a1c801ecae9c75483b0['orders'] = array(
                    'elm' => '#ur-orders',
                    'msg' => "<div class='uir-warning'>  Order details not uploaded.</div>"
                );
            }
            $this->_notice['csv_import']['function'] = '_setupStorageCsv';
        }
        $this->_notice['config']['config_support']['country_map'] = false;
        return array(
            'result' => 'success',
            'msg' => $nhtdc9cdbac35c7ca0930709a1c801ecae9c75483b0
        );
    }

    /**
     * Process and get data use for config display
     *
     * @return array : Response as success or error with msg
     */
    public function displayConfig(){
        $nhtd8fd39d0bbdd2dcf322d8b11390a4c5825b11495 = parent::displayConfig();
        if($nhtd8fd39d0bbdd2dcf322d8b11390a4c5825b11495["result"] != "success"){
            return $nhtd8fd39d0bbdd2dcf322d8b11390a4c5825b11495;
        }
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array();
        $nhtba353c24084a0d9048add733c42b213a38c15544 = array("Root category");
        $nht0b567eb08aa94980cf73b8222433d2e2181b6e7b = array("Root attribute set");
        $nht0df84d475d3bdfb40b8d87fa6e7e9013d04be8f8 = array(1 => "Default language");
        $nht2360856a680ace2200ca24ccb2bc445ea6c4d092 = array(
            'New - See Order Notes',
            'New',
            'Pending',
            'Processing',
            'Payment Declined',
            'Awaiting Payment',
            'Ready to Ship',
            'Pending Shipment',
            'Partially Shipped',
            'Shipped',
            'Partially Backordered',
            'Backordered',
            'See Line Items',
            'See Order Notes',
            'Partially Returned',
            'Returned',
            'Cancel Order',
            'Cancelled',
        );
        $nhtebdea3620f4144a57de2a68a23251f5df5b471a7 = $this->selectTable(self::VLS_CUR, array(
            'domain' => $this->_cart_url
        ));
        if($nhtebdea3620f4144a57de2a68a23251f5df5b471a7 === false){
            return $this->errorDatabase();
        }
        $nht6055366374487f3eaf5cb84ef2da46ec123ffd99 = array();
        foreach($nhtebdea3620f4144a57de2a68a23251f5df5b471a7 as $nht001517ee5d3d0c7f4481ec2cd77c6aefd2fa802e){
            $nhta62f2225bf70bfaccbc7f1ef2a397836717377de = $nht001517ee5d3d0c7f4481ec2cd77c6aefd2fa802e['er_id'];
            $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = $nht001517ee5d3d0c7f4481ec2cd77c6aefd2fa802e['currency'];
            $nht6055366374487f3eaf5cb84ef2da46ec123ffd99[$nhta62f2225bf70bfaccbc7f1ef2a397836717377de] = $nhtf32b67c7e26342af42efabc674d441dca0a281c5;
        }
        $nht85bd9cde3bff9eccc95733bca5660951962acae6 = array();
        if($this->_notice['config']['files']['taxes']){
            $nht159de3f6817f8fe14dd0e1d714dd91a27c4e2c46 = $this->getTableName(self::VLS_TAX);
            $nhtf18c92306d446cf7d2b5928387113c010b6e2acd = "SELECT taxcountry FROM {$nht159de3f6817f8fe14dd0e1d714dd91a27c4e2c46} WHERE `domain` = '{$this->_cart_url}' GROUP BY taxcountry";
            $nhtd5bc8f8ea0c235d0899e344a6d1f76608f144306 = $this->readQuery($nhtf18c92306d446cf7d2b5928387113c010b6e2acd);
            if($nhtd5bc8f8ea0c235d0899e344a6d1f76608f144306['result'] != 'success'){
                return $this->errorDatabase();
            }
            $nht45d4a783b765079b8f3c32089e4b67036b9bc87b = $this->duplicateFieldValueFromList($nhtd5bc8f8ea0c235d0899e344a6d1f76608f144306['data'], 'taxcountry');
            if($nht45d4a783b765079b8f3c32089e4b67036b9bc87b){
                $nht85bd9cde3bff9eccc95733bca5660951962acae6 = array_merge($nht85bd9cde3bff9eccc95733bca5660951962acae6, $nht45d4a783b765079b8f3c32089e4b67036b9bc87b);
            }
        }
        if($this->_notice['config']['files']['customers']){
            $nht71b30e888c006662a760dad3db609257c595a915 = $this->getTableName(self::VLS_CUS);
            $nht9d57be226d5c756ec9a0337527859ea90798f021 = "SELECT country FROM {$nht71b30e888c006662a760dad3db609257c595a915} WHERE `domain` = '{$this->_cart_url}' GROUP BY country";
            $nht8ace0dbab09ba4cec704c4b12b874394a92f696c = $this->readQuery($nht9d57be226d5c756ec9a0337527859ea90798f021);
            if($nht8ace0dbab09ba4cec704c4b12b874394a92f696c['result'] !== 'success'){
                return $this->errorDatabase();
            }
            $nht67ebf6ae321e5a9e0b7dd22673c04021c351bc66 = $this->duplicateFieldValueFromList($nht8ace0dbab09ba4cec704c4b12b874394a92f696c['data'], 'country');
            if($nht67ebf6ae321e5a9e0b7dd22673c04021c351bc66){
                $nht85bd9cde3bff9eccc95733bca5660951962acae6 = array_merge($nht85bd9cde3bff9eccc95733bca5660951962acae6, $nht67ebf6ae321e5a9e0b7dd22673c04021c351bc66);
            }
        }
        if($this->_notice['config']['files']['orders']){
            $nht93b68ff0e11ec298ac96f631d2508d75499fab18 = $this->getTableName(self::VLS_ORD);
            $nht139ae4ea8fac7d07350d02c8c2f936653a149124 = "SELECT billingcountry FROM {$nht93b68ff0e11ec298ac96f631d2508d75499fab18} WHERE `domain` = '{$this->_cart_url}' GROUP BY billingcountry";
            $nht36378b328caeab45f93b42b7cd4f9a84e87258b8 = "SELECT shipcountry FROM {$nht93b68ff0e11ec298ac96f631d2508d75499fab18} WHERE `domain` = '{$this->_cart_url}' GROUP BY shipcountry";
            $nht79f7f9180a51a2565e733af4fd30e2e4495682cf = $this->readQuery($nht139ae4ea8fac7d07350d02c8c2f936653a149124);
            $nhtcf4c10107c771098bf112e5cac605ddb372a29a1 = $this->readQuery($nht36378b328caeab45f93b42b7cd4f9a84e87258b8);
            if($nht79f7f9180a51a2565e733af4fd30e2e4495682cf['result'] != 'success' || $nhtcf4c10107c771098bf112e5cac605ddb372a29a1['result'] != 'success'){
                return $this->errorDatabase();
            }
            $nhtdfa4178589ac52f47d83140d9299dc4e0fee7194 = $this->duplicateFieldValueFromList($nht79f7f9180a51a2565e733af4fd30e2e4495682cf['data'], 'billingcountry');
            $nht63da8a5bbc08bff01070008bb0ab9b7e06e0b07f = $this->duplicateFieldValueFromList($nhtcf4c10107c771098bf112e5cac605ddb372a29a1['data'], 'shipcountry');
            if($nhtdfa4178589ac52f47d83140d9299dc4e0fee7194){
                $nht85bd9cde3bff9eccc95733bca5660951962acae6 = array_merge($nht85bd9cde3bff9eccc95733bca5660951962acae6, $nhtdfa4178589ac52f47d83140d9299dc4e0fee7194);
            }
            if($nht63da8a5bbc08bff01070008bb0ab9b7e06e0b07f){
                $nht85bd9cde3bff9eccc95733bca5660951962acae6 = array_merge($nht85bd9cde3bff9eccc95733bca5660951962acae6, $nht63da8a5bbc08bff01070008bb0ab9b7e06e0b07f);
            }
        }
        if($nht85bd9cde3bff9eccc95733bca5660951962acae6){
            $nht85bd9cde3bff9eccc95733bca5660951962acae6 = array_unique($nht85bd9cde3bff9eccc95733bca5660951962acae6);
            $nht85bd9cde3bff9eccc95733bca5660951962acae6 = array_filter($nht85bd9cde3bff9eccc95733bca5660951962acae6);
            $this->_notice['config']['countries_data'] = $nht85bd9cde3bff9eccc95733bca5660951962acae6;
        } else {
            $this->_notice['config']['config_support']['country_map'] = false;
        }
        $this->_notice['config']['default_lang'] = 1;
        $this->_notice['config']['category_data'] = $nhtba353c24084a0d9048add733c42b213a38c15544;
        $this->_notice['config']['attribute_data'] = $nht0b567eb08aa94980cf73b8222433d2e2181b6e7b;
        $this->_notice['config']['languages_data'] = $nht0df84d475d3bdfb40b8d87fa6e7e9013d04be8f8;
        $this->_notice['config']['currencies_data'] = $nht6055366374487f3eaf5cb84ef2da46ec123ffd99;
        $this->_notice['config']['order_status_data'] = $nht2360856a680ace2200ca24ccb2bc445ea6c4d092;
        $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Save config of use in config step to notice
     */
    public function displayConfirm($nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90){
        parent::displayConfirm($nhtfd7b034e09b752c24942cd9b0b20c29db2dc3e90);
        return array(
            'result' => 'success'
        );
    }

    /**
     * Get data for import display
     *
     * @return array : Response as success or error with msg
     */
    public function displayImport(){
        $nht159de3f6817f8fe14dd0e1d714dd91a27c4e2c46 = $this->getTableName(self::VLS_TAX);
        $nht393503680458155c94de811a593a7ecab7d4bfa3 = $this->getTableName(self::VLS_CAT);
        $nht3d08b35c6f931dc3dbed3e6101c63b0f9f119470 = $this->getTableName(self::VLS_PRO);
        $nht630afacb366c00a7890bf70d0700f8f5fa4e15b6 = $this->getTableName(self::VLS_CUS);
        $nht666ad8244a44d4bf1bb7d709ab0fac9dbe3bb75a = $this->getTableName(self::VLS_ORD);
        $nht63f21aef7b97de6435b21936fb03c3a5402d77aa = $this->getTableName(self::VLS_REV);
        $nhtb9bc9eb9599dc8cac9a8f99884fcfb4ed933a35f = array(
            'taxes' => "SELECT COUNT(1) AS count FROM {$nht159de3f6817f8fe14dd0e1d714dd91a27c4e2c46} WHERE `domain` = '{$this->_cart_url}'",
            'categories' => "SELECT COUNT(1) AS count FROM {$nht393503680458155c94de811a593a7ecab7d4bfa3} WHERE `domain` = '{$this->_cart_url}'",
            'products' => "SELECT COUNT(1) AS count FROM {$nht3d08b35c6f931dc3dbed3e6101c63b0f9f119470} WHERE `domain` = '{$this->_cart_url}' AND (ischildofproductcode IS NULL OR ischildofproductcode = '')",
            'customers' => "SELECT COUNT(1) AS count FROM {$nht630afacb366c00a7890bf70d0700f8f5fa4e15b6} WHERE `domain` = '{$this->_cart_url}'",
            'orders' => "SELECT COUNT(1) AS count FROM {$nht666ad8244a44d4bf1bb7d709ab0fac9dbe3bb75a} WHERE `domain` = '{$this->_cart_url}'",
            'reviews' => "SELECT COUNT(1) AS count FROM {$nht63f21aef7b97de6435b21936fb03c3a5402d77aa} WHERE `domain` = '{$this->_cart_url}'"
        );
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array();
        foreach($nhtb9bc9eb9599dc8cac9a8f99884fcfb4ed933a35f as $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9 => $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72){
            $nhta7afddb68260a60f86c02a021efba7f216c2e7cf = $this->readQuery($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72);
            if($nhta7afddb68260a60f86c02a021efba7f216c2e7cf['result'] != 'success'){
                return $this->errorDatabase();
            }
            $nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556 = $this->arrayToCount($nhta7afddb68260a60f86c02a021efba7f216c2e7cf['data'], 'count');
            $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd[$nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9] = $nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556;
        }
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = $this->_limit($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        foreach($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd as $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9 => $nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556){
            $this->_notice[$nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9]['total'] = $nhtee9f38e186ba06f57b7b74d7e626b94e13ce2556;
        }
        if (!$this->_notice['config']['add_option']['add_new']) {
            $nhtfea453f853c8645b085126e6517eab38dfaa022f = $this->deleteTable(self::TABLE_IMPORT, array(
                'domain' => $this->_cart_url
            ));
            if (!$nhtfea453f853c8645b085126e6517eab38dfaa022f) {
                return $this->errorDatabase();
            }
        }
        return array(
            'result' => 'success'
        );
    }

    /**
     * Router and work with csv file
     */
    public function storageCsv(){
        if(\LitExtension\CartImport\Model\Custom::CSV_STORAGE){
            return $this->_custom->storageCsvCustom($this);
        }
        $nhtc218e39efa2e1aae69f39d2054528369ce1e1f46 = $this->_notice['csv_import']['function'];
        if(!$nhtc218e39efa2e1aae69f39d2054528369ce1e1f46){
            return array(
                'result' => 'success',
                'msg' => ''
            );
        }
        return $this->$nhtc218e39efa2e1aae69f39d2054528369ce1e1f46();
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
        parent::prepareImportTaxes();
        $nht9fbfdb4b3d7380abf73a3941c2ac1a5f4d4db774 = $this->getTaxCustomerDefault();
        if($nht9fbfdb4b3d7380abf73a3941c2ac1a5f4d4db774['result'] == 'success'){
            $this->taxCustomerSuccess(1, $nht9fbfdb4b3d7380abf73a3941c2ac1a5f4d4db774['mage_id']);
        }
    }

    /**
     * Get data of table convert to tax rule
     */
    public function getTaxes(){
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->_notice['taxes']['id_src'];
        $nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5 = $this->_notice['setting']['taxes'];
        $nht159de3f6817f8fe14dd0e1d714dd91a27c4e2c46 = $this->getTableName(self::VLS_TAX);
        $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72 = "SELECT * FROM {$nht159de3f6817f8fe14dd0e1d714dd91a27c4e2c46} WHERE `domain` = '{$this->_cart_url}' AND taxid > {$nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595} ORDER BY taxid ASC LIMIT {$nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5}";
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->readQuery($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72);
        if($nht37a5301a88da334dc5afc5b63979daa0f3f45e68['result'] != 'success'){
            return $this->errorDatabase(true);
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Get primary key of main tax table
     *
     * @param array $nhte8e27c0a096e5becf6a58884d840636ce26d1f2c : One row of function getTaxes
     * @return int
     */
    public function getTaxId($nhte8e27c0a096e5becf6a58884d840636ce26d1f2c){
        return $nhte8e27c0a096e5becf6a58884d840636ce26d1f2c['taxid'];
    }

    /**
     * Convert source data to data for import
     *
     * @param array $nhte8e27c0a096e5becf6a58884d840636ce26d1f2c : One row of function getTaxes
     * @return array
     */
    public function convertTax($nhte8e27c0a096e5becf6a58884d840636ce26d1f2c){
        if(\LitExtension\CartImport\Model\Custom::TAX_CONVERT){
            return $this->_custom->convertTaxCustom($this, $nhte8e27c0a096e5becf6a58884d840636ce26d1f2c);
        }
        $nht7c0f65ed43906517737da944bf8c5da6d3a94933 = $nht5b2e7a24973ad8110e36ba389dc6e5bf7d19cf31 = $nht0cb931ef6ae7f21b4bfa62f9334fd65a875a073b = array();
        if($nht39786476a71ea7b32cc051894f81d3a4ffe69358 = $this->getIdDescTaxCustomer(1)){
            $nht7c0f65ed43906517737da944bf8c5da6d3a94933[] = $nht39786476a71ea7b32cc051894f81d3a4ffe69358;
        }
        $nht574dd394846fb85d495d0f77dfd4e6fd631077ce = $nhte8e27c0a096e5becf6a58884d840636ce26d1f2c['taxcountry'];
        if($nhte8e27c0a096e5becf6a58884d840636ce26d1f2c['taxstatelong']){
            $nht574dd394846fb85d495d0f77dfd4e6fd631077ce .= "-" . $nhte8e27c0a096e5becf6a58884d840636ce26d1f2c['taxstatelong'];
        }
        $nht87c23c55601ec611c2a0be2607e1bf02726a8a56 = array(
            'class_name' => $nht574dd394846fb85d495d0f77dfd4e6fd631077ce
        );
        $nht1db2d942f751abf8de054eac60a75b854177cd91 = $this->_process->taxProduct($nht87c23c55601ec611c2a0be2607e1bf02726a8a56);
        if($nht1db2d942f751abf8de054eac60a75b854177cd91['result'] == 'success'){
            $nht5b2e7a24973ad8110e36ba389dc6e5bf7d19cf31[] = $nht1db2d942f751abf8de054eac60a75b854177cd91['mage_id'];
        }
        $nht755c3af46e4b57752d869425add8df0ac2d2b8d9 = array();
        $nht755c3af46e4b57752d869425add8df0ac2d2b8d9['code'] = $this->createTaxRateCode($nht574dd394846fb85d495d0f77dfd4e6fd631077ce);
        $nht08854b3ac52197224ea37fdc63e1d96b6f68ef60 = $this->getArrayValueByValueArray($nhte8e27c0a096e5becf6a58884d840636ce26d1f2c['taxcountry'], $this->_notice['config']['countries_data'], $this->_notice['config']['countries']);
        $nhtbf6e37c231c4f4ea56dcd88726949c2b7714acba = $nht08854b3ac52197224ea37fdc63e1d96b6f68ef60 ? $nht08854b3ac52197224ea37fdc63e1d96b6f68ef60 : 'US';
        $nht755c3af46e4b57752d869425add8df0ac2d2b8d9['tax_country_id'] = $nhtbf6e37c231c4f4ea56dcd88726949c2b7714acba;
        if(!$nhte8e27c0a096e5becf6a58884d840636ce26d1f2c['taxstatelong']){
            $nht755c3af46e4b57752d869425add8df0ac2d2b8d9['tax_region_id'] = 0;
        } else {
            $nht755c3af46e4b57752d869425add8df0ac2d2b8d9['tax_region_id'] = $this->getRegionId($nhte8e27c0a096e5becf6a58884d840636ce26d1f2c['taxstatelong'], $nhtbf6e37c231c4f4ea56dcd88726949c2b7714acba);
        }
        $nht755c3af46e4b57752d869425add8df0ac2d2b8d9['zip_is_range'] = 0;
        $nht755c3af46e4b57752d869425add8df0ac2d2b8d9['tax_postcode'] = "*";
        $nht755c3af46e4b57752d869425add8df0ac2d2b8d9['rate'] = $nhte8e27c0a096e5becf6a58884d840636ce26d1f2c['tax1_percent'] ? $nhte8e27c0a096e5becf6a58884d840636ce26d1f2c['tax1_percent'] : 0;
        $nht921c61f78a09f037cb8580ce40466c4b441676d1 = $this->_process->taxRate($nht755c3af46e4b57752d869425add8df0ac2d2b8d9);
        if($nht921c61f78a09f037cb8580ce40466c4b441676d1['result'] == 'success'){
            $nht0cb931ef6ae7f21b4bfa62f9334fd65a875a073b[] = $nht921c61f78a09f037cb8580ce40466c4b441676d1['mage_id'];
        }
        $nhte2da1d24327238b96e1365f19a55877fda4c5b6c = array();
        $nhte2da1d24327238b96e1365f19a55877fda4c5b6c['code'] = $this->createTaxRuleCode($nht574dd394846fb85d495d0f77dfd4e6fd631077ce);
        $nhte2da1d24327238b96e1365f19a55877fda4c5b6c['customer_tax_class_ids'] = $nht7c0f65ed43906517737da944bf8c5da6d3a94933;
        $nhte2da1d24327238b96e1365f19a55877fda4c5b6c['product_tax_class_ids'] = $nht5b2e7a24973ad8110e36ba389dc6e5bf7d19cf31;
        $nhte2da1d24327238b96e1365f19a55877fda4c5b6c['tax_rate_ids'] = $nht0cb931ef6ae7f21b4bfa62f9334fd65a875a073b;
        $nhte2da1d24327238b96e1365f19a55877fda4c5b6c['priority'] = 0;
        $nhte2da1d24327238b96e1365f19a55877fda4c5b6c['position'] = 0;
        $nhte2da1d24327238b96e1365f19a55877fda4c5b6c['calculate_subtotal'] = false;
        $nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c = $this->_custom->convertTaxCustom($this, $nhte8e27c0a096e5becf6a58884d840636ce26d1f2c);
        if($nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c){
            $nhte2da1d24327238b96e1365f19a55877fda4c5b6c = array_merge($nhte2da1d24327238b96e1365f19a55877fda4c5b6c, $nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c);
        }
        return array(
            'result' => 'success',
            'data' => $nhte2da1d24327238b96e1365f19a55877fda4c5b6c
        );
    }

    /**
     * Get data for convert to manufacturer option
     */
    public function getManufacturers(){
        return false;
    }

    /**
     * Get primary key of source manufacturer
     *
     * @param array $nhtac2b14060f486df05967acddba9cbbc26f50cb81 : One row of object in function getManufacturers
     * @return int
     */
    public function getManufacturerId($nhtac2b14060f486df05967acddba9cbbc26f50cb81){
        return false;
    }

    /**
     * Convert source data to data import
     *
     * @param array $nhtac2b14060f486df05967acddba9cbbc26f50cb81 : One row of object in function getManufacturers
     * @return array
     */
    public function convertManufacturer($nhtac2b14060f486df05967acddba9cbbc26f50cb81){
        return false;
    }

    /**
     * Get data of main table use import category
     */
    public function getCategories(){
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->_notice['categories']['id_src'];
        $nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5 = $this->_notice['setting']['categories'];
        $nht4d63e890c02f721589565e563d571c3d798e0698 = $this->getTableName(self::VLS_CAT);
        $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72 = "SELECT * FROM {$nht4d63e890c02f721589565e563d571c3d798e0698} WHERE `domain` = '{$this->_cart_url}' AND categoryid > {$nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595} ORDER BY categoryid ASC LIMIT {$nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5}";
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->readQuery($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72);
        if($nht37a5301a88da334dc5afc5b63979daa0f3f45e68['result'] != 'success'){
            return $this->errorDatabase(true);
        }
        if($this->_notice['config']['add_option']['seo_url'] && $this->_notice['config']['add_option']['seo_plugin']){
            $nht146be438026e6116f8bda274f4eb34476c8af398 = 'LitExtension\CartImport\Model\\' . $this->_pathToName($this->_notice['config']['add_option']['seo_plugin']);
            $this->_seo = $this->_objectManager->create($nht146be438026e6116f8bda274f4eb34476c8af398);
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Get primary key of source category
     *
     * @param array $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165 : One row of object in function getCategories
     * @return int
     */
    public function getCategoryId($nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165){
        return $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165['categoryid'];
    }

    /**
     * Convert source data to data import
     *
     * @param array $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165 : One row of object in function getCategories
     * @return array
     */
    public function convertCategory($nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165){
        if(\LitExtension\CartImport\Model\Custom::CATEGORY_CONVERT){
            return $this->_custom->convertCategoryCustom($this, $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165);
        }
        if($nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165['parentid'] == 0){
            $nht8371aa7e9ded8976da4a05ac9d6181925434b453 = $this->_notice['config']['root_category_id'];
        } else {
            $nht8371aa7e9ded8976da4a05ac9d6181925434b453 = $this->getIdDescCategory($nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165['parentid']);
            if(!$nht8371aa7e9ded8976da4a05ac9d6181925434b453){
                $nht9e370db4636cc60bbba629a086cc82443c26b2d0 = $this->_importCategoryParent($nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165['parentid']);
                if($nht9e370db4636cc60bbba629a086cc82443c26b2d0['result'] == 'error'){
                    return $nht9e370db4636cc60bbba629a086cc82443c26b2d0;
                } else if($nht9e370db4636cc60bbba629a086cc82443c26b2d0['result'] == 'warning'){
                    return array(
                        'result' => 'warning',
                        'msg' => $this->consoleWarning("Category Id = {$nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165['categoryid']} import failed. Error: Could not import parent category id = {$nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165['parentid']}")
                    );
                } else {
                    $nht8371aa7e9ded8976da4a05ac9d6181925434b453 = $nht9e370db4636cc60bbba629a086cc82443c26b2d0['mage_id'];
                }
            }
        }
        $nht347824b6e1a2c7ede593780cded28a9ad0ee522a = array();
        $nht347824b6e1a2c7ede593780cded28a9ad0ee522a['name'] = $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165['categoryname'];
        $nht347824b6e1a2c7ede593780cded28a9ad0ee522a['description']  = $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165['categorydescription'];
        $nht347824b6e1a2c7ede593780cded28a9ad0ee522a['meta_title'] = $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165['metatag_title'];
        $nht347824b6e1a2c7ede593780cded28a9ad0ee522a['meta_keywords'] = $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165['metatag_keywords'];
        $nht347824b6e1a2c7ede593780cded28a9ad0ee522a['meta_description'] = $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165['metatag_description'];
        if($nht8371aa7e9ded8976da4a05ac9d6181925434b453){
            $nhtea25a2a8bbeb4ca5e271b8fbcf780b9658e0c6eb = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($nht8371aa7e9ded8976da4a05ac9d6181925434b453);
            $nht347824b6e1a2c7ede593780cded28a9ad0ee522a['path'] = $nhtea25a2a8bbeb4ca5e271b8fbcf780b9658e0c6eb->getPath();
        } else {
            $nhtea25a2a8bbeb4ca5e271b8fbcf780b9658e0c6eb = null;
            $nht347824b6e1a2c7ede593780cded28a9ad0ee522a['path'] = '';
        }
        $nhtf3172007d4de5ae8e7692759d79f67f5558242ed = $this->_notice['config']['languages'][1];
        $nht347824b6e1a2c7ede593780cded28a9ad0ee522a['url_key'] = $this->generateCategoryUrlKey($nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165['categoryname'], $nhtea25a2a8bbeb4ca5e271b8fbcf780b9658e0c6eb, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
        $nhtd8f81c0563b9d3edd2c7aecba9ec34b4a64f08a4 = $this->removeHttp(strtolower($this->_cart_url));
        $nht2e24db66807162e81f8d307a3bc1f36c3193e689 = 'http' . $nhtd8f81c0563b9d3edd2c7aecba9ec34b4a64f08a4;
        $nhte44dbaf133d0d7c60aa0192aa74bde42596ada90 = '/v/vspfiles/photos/categories/' . $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165['categoryid'] . '.jpg';
        $nht6c897b85fec916bd1c7001543da8b900740a0b67 = '/v/vspfiles/photos/categories/' . $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165['categoryid'] . '.gif';
        if($nht05a2c508d76d1d77d7490b06b59e238d4799fe6c = $this->downloadImage($nht2e24db66807162e81f8d307a3bc1f36c3193e689,  $nht6c897b85fec916bd1c7001543da8b900740a0b67, 'catalog/category')){
            $nht347824b6e1a2c7ede593780cded28a9ad0ee522a['image'] = $nht05a2c508d76d1d77d7490b06b59e238d4799fe6c;
        }
        if($nht05a2c508d76d1d77d7490b06b59e238d4799fe6c = $this->downloadImage($nht2e24db66807162e81f8d307a3bc1f36c3193e689,  $nhte44dbaf133d0d7c60aa0192aa74bde42596ada90, 'catalog/category')){
            $nht347824b6e1a2c7ede593780cded28a9ad0ee522a['image'] = $nht05a2c508d76d1d77d7490b06b59e238d4799fe6c;
        }
        $nht347824b6e1a2c7ede593780cded28a9ad0ee522a['is_active'] = ($nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165['hidden'] == 'N') ? true : false;
        $nht347824b6e1a2c7ede593780cded28a9ad0ee522a['is_anchor'] = 0;
        $nht347824b6e1a2c7ede593780cded28a9ad0ee522a['include_in_menu'] = ($nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165['hidden'] ==  'N') ? 1 : 0;
        $nht347824b6e1a2c7ede593780cded28a9ad0ee522a['display_mode'] = \Magento\Catalog\Model\Category::DM_PRODUCT;
        $nht347824b6e1a2c7ede593780cded28a9ad0ee522a['position'] = $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165['categoryorder'];
        if($this->_seo){
            $nht6170ca2b023edf54ada0f81d18c7c2b3d6db9553 = $this->_seo->convertCategorySeo($this, $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165);
            if($nht6170ca2b023edf54ada0f81d18c7c2b3d6db9553){
                $nht347824b6e1a2c7ede593780cded28a9ad0ee522a['seo_url'] = $nht6170ca2b023edf54ada0f81d18c7c2b3d6db9553;
            }
        }
        $nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c = $this->_custom->convertCategoryCustom($this, $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165);
        if($nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c){
            $nht347824b6e1a2c7ede593780cded28a9ad0ee522a = array_merge($nht347824b6e1a2c7ede593780cded28a9ad0ee522a, $nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c);
        }
        return array(
            'result' => 'success',
            'data' => $nht347824b6e1a2c7ede593780cded28a9ad0ee522a
        );
    }

    /**
     * Process before import products
     */
    public function prepareImportProducts(){
        parent::prepareImportProducts();
        $this->_notice['extend']['website_ids']= $this->getWebsiteIdsByStoreIds($this->_notice['config']['languages']);
    }

    /**
     * Get data of main table use for import product
     */
    public function getProducts(){
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->_notice['products']['id_src'];
        $nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5 = $this->_notice['setting']['products'];
        $nht3d08b35c6f931dc3dbed3e6101c63b0f9f119470 = $this->getTableName(self::VLS_PRO);
        $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72 = "SELECT * FROM {$nht3d08b35c6f931dc3dbed3e6101c63b0f9f119470} WHERE `domain` = '{$this->_cart_url}' AND id > {$nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595} AND (ischildofproductcode IS NULL OR ischildofproductcode = '') ORDER BY id ASC LIMIT {$nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5}";
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->readQuery($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72);
        if($nht37a5301a88da334dc5afc5b63979daa0f3f45e68['result'] != 'success'){
            return $this->errorDatabase(true);
        }
        if($this->_notice['config']['add_option']['seo_url'] && $this->_notice['config']['add_option']['seo_plugin']){
            $nht146be438026e6116f8bda274f4eb34476c8af398 = 'LitExtension\CartImport\Model\\' . $this->_pathToName($this->_notice['config']['add_option']['seo_plugin']);
            $this->_seo = $this->_objectManager->create($nht146be438026e6116f8bda274f4eb34476c8af398);
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Get primary key of source product main
     *
     * @param array $nht38a007151abe87cc01a5b6e9cc418e85286e2087 : One row of object in function getProducts
     * @return int
     */
    public function getProductId($nht38a007151abe87cc01a5b6e9cc418e85286e2087){
        return $nht38a007151abe87cc01a5b6e9cc418e85286e2087['id'];
    }

    /**
     * Check product has been imported
     *
     * @param array $nht38a007151abe87cc01a5b6e9cc418e85286e2087 : One row of object in function getProducts
     * @return boolean
     */
    public function checkProductImport($nht38a007151abe87cc01a5b6e9cc418e85286e2087){
        $nht99c39b067cfa73c783f0fc49a61966ef649466e9 = $nht38a007151abe87cc01a5b6e9cc418e85286e2087['productcode'];
        return $this->_getLeCaIpImportIdDescByValue(self::TYPE_PRODUCT, $nht99c39b067cfa73c783f0fc49a61966ef649466e9);
    }

    /**
     * Convert source data to data import
     *
     * @param array $nht38a007151abe87cc01a5b6e9cc418e85286e2087 : One row of object in function getProducts
     * @return array
     */
    public function convertProduct($nht38a007151abe87cc01a5b6e9cc418e85286e2087){
        if(\LitExtension\CartImport\Model\Custom::PRODUCT_CONVERT){
            return $this->_custom->convertProductCustom($this, $nht38a007151abe87cc01a5b6e9cc418e85286e2087);
        }
        $nhtd3a2c8382fd345aed57e6cad36df73fef9838e96 = $this->_checkProductHasChild($nht38a007151abe87cc01a5b6e9cc418e85286e2087);
        if($nhtd3a2c8382fd345aed57e6cad36df73fef9838e96){
            $nht838a21de507884b976cccbf18e79cf6f1babd43b = $this->_importChildrenProduct($nht38a007151abe87cc01a5b6e9cc418e85286e2087);
            if($nht838a21de507884b976cccbf18e79cf6f1babd43b['result'] != 'success'){
                return $nht838a21de507884b976cccbf18e79cf6f1babd43b;
            }
            $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['type_id'] = \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE;
            $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb = array_merge($nht8a4988b5f3da81038f6461b70095e5144ba1c4fb, $nht838a21de507884b976cccbf18e79cf6f1babd43b['data']);
        } else {
            $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['type_id'] = \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE;
        }
        $nht3452e6adc1fb4f9b0877a311f9a431a0e7dee2bf = $this->_convertProduct($nht38a007151abe87cc01a5b6e9cc418e85286e2087);
        if($nht3452e6adc1fb4f9b0877a311f9a431a0e7dee2bf['result'] != 'success'){
            return $nht3452e6adc1fb4f9b0877a311f9a431a0e7dee2bf;
        }
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb = array_merge($nht8a4988b5f3da81038f6461b70095e5144ba1c4fb, $nht3452e6adc1fb4f9b0877a311f9a431a0e7dee2bf['data']);
        return array(
            'result' => "success",
            'data' => $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb
        );
    }

    /**
     * Import product with data convert in function convertProduct
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of function convertProduct
     * @param array $nht38a007151abe87cc01a5b6e9cc418e85286e2087 : One row of object in function getProducts
     * @return array
     */
    public function importProduct($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht38a007151abe87cc01a5b6e9cc418e85286e2087){
        if(\LitExtension\CartImport\Model\Custom::PRODUCT_IMPORT){
            return $this->_custom->importProductCustom($this, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht38a007151abe87cc01a5b6e9cc418e85286e2087);
        }
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->getProductId($nht38a007151abe87cc01a5b6e9cc418e85286e2087);
        $nht94caa7916e70ab6fe7a8ebc1d0daad006bcf3ffe = $this->_process->product($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        if($nht94caa7916e70ab6fe7a8ebc1d0daad006bcf3ffe['result'] == 'success'){
            $nht32e0b4164798121e0ed86fc6820775f185e5ea3c = $nht94caa7916e70ab6fe7a8ebc1d0daad006bcf3ffe['mage_id'];
            $this->productSuccess($nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595, $nht32e0b4164798121e0ed86fc6820775f185e5ea3c, $nht38a007151abe87cc01a5b6e9cc418e85286e2087['productcode']);
        } else {
            $nht94caa7916e70ab6fe7a8ebc1d0daad006bcf3ffe['result'] = 'warning';
            $nht19f34ee1e406ea84ca83c835a3301b5d9014a788 = "Product code = {$nht38a007151abe87cc01a5b6e9cc418e85286e2087['productcode']} import failed. Error: " . $nht94caa7916e70ab6fe7a8ebc1d0daad006bcf3ffe['msg'];
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
        if(parent::afterSaveProduct($nhtd3c51f863ddb049812537af3b311c7ebb195682c, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht38a007151abe87cc01a5b6e9cc418e85286e2087)){
            return ;
        }
        if($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['type_id'] != \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE && $nht38a007151abe87cc01a5b6e9cc418e85286e2087['optionids']){
            $nht56a78ba90ef18e3f76ab83284e73db436d14e463 = explode(',', $nht38a007151abe87cc01a5b6e9cc418e85286e2087['optionids']);
            $nhtbcc73a55615c38a3cebd5b79ea4b6b4c32a17dfd = $this->arrayToInCondition($nht56a78ba90ef18e3f76ab83284e73db436d14e463);
            $nht4f890bda0d4a47e44cf41881bdf22f818aef284b = $this->getTableName(self::VLS_OPT);
            $nht2676bc5b16711df6aa10b48ceb61a60686d4fe4d = "SELECT * FROM {$nht4f890bda0d4a47e44cf41881bdf22f818aef284b} WHERE `domain` = '{$this->_cart_url}' AND id IN {$nhtbcc73a55615c38a3cebd5b79ea4b6b4c32a17dfd}";
            $nht513f8de9259fe7658fe14d1352c54ccf070e911f = $this->readQuery($nht2676bc5b16711df6aa10b48ceb61a60686d4fe4d);
            if($nht513f8de9259fe7658fe14d1352c54ccf070e911f['result'] != 'success' || empty($nht513f8de9259fe7658fe14d1352c54ccf070e911f['data'])){
                return;
            }
            $nht52a9e5d8fa1515060754e6d90cf5c6969ab99a09 = $this->duplicateFieldValueFromList($nht513f8de9259fe7658fe14d1352c54ccf070e911f['data'], 'optioncatid');
            $nht4899adbd80e198e6ade95b3ef337a5bccd9a600d = $this->arrayToInCondition($nht52a9e5d8fa1515060754e6d90cf5c6969ab99a09);
            $nht3be97e3f3ece764d146edbef80d496cde8140758 = $this->getTableName(self::VLS_OPT_CAT);
            $nht23969933dc2e1b3abd0e719f648e736e002dcb86 = "SELECT * FROM {$nht3be97e3f3ece764d146edbef80d496cde8140758} WHERE `domain` = '{$this->_cart_url}' AND id IN {$nht4899adbd80e198e6ade95b3ef337a5bccd9a600d}";
            $nht49ba7cdad6f9a18274d6d2ce65b918a22c0f463f = $this->readQuery($nht23969933dc2e1b3abd0e719f648e736e002dcb86);
            if($nht49ba7cdad6f9a18274d6d2ce65b918a22c0f463f['result'] != 'success' || empty($nht49ba7cdad6f9a18274d6d2ce65b918a22c0f463f['data'])){
                return;
            }
            $nht40f01e8847b6c183ce820f764d29df7842a6b6c9 = array();
            $nhte7b1fff7007b635892a8f2c7c17f4fabc7aa2f8c = array(
                'DROPDOWN' => 'drop_down',
                'DROPDOWN_CONTROL' => 'drop_down',
                'DROPDOWN_CLIENT' => 'drop_down',
                'DROPDOWN_SMARTMATCH' => 'drop_down',
                'CHECKBOX' => 'checkbox',
                'RADIO' => 'radio',
                'TEXTBOX' => 'field',
                'PLAIN_TEXT' => 'area'
            );
            foreach($nht49ba7cdad6f9a18274d6d2ce65b918a22c0f463f['data'] as $nht909e8ef5e6ff8509055e7bcf2e890843c6a2ea9d){
                $nhtb7461ce4439283ad9847c7f3a1570782b8718e60 = $nht909e8ef5e6ff8509055e7bcf2e890843c6a2ea9d['displaytype'];
                $nhtd3cadf11572c4d71b2e8a3221ec3e30ffd6169a0 = array(
                    'previous_group' => $this->_objectManager->create('Magento\Catalog\Model\Product\Option')->getGroupByType($nhte7b1fff7007b635892a8f2c7c17f4fabc7aa2f8c[$nhtb7461ce4439283ad9847c7f3a1570782b8718e60]),
                    'type' => $nhte7b1fff7007b635892a8f2c7c17f4fabc7aa2f8c[$nhtb7461ce4439283ad9847c7f3a1570782b8718e60],
                    'is_require' => 1,
                    'title' => $nht909e8ef5e6ff8509055e7bcf2e890843c6a2ea9d['optioncategoriesdesc'],
                );
                $nhtee4540c8104c99d409907cdd233d87c10cf9a8c4 = $this->getListFromListByField($nht513f8de9259fe7658fe14d1352c54ccf070e911f['data'], 'optioncatid', $nht909e8ef5e6ff8509055e7bcf2e890843c6a2ea9d['id']);
                if(in_array($nhtb7461ce4439283ad9847c7f3a1570782b8718e60, array('DROPDOWN', 'DROPDOWN_CONTROL', 'DROPDOWN_CLIENT', 'DROPDOWN_SMARTMATCH', 'CHECKBOX', 'RADIO'))){
                    $nhtd3cadf11572c4d71b2e8a3221ec3e30ffd6169a0['values'] = array();
                    if($nhtee4540c8104c99d409907cdd233d87c10cf9a8c4){
                        foreach($nhtee4540c8104c99d409907cdd233d87c10cf9a8c4 as $nht299768430b649c97f6b5cd40ccb19385c1b42ceb){
                            $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = array(
                                'option_type_id' => -1,
                                'title' => strip_tags($nht299768430b649c97f6b5cd40ccb19385c1b42ceb['optionsdesc']),
                                'price' => $nht299768430b649c97f6b5cd40ccb19385c1b42ceb['pricediff'],
                                'price_type' => 'fixed',
                            );
                            $nhtd3cadf11572c4d71b2e8a3221ec3e30ffd6169a0['values'][] = $nhtf32b67c7e26342af42efabc674d441dca0a281c5;
                        }
                    }
                }
                if(in_array($nhtb7461ce4439283ad9847c7f3a1570782b8718e60, array('TEXTBOX', 'PLAIN_TEXT'))){
                    if(isset($nhtee4540c8104c99d409907cdd233d87c10cf9a8c4[0])){
                        $nht299768430b649c97f6b5cd40ccb19385c1b42ceb = $nhtee4540c8104c99d409907cdd233d87c10cf9a8c4[0];
                        $nhtd3cadf11572c4d71b2e8a3221ec3e30ffd6169a0['price'] = $nht299768430b649c97f6b5cd40ccb19385c1b42ceb['pricediff'];
                        $nhtd3cadf11572c4d71b2e8a3221ec3e30ffd6169a0['price_type'] = 'fixed';
                    }
                }
                $nht40f01e8847b6c183ce820f764d29df7842a6b6c9[] = $nhtd3cadf11572c4d71b2e8a3221ec3e30ffd6169a0;
            }
            if($nht40f01e8847b6c183ce820f764d29df7842a6b6c9){
                $this->importProductOption($nhtd3c51f863ddb049812537af3b311c7ebb195682c, $nht40f01e8847b6c183ce820f764d29df7842a6b6c9);
            }
        }
    }

    /**
     * Get data of main table use for import customer
     */
    public function getCustomers(){
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->_notice['customers']['id_src'];
        $nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5 = $this->_notice['setting']['customers'];
        $nht630afacb366c00a7890bf70d0700f8f5fa4e15b6 = $this->getTableName(self::VLS_CUS);
        $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72 = "SELECT * FROM {$nht630afacb366c00a7890bf70d0700f8f5fa4e15b6} WHERE `domain` = '{$this->_cart_url}' AND customerid > {$nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595} ORDER BY customerid ASC LIMIT {$nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5}";
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->readQuery($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72);
        if($nht37a5301a88da334dc5afc5b63979daa0f3f45e68['result'] != 'success'){
            return $this->errorDatabase(true);
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Get primary key of source customer main
     *
     * @param array $nhtb39f008e318efd2bb988d724a161b61c6909677f : One row of object in function getCustomers
     * @return int
     */
    public function getCustomerId($nhtb39f008e318efd2bb988d724a161b61c6909677f){
        return $nhtb39f008e318efd2bb988d724a161b61c6909677f['customerid'];
    }

    /**
     * Convert source data to data import
     *
     * @param array $nhtb39f008e318efd2bb988d724a161b61c6909677f : One row of object in function getCustomers
     * @return array
     */
    public function convertCustomer($nhtb39f008e318efd2bb988d724a161b61c6909677f){
        if(\LitExtension\CartImport\Model\Custom::CUSTOMER_CONVERT){
            return $this->_custom->convertCustomerCustom($this, $nhtb39f008e318efd2bb988d724a161b61c6909677f);
        }
        $nht191c6154b0ffa6b8f6543a62df338e3a58acb446 = array();
        if($this->_notice['config']['add_option']['pre_cus']){
            $nht191c6154b0ffa6b8f6543a62df338e3a58acb446['id'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f['customerid'];
        }
        $nht191c6154b0ffa6b8f6543a62df338e3a58acb446['website_id'] = $this->_notice['config']['website_id'];
        $nht191c6154b0ffa6b8f6543a62df338e3a58acb446['email'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f['emailaddress'];
        $nht191c6154b0ffa6b8f6543a62df338e3a58acb446['firstname'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f['firstname'] ? $nhtb39f008e318efd2bb988d724a161b61c6909677f['firstname'] : " ";
        $nht191c6154b0ffa6b8f6543a62df338e3a58acb446['lastname'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f['lastname'] ? $nhtb39f008e318efd2bb988d724a161b61c6909677f['lastname'] : " ";
        $nht191c6154b0ffa6b8f6543a62df338e3a58acb446['created_at'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f['lastmodified'] ? date('Y-m-d H:i:s', strtotime($nhtb39f008e318efd2bb988d724a161b61c6909677f['lastmodified'])) : null;
        $nht191c6154b0ffa6b8f6543a62df338e3a58acb446['group_id'] = 1;
        $nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c = $this->_custom->convertCustomerCustom($this, $nhtb39f008e318efd2bb988d724a161b61c6909677f);
        if($nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c){
            $nht191c6154b0ffa6b8f6543a62df338e3a58acb446 = array_merge($nht191c6154b0ffa6b8f6543a62df338e3a58acb446, $nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c);
        }
        return array(
            'result' => 'success',
            'data' => $nht191c6154b0ffa6b8f6543a62df338e3a58acb446
        );
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
        if(parent::afterSaveCustomer($nht99f4d75970929dd23fc2d2793107a65bb8b95b68, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtb39f008e318efd2bb988d724a161b61c6909677f)){
            return ;
        }
        if($nhtb39f008e318efd2bb988d724a161b61c6909677f['emailsubscriber'] ==  'Y'){
            $this->_objectManager->create('Magento\Newsletter\Model\Subscriber')->subscribeCustomerById($nht99f4d75970929dd23fc2d2793107a65bb8b95b68);
        }
        $nhtc662180230cad14787d4ab7e77aa08681ce783fa = array();
        $nhtc662180230cad14787d4ab7e77aa08681ce783fa['firstname'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f['firstname'] ? $nhtb39f008e318efd2bb988d724a161b61c6909677f['firstname'] : " ";
        $nhtc662180230cad14787d4ab7e77aa08681ce783fa['lastname'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f['lastname'] ? $nhtb39f008e318efd2bb988d724a161b61c6909677f['lastname'] : " ";
        $nhtcc58a4546cd5bf47ed7ffd8876a1c6e0fcc8b9ae = str_replace(' ','',$nhtb39f008e318efd2bb988d724a161b61c6909677f['country']);
        $nhtd1245d2d6e6243a644ca8affa2ecf2776c4f72ee = array(
            'AF' => "Afghanistan",
            'AX' => "Aland Islands",
            'AL' => "Albania",
            'DZ' => "Algeria",
            'AS' => "American Samoa",
            'AD' => "Andorra",
            'AO' => "Angola",
            'AI' => "Anguilla",
            'AQ' => "Antarctica",
            'AG' => "Antigua and Barbuda",
            'AR' => "Argentina",
            'AM' => "Armenia",
            'AW' => "Aruba",
            'AP' => "Asia/Pacific Region",
            'AU' => "Australia",
            'AT' => "Austria",
            'AZ' => "Azerbaijan",
            'BS' => "Bahamas",
            'BH' => "Bahrain",
            'BD' => "Bangladesh",
            'BB' => "Barbados",
            'BY' => "Belarus",
            'BE' => "Belgium",
            'BZ' => "Belize",
            'BJ' => "Benin",
            'BM' => "Bermuda",
            'BT' => "Bhutan",
            'BO' => "Bolivia",
            'BQ' => "Bonaire, Saint Eustatius and Saba",
            'BA' => "Bosnia and Herzegovina",
            'BW' => "Botswana",
            'BR' => "Brazil",
            'IO' => "British Indian Ocean Territory",
            'BN' => "Brunei Darussalam",
            'BG' => "Bulgaria",
            'BF' => "Burkina Faso",
            'BI' => "Burundi",
            'KH' => "Cambodia",
            'CM' => "Cameroon",
            'CA' => "Canada",
            'CV' => "Cape Verde",
            'KY' => "Cayman Islands",
            'CF' => "Central African Republic",
            'TD' => "Chad",
            'CL' => "Chile",
            'CN' => "China",
            'CX' => "Christmas Island",
            'CC' => "Cocos (Keeling) Islands",
            'CO' => "Colombia",
            'KM' => "Comoros",
            'CG' => "Congo",
            'CD' => "Congo, The Democratic Republic of the",
            'CK' => "Cook Islands",
            'CR' => "Costa Rica",
            'CI' => "Cote D'Ivoire",
            'HR' => "Croatia",
            'CU' => "Cuba",
            'CW' => "Curacao",
            'CY' => "Cyprus",
            'CZ' => "Czech Republic",
            'DK' => "Denmark",
            'DJ' => "Djibouti",
            'DM' => "Dominica",
            'DO' => "Dominican Republic",
            'EC' => "Ecuador",
            'EG' => "Egypt",
            'SV' => "El Salvador",
            'GQ' => "Equatorial Guinea",
            'ER' => "Eritrea",
            'EE' => "Estonia",
            'ET' => "Ethiopia",
            'EU' => "Europe",
            'FK' => "Falkland Islands (Malvinas)",
            'FO' => "Faroe Islands",
            'FJ' => "Fiji",
            'FI' => "Finland",
            'FR' => "France",
            'GF' => "French Guiana",
            'PF' => "French Polynesia",
            'TF' => "French Southern Territories",
            'GA' => "Gabon",
            'GM' => "Gambia",
            'GE' => "Georgia",
            'DE' => "Germany",
            'GH' => "Ghana",
            'GI' => "Gibraltar",
            'GR' => "Greece",
            'GL' => "Greenland",
            'GD' => "Grenada",
            'GP' => "Guadeloupe",
            'GU' => "Guam",
            'GT' => "Guatemala",
            'GG' => "Guernsey",
            'GN' => "Guinea",
            'GW' => "Guinea-Bissau",
            'GY' => "Guyana",
            'HT' => "Haiti",
            'VA' => "Holy See (Vatican City State)",
            'HN' => "Honduras",
            'HK' => "Hong Kong",
            'HU' => "Hungary",
            'IS' => "Iceland",
            'IN' => "India",
            'ID' => "Indonesia",
            'IR' => "Iran, Islamic Republic of",
            'IQ' => "Iraq",
            'IE' => "Ireland",
            'IM' => "Isle of Man",
            'IL' => "Israel",
            'IT' => "Italy",
            'JM' => "Jamaica",
            'JP' => "Japan",
            'JE' => "Jersey",
            'JO' => "Jordan",
            'KZ' => "Kazakhstan",
            'KE' => "Kenya",
            'KI' => "Kiribati",
            'KP' => "Korea, Democratic People's Republic of",
            'KR' => "Korea, Republic of",
            'KW' => "Kuwait",
            'KG' => "Kyrgyzstan",
            'LA' => "Lao People's Democratic Republic",
            'LV' => "Latvia",
            'LB' => "Lebanon",
            'LS' => "Lesotho",
            'LR' => "Liberia",
            'LY' => "Libya",
            'LI' => "Liechtenstein",
            'LT' => "Lithuania",
            'LU' => "Luxembourg",
            'MO' => "Macau",
            'MK' => "Macedonia",
            'MG' => "Madagascar",
            'MW' => "Malawi",
            'MY' => "Malaysia",
            'MV' => "Maldives",
            'ML' => "Mali",
            'MT' => "Malta",
            'MH' => "Marshall Islands",
            'MQ' => "Martinique",
            'MR' => "Mauritania",
            'MU' => "Mauritius",
            'YT' => "Mayotte",
            'MX' => "Mexico",
            'FM' => "Micronesia, Federated States of",
            'MD' => "Moldova, Republic of",
            'MC' => "Monaco",
            'MN' => "Mongolia",
            'ME' => "Montenegro",
            'MS' => "Montserrat",
            'MA' => "Morocco",
            'MZ' => "Mozambique",
            'MM' => "Myanmar",
            'NA' => "Namibia",
            'NR' => "Nauru",
            'NP' => "Nepal",
            'NL' => "Netherlands",
            'NC' => "New Caledonia",
            'NZ' => "New Zealand",
            'NI' => "Nicaragua",
            'NE' => "Niger",
            'NG' => "Nigeria",
            'NU' => "Niue",
            'NF' => "Norfolk Island",
            'MP' => "Northern Mariana Islands",
            'NO' => "Norway",
            'OM' => "Oman",
            'PK' => "Pakistan",
            'PW' => "Palau",
            'PS' => "Palestinian Territory",
            'PA' => "Panama",
            'PG' => "Papua New Guinea",
            'PY' => "Paraguay",
            'PE' => "Peru",
            'PH' => "Philippines",
            'PN' => "Pitcairn Islands",
            'PL' => "Poland",
            'PT' => "Portugal",
            'PR' => "Puerto Rico",
            'QA' => "Qatar",
            'RE' => "Reunion",
            'RO' => "Romania",
            'RU' => "Russian Federation",
            'RW' => "Rwanda",
            'BL' => "Saint Barthelemy",
            'SH' => "Saint Helena",
            'KN' => "Saint Kitts and Nevis",
            'LC' => "Saint Lucia",
            'MF' => "Saint Martin",
            'PM' => "Saint Pierre and Miquelon",
            'VC' => "Saint Vincent and the Grenadines",
            'WS' => "Samoa",
            'SM' => "San Marino",
            'ST' => "Sao Tome and Principe",
            'SA' => "Saudi Arabia",
            'SN' => "Senegal",
            'RS' => "Serbia",
            'SC' => "Seychelles",
            'SL' => "Sierra Leone",
            'SG' => "Singapore",
            'SX' => "Sint Maarten (Dutch part)",
            'SK' => "Slovakia",
            'SI' => "Slovenia",
            'SB' => "Solomon Islands",
            'SO' => "Somalia",
            'ZA' => "South Africa",
            'GS' => "South Georgia and the South Sandwich Islands",
            'SS' => "South Sudan",
            'ES' => "Spain",
            'LK' => "Sri Lanka",
            'SD' => "Sudan",
            'SR' => "Suriname",
            'SJ' => "Svalbard and Jan Mayen",
            'SZ' => "Swaziland",
            'SE' => "Sweden",
            'CH' => "Switzerland",
            'SY' => "Syrian Arab Republic",
            'TW' => "Taiwan",
            'TJ' => "Tajikistan",
            'TZ' => "Tanzania, United Republic of",
            'TH' => "Thailand",
            'TL' => "Timor-Leste",
            'TG' => "Togo",
            'TK' => "Tokelau",
            'TO' => "Tonga",
            'TT' => "Trinidad and Tobago",
            'TN' => "Tunisia",
            'TR' => "Turkey",
            'TM' => "Turkmenistan",
            'TC' => "Turks and Caicos Islands",
            'TV' => "Tuvalu",
            'UG' => "Uganda",
            'UA' => "Ukraine",
            'AE' => "United Arab Emirates",
            'GB' => "United Kingdom",
            'US' => "United States",
            'UM' => "United States Minor Outlying Islands",
            'UY' => "Uruguay",
            'UZ' => "Uzbekistan",
            'VU' => "Vanuatu",
            'VE' => "Venezuela",
            'VN' => "Vietnam",
            'VG' => "Virgin Islands, British",
            'VI' => "Virgin Islands, U.S.",
            'WF' => "Wallis and Futuna",
            'YE' => "Yemen",
            'ZM' => "Zambia",
            'ZW' => "Zimbabwe"
        );
        $nhtf3d97c9121f76a1aec3032116be91ac9648ed7b2 = 'US';
        foreach ($nhtd1245d2d6e6243a644ca8affa2ecf2776c4f72ee AS $nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e => $nht8e68b3e5af636475363c23b52ade8e6064b05806){
            $nhtcaf9d8de2eedc6e0ed29db981e2196525fe8acef = str_replace(' ','',$nht8e68b3e5af636475363c23b52ade8e6064b05806);
            if (strtolower($nhtcc58a4546cd5bf47ed7ffd8876a1c6e0fcc8b9ae) == strtolower($nhtcaf9d8de2eedc6e0ed29db981e2196525fe8acef)){
                $nhtf3d97c9121f76a1aec3032116be91ac9648ed7b2 = $nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e;
            }
        }
        $nhtc662180230cad14787d4ab7e77aa08681ce783fa['country_id'] = $nhtf3d97c9121f76a1aec3032116be91ac9648ed7b2;
        $nhtc662180230cad14787d4ab7e77aa08681ce783fa['street'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f['billingaddress1'] . "\n" . $nhtb39f008e318efd2bb988d724a161b61c6909677f['billingaddress2'];
        $nhtc662180230cad14787d4ab7e77aa08681ce783fa['postcode'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f['postalcode'];
        $nhtc662180230cad14787d4ab7e77aa08681ce783fa['city'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f['city'];
        $nhtc662180230cad14787d4ab7e77aa08681ce783fa['telephone'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f['phonenumber'];
        $nhtc662180230cad14787d4ab7e77aa08681ce783fa['company'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f['companyname'];
        $nhtc662180230cad14787d4ab7e77aa08681ce783fa['fax'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f['faxnumber'];
        if($nhtb39f008e318efd2bb988d724a161b61c6909677f['state']){
            $nht5f48ffc3af96bc486f5f3f3a6da77a69faee9fb9 = false;
            if(strlen($nhtb39f008e318efd2bb988d724a161b61c6909677f['state']) == 2){
                $nhta94a58406ef24e931ffe5c077c69b8bd8e3ac282= $this->_objectManager->create('Magento\Directory\Model\Region')->loadByCode($nhtb39f008e318efd2bb988d724a161b61c6909677f['state'], $nhtf3d97c9121f76a1aec3032116be91ac9648ed7b2);
                if($nhta94a58406ef24e931ffe5c077c69b8bd8e3ac282->getId()){
                    $nht5f48ffc3af96bc486f5f3f3a6da77a69faee9fb9 = $nhta94a58406ef24e931ffe5c077c69b8bd8e3ac282->getId();
                }
            } else {
                $nht5f48ffc3af96bc486f5f3f3a6da77a69faee9fb9 = $this->getRegionId($nhtb39f008e318efd2bb988d724a161b61c6909677f['state'], $nhtf3d97c9121f76a1aec3032116be91ac9648ed7b2);
            }
            if($nht5f48ffc3af96bc486f5f3f3a6da77a69faee9fb9){
                $nhtc662180230cad14787d4ab7e77aa08681ce783fa['region_id'] = $nht5f48ffc3af96bc486f5f3f3a6da77a69faee9fb9;
            }
            $nhtc662180230cad14787d4ab7e77aa08681ce783fa['region'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f['state'];
        } else {
            $nhtc662180230cad14787d4ab7e77aa08681ce783fa['region_id'] = 0;
        }
        $nht7826c62255e219b9f833f24f608da3c5131fa531 = $this->_process->address($nhtc662180230cad14787d4ab7e77aa08681ce783fa, $nht99f4d75970929dd23fc2d2793107a65bb8b95b68);
        if($nht7826c62255e219b9f833f24f608da3c5131fa531['result'] == 'success'){
            try{
                $nht0c12e642ca5b7ed4436e5f23f568ae10066608d3 = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($nht99f4d75970929dd23fc2d2793107a65bb8b95b68);
                $nht0c12e642ca5b7ed4436e5f23f568ae10066608d3->setDefaultBilling($nht7826c62255e219b9f833f24f608da3c5131fa531['mage_id']);
                $nht0c12e642ca5b7ed4436e5f23f568ae10066608d3->setDefaultShipping($nht7826c62255e219b9f833f24f608da3c5131fa531['mage_id']);
            }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){}
        }
    }

    /**
     * Get data use for import order
     */
    public function getOrders(){
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->_notice['orders']['id_src'];
        $nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5 = $this->_notice['setting']['orders'];
        $nht666ad8244a44d4bf1bb7d709ab0fac9dbe3bb75a = $this->getTableName(self::VLS_ORD);
        $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72 = "SELECT * FROM {$nht666ad8244a44d4bf1bb7d709ab0fac9dbe3bb75a} WHERE `domain` = '{$this->_cart_url}' AND orderid > {$nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595} ORDER BY orderid ASC LIMIT {$nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5}";
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->readQuery($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72);
        if($nht37a5301a88da334dc5afc5b63979daa0f3f45e68['result'] != 'success'){
            return $this->errorDatabase(true);
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Get primary key of source order main
     *
     * @param array $nhtcce55e4309a753985bdd21919395fdc17daa11e4 : One row of object in function getOrders
     * @return int
     */
    public function getOrderId($nhtcce55e4309a753985bdd21919395fdc17daa11e4){
        return $nhtcce55e4309a753985bdd21919395fdc17daa11e4['orderid'];
    }

    /**
     * Convert source data to data import
     *
     * @param array $nhtcce55e4309a753985bdd21919395fdc17daa11e4 : One row of object in function getOrders
     * @return array
     */
    public function convertOrder($nhtcce55e4309a753985bdd21919395fdc17daa11e4){
        if(\LitExtension\CartImport\Model\Custom::ORDER_CONVERT){
            return $this->_custom->convertOrderCustom($this, $nhtcce55e4309a753985bdd21919395fdc17daa11e4);
        }
        $nht71b30e888c006662a760dad3db609257c595a915 = $this->getTableName(self::VLS_CUS);
        $nht70ef1c723d608f264a166e7e356fc4ee0a03bcb7 = $this->readQuery("SELECT * FROM {$nht71b30e888c006662a760dad3db609257c595a915} WHERE `domain` = '{$this->_cart_url}' AND customerid = {$nhtcce55e4309a753985bdd21919395fdc17daa11e4['customerid']} ");
        if($nht70ef1c723d608f264a166e7e356fc4ee0a03bcb7['result'] != 'success' || empty($nht70ef1c723d608f264a166e7e356fc4ee0a03bcb7['data'])){
            return array(
                'result' => 'warning',
                'msg' => $this->consoleWarning("Order id = {$nhtcce55e4309a753985bdd21919395fdc17daa11e4['orderid']} import failed. Error: Customer not import!")
            );
        }
        $nht2e46d3880ca2c295290b1ea6b1dbe262ff14438a = $this->getTableName(self::VLS_ORD_DTL);
        $nhtf500a9ec51d6cd514688735088354692f051ece6 = $this->readQuery("SELECT * FROM {$nht2e46d3880ca2c295290b1ea6b1dbe262ff14438a} WHERE `domain` = '{$this->_cart_url}' AND orderid = {$nhtcce55e4309a753985bdd21919395fdc17daa11e4['orderid']}");
        $nht602b221b6c8ec577a4110064d6893d00def7e051 = ($nhtf500a9ec51d6cd514688735088354692f051ece6['result'] == 'success') ? $nhtf500a9ec51d6cd514688735088354692f051ece6['data'] : array();
        $nhtb39f008e318efd2bb988d724a161b61c6909677f = $nht70ef1c723d608f264a166e7e356fc4ee0a03bcb7['data'][0];

        $nhtd1245d2d6e6243a644ca8affa2ecf2776c4f72ee = array(
            'AF' => "Afghanistan",
            'AX' => "Aland Islands",
            'AL' => "Albania",
            'DZ' => "Algeria",
            'AS' => "American Samoa",
            'AD' => "Andorra",
            'AO' => "Angola",
            'AI' => "Anguilla",
            'AQ' => "Antarctica",
            'AG' => "Antigua and Barbuda",
            'AR' => "Argentina",
            'AM' => "Armenia",
            'AW' => "Aruba",
            'AP' => "Asia/Pacific Region",
            'AU' => "Australia",
            'AT' => "Austria",
            'AZ' => "Azerbaijan",
            'BS' => "Bahamas",
            'BH' => "Bahrain",
            'BD' => "Bangladesh",
            'BB' => "Barbados",
            'BY' => "Belarus",
            'BE' => "Belgium",
            'BZ' => "Belize",
            'BJ' => "Benin",
            'BM' => "Bermuda",
            'BT' => "Bhutan",
            'BO' => "Bolivia",
            'BQ' => "Bonaire, Saint Eustatius and Saba",
            'BA' => "Bosnia and Herzegovina",
            'BW' => "Botswana",
            'BR' => "Brazil",
            'IO' => "British Indian Ocean Territory",
            'BN' => "Brunei Darussalam",
            'BG' => "Bulgaria",
            'BF' => "Burkina Faso",
            'BI' => "Burundi",
            'KH' => "Cambodia",
            'CM' => "Cameroon",
            'CA' => "Canada",
            'CV' => "Cape Verde",
            'KY' => "Cayman Islands",
            'CF' => "Central African Republic",
            'TD' => "Chad",
            'CL' => "Chile",
            'CN' => "China",
            'CX' => "Christmas Island",
            'CC' => "Cocos (Keeling) Islands",
            'CO' => "Colombia",
            'KM' => "Comoros",
            'CG' => "Congo",
            'CD' => "Congo, The Democratic Republic of the",
            'CK' => "Cook Islands",
            'CR' => "Costa Rica",
            'CI' => "Cote D'Ivoire",
            'HR' => "Croatia",
            'CU' => "Cuba",
            'CW' => "Curacao",
            'CY' => "Cyprus",
            'CZ' => "Czech Republic",
            'DK' => "Denmark",
            'DJ' => "Djibouti",
            'DM' => "Dominica",
            'DO' => "Dominican Republic",
            'EC' => "Ecuador",
            'EG' => "Egypt",
            'SV' => "El Salvador",
            'GQ' => "Equatorial Guinea",
            'ER' => "Eritrea",
            'EE' => "Estonia",
            'ET' => "Ethiopia",
            'EU' => "Europe",
            'FK' => "Falkland Islands (Malvinas)",
            'FO' => "Faroe Islands",
            'FJ' => "Fiji",
            'FI' => "Finland",
            'FR' => "France",
            'GF' => "French Guiana",
            'PF' => "French Polynesia",
            'TF' => "French Southern Territories",
            'GA' => "Gabon",
            'GM' => "Gambia",
            'GE' => "Georgia",
            'DE' => "Germany",
            'GH' => "Ghana",
            'GI' => "Gibraltar",
            'GR' => "Greece",
            'GL' => "Greenland",
            'GD' => "Grenada",
            'GP' => "Guadeloupe",
            'GU' => "Guam",
            'GT' => "Guatemala",
            'GG' => "Guernsey",
            'GN' => "Guinea",
            'GW' => "Guinea-Bissau",
            'GY' => "Guyana",
            'HT' => "Haiti",
            'VA' => "Holy See (Vatican City State)",
            'HN' => "Honduras",
            'HK' => "Hong Kong",
            'HU' => "Hungary",
            'IS' => "Iceland",
            'IN' => "India",
            'ID' => "Indonesia",
            'IR' => "Iran, Islamic Republic of",
            'IQ' => "Iraq",
            'IE' => "Ireland",
            'IM' => "Isle of Man",
            'IL' => "Israel",
            'IT' => "Italy",
            'JM' => "Jamaica",
            'JP' => "Japan",
            'JE' => "Jersey",
            'JO' => "Jordan",
            'KZ' => "Kazakhstan",
            'KE' => "Kenya",
            'KI' => "Kiribati",
            'KP' => "Korea, Democratic People's Republic of",
            'KR' => "Korea, Republic of",
            'KW' => "Kuwait",
            'KG' => "Kyrgyzstan",
            'LA' => "Lao People's Democratic Republic",
            'LV' => "Latvia",
            'LB' => "Lebanon",
            'LS' => "Lesotho",
            'LR' => "Liberia",
            'LY' => "Libya",
            'LI' => "Liechtenstein",
            'LT' => "Lithuania",
            'LU' => "Luxembourg",
            'MO' => "Macau",
            'MK' => "Macedonia",
            'MG' => "Madagascar",
            'MW' => "Malawi",
            'MY' => "Malaysia",
            'MV' => "Maldives",
            'ML' => "Mali",
            'MT' => "Malta",
            'MH' => "Marshall Islands",
            'MQ' => "Martinique",
            'MR' => "Mauritania",
            'MU' => "Mauritius",
            'YT' => "Mayotte",
            'MX' => "Mexico",
            'FM' => "Micronesia, Federated States of",
            'MD' => "Moldova, Republic of",
            'MC' => "Monaco",
            'MN' => "Mongolia",
            'ME' => "Montenegro",
            'MS' => "Montserrat",
            'MA' => "Morocco",
            'MZ' => "Mozambique",
            'MM' => "Myanmar",
            'NA' => "Namibia",
            'NR' => "Nauru",
            'NP' => "Nepal",
            'NL' => "Netherlands",
            'NC' => "New Caledonia",
            'NZ' => "New Zealand",
            'NI' => "Nicaragua",
            'NE' => "Niger",
            'NG' => "Nigeria",
            'NU' => "Niue",
            'NF' => "Norfolk Island",
            'MP' => "Northern Mariana Islands",
            'NO' => "Norway",
            'OM' => "Oman",
            'PK' => "Pakistan",
            'PW' => "Palau",
            'PS' => "Palestinian Territory",
            'PA' => "Panama",
            'PG' => "Papua New Guinea",
            'PY' => "Paraguay",
            'PE' => "Peru",
            'PH' => "Philippines",
            'PN' => "Pitcairn Islands",
            'PL' => "Poland",
            'PT' => "Portugal",
            'PR' => "Puerto Rico",
            'QA' => "Qatar",
            'RE' => "Reunion",
            'RO' => "Romania",
            'RU' => "Russian Federation",
            'RW' => "Rwanda",
            'BL' => "Saint Barthelemy",
            'SH' => "Saint Helena",
            'KN' => "Saint Kitts and Nevis",
            'LC' => "Saint Lucia",
            'MF' => "Saint Martin",
            'PM' => "Saint Pierre and Miquelon",
            'VC' => "Saint Vincent and the Grenadines",
            'WS' => "Samoa",
            'SM' => "San Marino",
            'ST' => "Sao Tome and Principe",
            'SA' => "Saudi Arabia",
            'SN' => "Senegal",
            'RS' => "Serbia",
            'SC' => "Seychelles",
            'SL' => "Sierra Leone",
            'SG' => "Singapore",
            'SX' => "Sint Maarten (Dutch part)",
            'SK' => "Slovakia",
            'SI' => "Slovenia",
            'SB' => "Solomon Islands",
            'SO' => "Somalia",
            'ZA' => "South Africa",
            'GS' => "South Georgia and the South Sandwich Islands",
            'SS' => "South Sudan",
            'ES' => "Spain",
            'LK' => "Sri Lanka",
            'SD' => "Sudan",
            'SR' => "Suriname",
            'SJ' => "Svalbard and Jan Mayen",
            'SZ' => "Swaziland",
            'SE' => "Sweden",
            'CH' => "Switzerland",
            'SY' => "Syrian Arab Republic",
            'TW' => "Taiwan",
            'TJ' => "Tajikistan",
            'TZ' => "Tanzania, United Republic of",
            'TH' => "Thailand",
            'TL' => "Timor-Leste",
            'TG' => "Togo",
            'TK' => "Tokelau",
            'TO' => "Tonga",
            'TT' => "Trinidad and Tobago",
            'TN' => "Tunisia",
            'TR' => "Turkey",
            'TM' => "Turkmenistan",
            'TC' => "Turks and Caicos Islands",
            'TV' => "Tuvalu",
            'UG' => "Uganda",
            'UA' => "Ukraine",
            'AE' => "United Arab Emirates",
            'GB' => "United Kingdom",
            'US' => "United States",
            'UM' => "United States Minor Outlying Islands",
            'UY' => "Uruguay",
            'UZ' => "Uzbekistan",
            'VU' => "Vanuatu",
            'VE' => "Venezuela",
            'VN' => "Vietnam",
            'VG' => "Virgin Islands, British",
            'VI' => "Virgin Islands, U.S.",
            'WF' => "Wallis and Futuna",
            'YE' => "Yemen",
            'ZM' => "Zambia",
            'ZW' => "Zimbabwe"
        );
        $nht648027dfebc99fab591afb23f01742258bfdfda2 = str_replace(' ','',$nhtcce55e4309a753985bdd21919395fdc17daa11e4['billingcountry']);
        $nht03c1b70608c60e21e690f3a76f27221b03adb555 = str_replace(' ','',$nhtcce55e4309a753985bdd21919395fdc17daa11e4['shipcountry']);
        $nht5b0405040f03a6c3375b052dece696fed33af8a2 = $nhtc2194f26b295cf355dfc49a0d53f1927effc242f = 'US';
        foreach ($nhtd1245d2d6e6243a644ca8affa2ecf2776c4f72ee AS $nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e => $nht8e68b3e5af636475363c23b52ade8e6064b05806){
            $nhtcaf9d8de2eedc6e0ed29db981e2196525fe8acef = str_replace(' ','',$nht8e68b3e5af636475363c23b52ade8e6064b05806);
            if (strtolower($nht648027dfebc99fab591afb23f01742258bfdfda2) == strtolower($nhtcaf9d8de2eedc6e0ed29db981e2196525fe8acef)){
                $nht5b0405040f03a6c3375b052dece696fed33af8a2 = $nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e;
            }
            if (strtolower($nht03c1b70608c60e21e690f3a76f27221b03adb555) == strtolower($nhtcaf9d8de2eedc6e0ed29db981e2196525fe8acef)){
                $nhtc2194f26b295cf355dfc49a0d53f1927effc242f = $nhte6fb06210fafc02fd7479ddbed2d042cc3a5155e;
            }
        }

        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = $nht5ae5e326f5697bbc776ecd24a81074b5646d7723 = $nht6be600a29d57d37cac4d5fb17b67b7668677171a = $nht17994471a9a7a6fdf0818b65dae3008512bde344 = array();

        $nht5ae5e326f5697bbc776ecd24a81074b5646d7723['firstname'] = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['billingfirstname'];
        $nht5ae5e326f5697bbc776ecd24a81074b5646d7723['lastname'] = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['billinglastname'];
        $nht5ae5e326f5697bbc776ecd24a81074b5646d7723['company'] = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['billingcompanyname'];
        $nht5ae5e326f5697bbc776ecd24a81074b5646d7723['email'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f['emailaddress'];
        $nht5ae5e326f5697bbc776ecd24a81074b5646d7723['country_id'] = $nht5b0405040f03a6c3375b052dece696fed33af8a2;
        $nht5ae5e326f5697bbc776ecd24a81074b5646d7723['street'] = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['billingaddress1'] . "\n" . $nhtcce55e4309a753985bdd21919395fdc17daa11e4['billingaddress2'];
        $nht5ae5e326f5697bbc776ecd24a81074b5646d7723['postcode'] = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['billingpostalcode'];
        $nht5ae5e326f5697bbc776ecd24a81074b5646d7723['city'] = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['billingcity'];
        $nht5ae5e326f5697bbc776ecd24a81074b5646d7723['telephone'] = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['billingphonenumber'];
        $nht5ae5e326f5697bbc776ecd24a81074b5646d7723['fax'] = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['billingfaxnumber'];
        if($nhtcce55e4309a753985bdd21919395fdc17daa11e4['billingstate']){
            $nht33e818261e7fdbed868e087e64e568d701099300 = false;
            if(strlen($nhtcce55e4309a753985bdd21919395fdc17daa11e4['billingstate']) == 2){
                $nhta94a58406ef24e931ffe5c077c69b8bd8e3ac282= $this->_objectManager->create('Magento\Directory\Model\Region')->loadByCode($nhtcce55e4309a753985bdd21919395fdc17daa11e4['billingstate'], $nht5b0405040f03a6c3375b052dece696fed33af8a2);
                if($nhta94a58406ef24e931ffe5c077c69b8bd8e3ac282->getId()){
                    $nht33e818261e7fdbed868e087e64e568d701099300 = $nhta94a58406ef24e931ffe5c077c69b8bd8e3ac282->getId();
                }
            } else {
                $nht33e818261e7fdbed868e087e64e568d701099300 = $this->getRegionId($nhtcce55e4309a753985bdd21919395fdc17daa11e4['billingstate'], $nht5b0405040f03a6c3375b052dece696fed33af8a2);
            }
            if($nht33e818261e7fdbed868e087e64e568d701099300){
                $nht5ae5e326f5697bbc776ecd24a81074b5646d7723['region_id'] = $nht33e818261e7fdbed868e087e64e568d701099300;
            }
            $nht5ae5e326f5697bbc776ecd24a81074b5646d7723['region'] = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['billingstate'];
        } else {
            $nht5ae5e326f5697bbc776ecd24a81074b5646d7723['region_id'] = 0;
        }

        $nht6be600a29d57d37cac4d5fb17b67b7668677171a['firstname'] = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['shipfirstname'];
        $nht6be600a29d57d37cac4d5fb17b67b7668677171a['lastname'] = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['shiplastname'];
        $nht6be600a29d57d37cac4d5fb17b67b7668677171a['company'] = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['shipcompanyname'];
        $nht6be600a29d57d37cac4d5fb17b67b7668677171a['email'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f['emailaddress'];
        $nht6be600a29d57d37cac4d5fb17b67b7668677171a['country_id'] = $nhtc2194f26b295cf355dfc49a0d53f1927effc242f;
        $nht6be600a29d57d37cac4d5fb17b67b7668677171a['street'] = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['shipaddress1'] . "\n" . $nhtcce55e4309a753985bdd21919395fdc17daa11e4['shipaddress2'];
        $nht6be600a29d57d37cac4d5fb17b67b7668677171a['postcode'] = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['shippostalcode'];
        $nht6be600a29d57d37cac4d5fb17b67b7668677171a['city'] = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['shipcity'];
        $nht6be600a29d57d37cac4d5fb17b67b7668677171a['telephone'] = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['shipphonenumber'];
        $nht6be600a29d57d37cac4d5fb17b67b7668677171a['fax'] = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['shipfaxnumber'];
        if($nhtcce55e4309a753985bdd21919395fdc17daa11e4['shipstate']){
            $nht951e1fffa62af7a5b91c32261936b22f11ba4778 = false;
            if(strlen($nhtcce55e4309a753985bdd21919395fdc17daa11e4['shipstate']) == 2){
                $nhta94a58406ef24e931ffe5c077c69b8bd8e3ac282= $this->_objectManager->create('Magento\Directory\Model\Region')->loadByCode($nhtcce55e4309a753985bdd21919395fdc17daa11e4['shipstate'], $nht5b0405040f03a6c3375b052dece696fed33af8a2);
                if($nhta94a58406ef24e931ffe5c077c69b8bd8e3ac282->getId()){
                    $nht951e1fffa62af7a5b91c32261936b22f11ba4778 = $nhta94a58406ef24e931ffe5c077c69b8bd8e3ac282->getId();
                }
            } else {
                $nht951e1fffa62af7a5b91c32261936b22f11ba4778 = $this->getRegionId($nhtcce55e4309a753985bdd21919395fdc17daa11e4['shipstate'], $nhtc2194f26b295cf355dfc49a0d53f1927effc242f);
            }
            if($nht951e1fffa62af7a5b91c32261936b22f11ba4778){
                $nht6be600a29d57d37cac4d5fb17b67b7668677171a['region_id'] = $nht951e1fffa62af7a5b91c32261936b22f11ba4778;
            }
            $nht6be600a29d57d37cac4d5fb17b67b7668677171a['region'] = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['shipstate'];
        } else {
            $nht6be600a29d57d37cac4d5fb17b67b7668677171a['region_id'] = 0;
        }
        $nhtc467ca0fa21477fee3cde75a140b2963307388a7 = array();
        if($nht602b221b6c8ec577a4110064d6893d00def7e051){
            foreach($nht602b221b6c8ec577a4110064d6893d00def7e051 as $nht0cbe8192412de31b38fbf99ef441aeac13c8157b){
                if($nht0cbe8192412de31b38fbf99ef441aeac13c8157b['discounttype']){
                    $nhtc467ca0fa21477fee3cde75a140b2963307388a7 = $nht0cbe8192412de31b38fbf99ef441aeac13c8157b;
                    continue ;
                }
                $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed = array();
                $nhtbebc9158e480b949565b4dc7a82d05cfd99935d5 = $this->_getLeCaIpImportIdDescByValue(self::TYPE_PRODUCT, $nht0cbe8192412de31b38fbf99ef441aeac13c8157b['productcode']);
                if($nhtbebc9158e480b949565b4dc7a82d05cfd99935d5){
                    $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed['product_id'] = $nhtbebc9158e480b949565b4dc7a82d05cfd99935d5;
                }
                $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed['type_id'] = \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE;
                $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed['name'] = $nht0cbe8192412de31b38fbf99ef441aeac13c8157b['productname'];
                $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed['sku'] = $nht0cbe8192412de31b38fbf99ef441aeac13c8157b['productcode'];
                $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed['price'] = $nht0cbe8192412de31b38fbf99ef441aeac13c8157b['productprice'];
                $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed['original_price'] = $nht0cbe8192412de31b38fbf99ef441aeac13c8157b['productprice'];
                $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed['qty_ordered'] = $nht0cbe8192412de31b38fbf99ef441aeac13c8157b['quantity'];
                $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed['row_total'] = $nht0cbe8192412de31b38fbf99ef441aeac13c8157b['totalprice'];
                if($nht0cbe8192412de31b38fbf99ef441aeac13c8157b['options']){
                    $nhteb44c1dd3fe99716d636a689d3f0eb43f6a7f713 = array();
                    $nht513f8de9259fe7658fe14d1352c54ccf070e911f = str_replace('][', ',', $nht0cbe8192412de31b38fbf99ef441aeac13c8157b['options']);
                    $nht513f8de9259fe7658fe14d1352c54ccf070e911f = explode(',', $nht513f8de9259fe7658fe14d1352c54ccf070e911f);
                    foreach($nht513f8de9259fe7658fe14d1352c54ccf070e911f as $nhta62f2225bf70bfaccbc7f1ef2a397836717377de => $nht14eb14ece52df99c284b819d9f8092e50aa1613e){
                        $nht14eb14ece52df99c284b819d9f8092e50aa1613e = str_replace('[', '', $nht14eb14ece52df99c284b819d9f8092e50aa1613e);
                        $nht14eb14ece52df99c284b819d9f8092e50aa1613e = str_replace(']', '', $nht14eb14ece52df99c284b819d9f8092e50aa1613e);
                        $nht959710895f12a754e80d877af38c8e06a95cee4d = explode(':', $nht14eb14ece52df99c284b819d9f8092e50aa1613e);
                        $nhtd3cadf11572c4d71b2e8a3221ec3e30ffd6169a0 = array(
                            'label' => isset($nht959710895f12a754e80d877af38c8e06a95cee4d[0])? $nht959710895f12a754e80d877af38c8e06a95cee4d[0] : " ",
                            'value' => isset($nht959710895f12a754e80d877af38c8e06a95cee4d[1])? $nht959710895f12a754e80d877af38c8e06a95cee4d[1] : " ",
                            'print_value' => isset($nht959710895f12a754e80d877af38c8e06a95cee4d[1])? $nht959710895f12a754e80d877af38c8e06a95cee4d[1] : " ",
                            'option_id' => 'option_' . $nhta62f2225bf70bfaccbc7f1ef2a397836717377de,
                            'option_type' => 'drop_down',
                            'option_value' => 0,
                            'custom_view' => false
                        );
                        $nhteb44c1dd3fe99716d636a689d3f0eb43f6a7f713[] = $nhtd3cadf11572c4d71b2e8a3221ec3e30ffd6169a0;
                    }
                    $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed['product_options'] = serialize(array('options' => $nhteb44c1dd3fe99716d636a689d3f0eb43f6a7f713));
                }
                $nht17994471a9a7a6fdf0818b65dae3008512bde344[]= $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed;
            }
        }

        $nhta7a13f4cacb744524e44dfdad329d540144d209d = $this->getIdDescCustomer($nhtb39f008e318efd2bb988d724a161b61c6909677f['customerid']);
        $nhtd7c956a3f3323509820305ecebf4cf0f4dc3f9ca = array(
            'New' => 'pending',
            'Pending' => 'pending',
            'Processing' => 'processing',
            'Payment Declined' => 'payment_review',
            'Awaiting Payment' => 'pending_payment',
            'Ready to Ship' => 'processing',
            'Pending Shipment' => 'processing',
            'Partially Shipped' => 'processing',
            'Shipped' => 'complete',
            'Partially Backordered' => 'processing',
            'Backordered' => 'processing',
            'See Line Items' => 'pending',
            'See Order Notes' => 'pending',
            'Partially Returned' => 'closed',
            'Returned' => 'closed',
            'Cancel Order' => 'canceled'
        );
        $nht8ea75b2a26f83f3bc98b9c6aaf68502b64d224ed = 'pending';
        foreach ($nhtd7c956a3f3323509820305ecebf4cf0f4dc3f9ca as $nht5f916ecbe052e0f679a7bec0f82e5253844edd79 => $nht43102558285417e856726b1047ff48ea3c89dfab){
            if ($nhtcce55e4309a753985bdd21919395fdc17daa11e4['orderstatus'] == $nht5f916ecbe052e0f679a7bec0f82e5253844edd79)
                $nht8ea75b2a26f83f3bc98b9c6aaf68502b64d224ed = $nht43102558285417e856726b1047ff48ea3c89dfab;
        }
        $nhtcaed12bd772843a2b638f2d9e55e10d9c30d4b1c = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['salestax1'] + $nhtcce55e4309a753985bdd21919395fdc17daa11e4['salestax2'] + $nhtcce55e4309a753985bdd21919395fdc17daa11e4['salestax3'];
        $nht89255319f612cc58b4a26e152b695a153b33bc17 = $nhtc467ca0fa21477fee3cde75a140b2963307388a7 ? abs($nhtc467ca0fa21477fee3cde75a140b2963307388a7['totalprice']) : 0;
        $nhtf3172007d4de5ae8e7692759d79f67f5558242ed = $this->_notice['config']['languages'][1];
        $nht1e35df79f1353d6c17dd45a3be388a72c2716330 = $this->getStoreCurrencyCode($nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
        $nht0b16b5b30d7d6c58d7ae4f4935db39d101d15279 = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['totalshippingcost'];
        $nht8bf4afe1282172fb057b865e325b0bf82876802a = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['paymentamount'] - $nhtcaed12bd772843a2b638f2d9e55e10d9c30d4b1c + $nht89255319f612cc58b4a26e152b695a153b33bc17 - $nht0b16b5b30d7d6c58d7ae4f4935db39d101d15279;

        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1 = array();
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['store_id'] = $nhtf3172007d4de5ae8e7692759d79f67f5558242ed;
        if($nhta7a13f4cacb744524e44dfdad329d540144d209d){
            $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['customer_id'] = $nhta7a13f4cacb744524e44dfdad329d540144d209d;
            $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['customer_is_guest'] = false;
        } else {
            $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['customer_is_guest'] = true;
        }
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['customer_email'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f['emailaddress'];
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['customer_firstname'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f['firstname'];
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['customer_lastname'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f['lastname'];
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['customer_group_id'] = 1;
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['status'] = $nht8ea75b2a26f83f3bc98b9c6aaf68502b64d224ed;
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['state'] = $this->getOrderStateByStatus($nht8ea75b2a26f83f3bc98b9c6aaf68502b64d224ed);
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['subtotal'] = $this->incrementPriceToImport($nht8bf4afe1282172fb057b865e325b0bf82876802a);
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['base_subtotal'] =  $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['subtotal'];
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['shipping_amount'] = $nht0b16b5b30d7d6c58d7ae4f4935db39d101d15279;
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['base_shipping_amount'] = $nht0b16b5b30d7d6c58d7ae4f4935db39d101d15279;
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['base_shipping_invoiced'] = $nht0b16b5b30d7d6c58d7ae4f4935db39d101d15279;
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['shipping_description'] = "Shipping";
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['tax_amount'] = $nhtcaed12bd772843a2b638f2d9e55e10d9c30d4b1c;
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['base_tax_amount'] = $nhtcaed12bd772843a2b638f2d9e55e10d9c30d4b1c;
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['discount_amount'] = $nht89255319f612cc58b4a26e152b695a153b33bc17;
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['base_discount_amount'] = $nht89255319f612cc58b4a26e152b695a153b33bc17;
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['grand_total'] = $this->incrementPriceToImport($nhtcce55e4309a753985bdd21919395fdc17daa11e4['paymentamount']);
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['base_grand_total'] = $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['grand_total'];
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['base_total_invoiced'] = $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['grand_total'];
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['total_paid'] = $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['grand_total'];
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['base_total_paid'] = $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['grand_total'];
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['base_to_global_rate'] = true;
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['base_to_order_rate'] = true;
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['store_to_base_rate'] = true;
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['store_to_order_rate'] = true;
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['base_currency_code'] = $nht1e35df79f1353d6c17dd45a3be388a72c2716330['base'];
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['global_currency_code'] = $nht1e35df79f1353d6c17dd45a3be388a72c2716330['base'];
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['store_currency_code'] = $nht1e35df79f1353d6c17dd45a3be388a72c2716330['base'];
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['order_currency_code'] = $nht1e35df79f1353d6c17dd45a3be388a72c2716330['base'];
        $nhtb7f2949fb20c979483f430a0c6682083a985ddf1['created_at'] = date('Y-m-d H:i:s', strtotime($nhtcce55e4309a753985bdd21919395fdc17daa11e4['orderdate']));

        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['address_billing'] = $nht5ae5e326f5697bbc776ecd24a81074b5646d7723;
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['address_shipping'] = $nht6be600a29d57d37cac4d5fb17b67b7668677171a;
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['order'] = $nhtb7f2949fb20c979483f430a0c6682083a985ddf1;
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['carts'] = $nht17994471a9a7a6fdf0818b65dae3008512bde344;
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['order_src_id'] = $nhtcce55e4309a753985bdd21919395fdc17daa11e4['orderid'];
        $nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c = $this->_custom->convertOrderCustom($this, $nhtcce55e4309a753985bdd21919395fdc17daa11e4);
        if($nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c){
            $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array_merge($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c);
        }
        return array(
            'result' => 'success',
            'data' => $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd
        );
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
        if(parent::afterSaveOrder($nht1d17f5cae78c16f2bac6fbb7fe9f6acece23fa8a, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtcce55e4309a753985bdd21919395fdc17daa11e4)){
            return ;
        }
        $nht2360856a680ace2200ca24ccb2bc445ea6c4d092 = array();
        $nhtd7c956a3f3323509820305ecebf4cf0f4dc3f9ca = array(
            'New' => 'pending',
            'Pending' => 'pending',
            'Processing' => 'processing',
            'Payment Declined' => 'payment_review',
            'Awaiting Payment' => 'pending_payment',
            'Ready to Ship' => 'processing',
            'Pending Shipment' => 'processing',
            'Partially Shipped' => 'processing',
            'Shipped' => 'complete',
            'Partially Backordered' => 'processing',
            'Backordered' => 'processing',
            'See Line Items' => 'pending',
            'See Order Notes' => 'pending',
            'Partially Returned' => 'closed',
            'Returned' => 'closed',
            'Cancel Order' => 'canceled'
        );
        $nht8ea75b2a26f83f3bc98b9c6aaf68502b64d224ed = 'pending';
        foreach ($nhtd7c956a3f3323509820305ecebf4cf0f4dc3f9ca as $nht5f916ecbe052e0f679a7bec0f82e5253844edd79 => $nht43102558285417e856726b1047ff48ea3c89dfab){
            if ($nhtcce55e4309a753985bdd21919395fdc17daa11e4['orderstatus'] == $nht5f916ecbe052e0f679a7bec0f82e5253844edd79)
                $nht8ea75b2a26f83f3bc98b9c6aaf68502b64d224ed = $nht43102558285417e856726b1047ff48ea3c89dfab;
        }
        $nht2360856a680ace2200ca24ccb2bc445ea6c4d092['status'] = $nht8ea75b2a26f83f3bc98b9c6aaf68502b64d224ed;
        $nht2360856a680ace2200ca24ccb2bc445ea6c4d092['state'] = $this->getOrderStateByStatus($nht8ea75b2a26f83f3bc98b9c6aaf68502b64d224ed);
        $nht2360856a680ace2200ca24ccb2bc445ea6c4d092['comment'] = "<b>Reference order #".$nhtcce55e4309a753985bdd21919395fdc17daa11e4['orderid']."</b><br />";
        $nht2360856a680ace2200ca24ccb2bc445ea6c4d092['comment'] .= "<b>Payment method Id: </b>".$nhtcce55e4309a753985bdd21919395fdc17daa11e4['paymentmethodid']."<br />";
        $nht2360856a680ace2200ca24ccb2bc445ea6c4d092['comment'] .= "<b>Shipping method Id: </b> ".$nhtcce55e4309a753985bdd21919395fdc17daa11e4['shippingmethodid']."<br />";
        $nht2360856a680ace2200ca24ccb2bc445ea6c4d092['comment'] .= "<b>Order Notes: </b>".$nhtcce55e4309a753985bdd21919395fdc17daa11e4['order_comments'];
        $nht2360856a680ace2200ca24ccb2bc445ea6c4d092['is_customer_notified'] = 1;
        $nht2360856a680ace2200ca24ccb2bc445ea6c4d092['updated_at'] = date('Y-m-d H:i:s', strtotime($nhtcce55e4309a753985bdd21919395fdc17daa11e4['orderdate']));
        $nht2360856a680ace2200ca24ccb2bc445ea6c4d092['created_at'] = date('Y-m-d H:i:s', strtotime($nhtcce55e4309a753985bdd21919395fdc17daa11e4['orderdate']));
        $this->_process->ordersComment($nht1d17f5cae78c16f2bac6fbb7fe9f6acece23fa8a, $nht2360856a680ace2200ca24ccb2bc445ea6c4d092);
    }

    /**
     * Get main data use for import review
     */
    public function getReviews(){
        $nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595 = $this->_notice['reviews']['id_src'];
        $nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5 = $this->_notice['setting']['reviews'];
        $nht7c90be8f189695182434f0c6168eb8cab7448c51 = $this->getTableName(self::VLS_REV);
        $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72 = "SELECT * FROM {$nht7c90be8f189695182434f0c6168eb8cab7448c51} WHERE `domain` = '{$this->_cart_url}' AND reviewid > {$nht3517fcc8fec5023f7acb1a96d8cc7642bc4e6595} ORDER BY reviewid ASC LIMIT {$nhte4d68c5a97e466323c2fbe2b655ab578066a1cd5}";
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->readQuery($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72);
        if($nht37a5301a88da334dc5afc5b63979daa0f3f45e68['result'] != 'success'){
            return $this->errorDatabase(true);
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    /**
     * Get primary key of source review main
     *
     * @param array $nht61e62b213a1a56f7695845df4fc372a10cb0a73e : One row of object in function getReviews
     * @return int
     */
    public function getReviewId($nht61e62b213a1a56f7695845df4fc372a10cb0a73e){
        return $nht61e62b213a1a56f7695845df4fc372a10cb0a73e['reviewid'];
    }

    /**
     * Convert source data to data import
     *
     * @param array $nht61e62b213a1a56f7695845df4fc372a10cb0a73e : One row of object in function getReviews
     * @return array
     */
    public function convertReview($nht61e62b213a1a56f7695845df4fc372a10cb0a73e){
        if(\LitExtension\CartImport\Model\Custom::REVIEW_CONVERT){
            return $this->_custom->convertReviewCustom($this, $nht61e62b213a1a56f7695845df4fc372a10cb0a73e);
        }
        $nhtbebc9158e480b949565b4dc7a82d05cfd99935d5 = $this->_getLeCaIpImportIdDescByValue(self::TYPE_PRODUCT, strtolower($nht61e62b213a1a56f7695845df4fc372a10cb0a73e['productcode']));
        if(!$nhtbebc9158e480b949565b4dc7a82d05cfd99935d5){
            return array(
                'result' => 'warning',
                'msg' => $this->consoleWarning("Review Id = {$nht61e62b213a1a56f7695845df4fc372a10cb0a73e['reviewid']} import failed. Error: Product code = {$nht61e62b213a1a56f7695845df4fc372a10cb0a73e['productcode']} not imported!")
            );
        }
        $nht45feb9dc9334e33bcc8b240dbc4d9cec09e4519c = $nht61e62b213a1a56f7695845df4fc372a10cb0a73e['name'];
        if(!$nht45feb9dc9334e33bcc8b240dbc4d9cec09e4519c && $nht61e62b213a1a56f7695845df4fc372a10cb0a73e['customerid'] && $this->_notice['config']['import']['customers']){
            $nhtb39f008e318efd2bb988d724a161b61c6909677f = $this->selectTableRow(self::VLS_CUS, array(
                'domain' => $this->_cart_url,
                'customerid' => $nht61e62b213a1a56f7695845df4fc372a10cb0a73e['customerid']
            ));
            if($nhtb39f008e318efd2bb988d724a161b61c6909677f){
                $nht45feb9dc9334e33bcc8b240dbc4d9cec09e4519c = $nhtb39f008e318efd2bb988d724a161b61c6909677f['firstname'] . " " . $nhtb39f008e318efd2bb988d724a161b61c6909677f['lastname'];
            }
        }
        if(!$nht45feb9dc9334e33bcc8b240dbc4d9cec09e4519c){
            $nht45feb9dc9334e33bcc8b240dbc4d9cec09e4519c = ' ';
        }
        $nhta7a13f4cacb744524e44dfdad329d540144d209d = $this->getIdDescCustomer($nht61e62b213a1a56f7695845df4fc372a10cb0a73e['customerid']);
        $nhtf3172007d4de5ae8e7692759d79f67f5558242ed = $this->_notice['config']['languages'][1];
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array();
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['entity_pk_value'] = $nhtbebc9158e480b949565b4dc7a82d05cfd99935d5;
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['status_id'] = ($nht61e62b213a1a56f7695845df4fc372a10cb0a73e['active'] == 'Y')? 1 : 3;
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['title'] = $nht61e62b213a1a56f7695845df4fc372a10cb0a73e['reviewtitle'];
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['detail'] = $nht61e62b213a1a56f7695845df4fc372a10cb0a73e['reviewdescription'];
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['entity_id'] = 1;
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['stores'] = array($nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['customer_id'] = $nhta7a13f4cacb744524e44dfdad329d540144d209d ? $nhta7a13f4cacb744524e44dfdad329d540144d209d : null;
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['nickname'] = $nht45feb9dc9334e33bcc8b240dbc4d9cec09e4519c;
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['rating'] = $nht61e62b213a1a56f7695845df4fc372a10cb0a73e['rate'];
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['created_at'] = date('Y-m-d H:i:s', strtotime($nht61e62b213a1a56f7695845df4fc372a10cb0a73e['lastmodified']));
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['review_id_import'] = $nht61e62b213a1a56f7695845df4fc372a10cb0a73e['reviewid'];
        $nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c = $this->_custom->convertOrderCustom($this, $nht61e62b213a1a56f7695845df4fc372a10cb0a73e);
        if($nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c){
            $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array_merge($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c);
        }
        return array(
            'result' => 'success',
            'data' => $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd
        );
    }

    /**
     * TODO : Extend function
     */

    /**
     * Setup table for import
     */
    protected function _setupStorageCsv(){
        $nht145e2bd794fb7f250a3dbdafb78b9b9bad27cadb = $this->_custom->storageCsvCustom($this);
        if($nht145e2bd794fb7f250a3dbdafb78b9b9bad27cadb && $nht145e2bd794fb7f250a3dbdafb78b9b9bad27cadb['result'] == 'error'){
            return $nht145e2bd794fb7f250a3dbdafb78b9b9bad27cadb;
        }
        $nht06d9b5fd55301ce64d6adb95fb7a60cb8f4dcac9 = $this->_scopeConfig->getValue('leci/setup/volusion');
        if($nht06d9b5fd55301ce64d6adb95fb7a60cb8f4dcac9 < self::VLS_VERSION){
            $nht80437a44a661d141174209119d54125a59a64b2a = true;
            $nht3a9942a786a65473f8bb2713479585205dc2fb6e = $this->getListTableDrop();
            foreach($nht3a9942a786a65473f8bb2713479585205dc2fb6e as $nhta28183809fde04d2c8cdcadec6b12b48136e7311){
                $this->dropTable($nhta28183809fde04d2c8cdcadec6b12b48136e7311);
            }
            $nht3915396b5fe58dce8505b3e62c31b4f79b3ccc2c = $nhtb9bc9eb9599dc8cac9a8f99884fcfb4ed933a35f = array();
            $nht22f0a27d79bfa68c634156931dd1b20dff82ee4b = array(
                'exchangeRatesTableConstruct',
                'taxesTableConstruct',
                'categoriesTableConstruct',
                'productsTableConstruct',
                'optionCategoriesTableConstruct',
                'optionsTableConstruct',
                'kitsTableConstruct',
                'kitLinksTableConstruct',
                'customersTableConstruct',
                'ordersTableConstruct',
                'orderDetailsTableConstruct',
                'reviewsTableConstruct'
            );
            foreach($nht22f0a27d79bfa68c634156931dd1b20dff82ee4b as $nht9b7c68a918b17eb053809b198d7c9abfc142f30a){
                $nht3915396b5fe58dce8505b3e62c31b4f79b3ccc2c[] = $this->$nht9b7c68a918b17eb053809b198d7c9abfc142f30a();
            }
            foreach($nht3915396b5fe58dce8505b3e62c31b4f79b3ccc2c as $nhtc3ee137d4f22eb06ed1351d644f3674592c90836){
                $nht83fc3c44c68e8cf687e9cf104dd544c22930b923 = $this->arrayToCreateSql($nhtc3ee137d4f22eb06ed1351d644f3674592c90836);
                if($nht83fc3c44c68e8cf687e9cf104dd544c22930b923['result'] != 'success'){
                    $nht83fc3c44c68e8cf687e9cf104dd544c22930b923['msg'] = $this->consoleError($nht83fc3c44c68e8cf687e9cf104dd544c22930b923['msg']);
                    return $nht83fc3c44c68e8cf687e9cf104dd544c22930b923;
                }
                $nhtb9bc9eb9599dc8cac9a8f99884fcfb4ed933a35f[] = $nht83fc3c44c68e8cf687e9cf104dd544c22930b923['query'];
            }
            foreach($nhtb9bc9eb9599dc8cac9a8f99884fcfb4ed933a35f as $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72){
                if(!$this->writeQuery($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72)){
                    $nht80437a44a661d141174209119d54125a59a64b2a = false;
                }
            }
            if($nht80437a44a661d141174209119d54125a59a64b2a){
                $this->_objectManager->create('Magento\Config\Model\Config')->setDataByPath('leci/setup/volusion', self::VLS_VERSION);
            } else {
                return array(
                    'result' => 'error',
                    'msg' => $this->consoleError("Could not created table to storage data.")
                );
            }
        }
        $this->_notice['csv_import']['result'] = 'process';
        $this->_notice['csv_import']['function'] = '_clearStorageCsv';
        $this->_notice['csv_import']['msg'] = "";
        return $this->_notice['csv_import'];
    }

    public function getListTableDrop(){
        $nht3915396b5fe58dce8505b3e62c31b4f79b3ccc2c = $this->_getTablesTmp();
        $nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c = $this->_custom->getListTableDropCustom($nht3915396b5fe58dce8505b3e62c31b4f79b3ccc2c);
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c ? $nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c : $nht3915396b5fe58dce8505b3e62c31b4f79b3ccc2c;
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }
    /**
     * Construct of table currency
     */
    public function exchangeRatesTableConstruct(){
        return array(
            'table' => self::VLS_CUR,
            'rows' => array(
                'domain' => 'VARCHAR(255)',
                'er_id' => 'BIGINT',
                'currency' => 'VARCHAR(255)',
                'symbol' => 'VARCHAR(255)',
                'exchangerate' => 'VARCHAR(255)',
                'isdefault' => 'VARCHAR(5)',
                'lastmodified' => 'VARCHAR(255)',
                'paypal_currencycode' => 'VARCHAR(255)'
            ),
            'validation' => array('er_id', 'currency')
        );
    }

    /**
     * Construct of table taxes
     */
    public function taxesTableConstruct(){
        return array(
            'table' => self::VLS_TAX,
            'rows' => array(
                'domain' => 'VARCHAR(255)',
                'taxid' => 'BIGINT',
                'taxstateshort' => 'VARCHAR(255)',
                'taxstatelong' => 'VARCHAR(255)',
                'taxcountry' => 'VARCHAR(255)',
                'tax1_title' => 'TEXT',
                'tax2_title' => 'TEXT',
                'tax3_title' => 'TEXT',
                'tax1_percent' => 'TEXT',
                'tax2_percent' => 'TEXT',
                'tax3_percent' => 'TEXT',
            ),
            'validation' => array('taxid')
        );
    }

    /**
     * Construct of table categories
     */
    public function categoriesTableConstruct(){
        return array(
            'table' => self::VLS_CAT,
            'rows' => array(
                'domain' => 'VARCHAR(255)',
                'categoryid' => 'BIGINT',
                'parentid' => 'BIGINT',
                'categoryname' => 'TEXT',
                'categoryorder' => 'VARCHAR(255)',
                'categoryvisible' => 'VARCHAR(255)',
                'metatag_title' => 'TEXT',
                'metatag_description' => 'TEXT',
                'link_title_tag' => 'TEXT',
                'categorydescriptionshort' => 'TEXT',
                'categorydescription' => 'TEXT',
                'metatag_keywords' => 'TEXT',
                'hidden' => 'VARCHAR(5)'
            ),
            'validation' => array('categoryid', 'categoryname')
        );
    }

    /**
     * Construct of table products
     */
    public function productsTableConstruct(){
        return array(
            'table' => self::VLS_PRO,
            'rows' => array(
                'id' => 'BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY',
                'domain' => 'VARCHAR(255)',
                'productcode' => 'VARCHAR(255)',
                'productname' => 'TEXT',
                'hideproduct' => 'VARCHAR(5)',
                'stockstatus' => 'VARCHAR(255)',
                'lastmodified' => 'VARCHAR(255)',
                'ischildofproductcode' => 'VARCHAR(255)',
                'productnameshort' => 'VARCHAR(255)',
                'productweight' => 'VARCHAR(255)',
                'recurringprice' => 'VARCHAR(255)',
                'productprice' => 'VARCHAR(255)',
                'listprice' => 'VARCHAR(255)',
                'saleprice' => 'VARCHAR(255)',
                'metatag_title' => 'TEXT',
                'metatag_description' => 'TEXT',
                'photo_subtext' => 'TEXT',
                'photo_alttext' => 'TEXT',
                'setupcost' => 'VARCHAR(255)',
                'productdescriptionshort' => 'TEXT',
                'productdescription' => 'TEXT',
                'metatag_keywords' => 'TEXT',
                'categoryids' => 'TEXT',
                'optionids' => 'TEXT',
                'photourl' => 'TEXT',
                'photourl_large' => 'TEXT',
                'donotallowbackorders' => 'VARCHAR(10)'
            ),
            'validation' => array('productcode')
        );
    }

    /**
     * Construct of table option categories
     */
    public function optionCategoriesTableConstruct(){
        return array(
            'table' => self::VLS_OPT_CAT,
            'rows' => array(
                'domain' => 'VARCHAR(255)',
                'id' => 'BIGINT',
                'headinggroup' => 'VARCHAR(255)',
                'optioncategoriesdesc' => 'VARCHAR(255)',
                'displaytype' => 'VARCHAR(255)',
            ),
            'validation' => array('id')
        );
    }

    /**
     * Construct of table options
     */
    public function optionsTableConstruct(){
        return array(
            'table' => self::VLS_OPT,
            'rows' => array(
                'domain' => 'VARCHAR(255)',
                'id' => 'BIGINT',
                'optioncatid' => 'BIGINT',
                'optionsdesc' => 'VARCHAR(255)',
                'pricediff' => 'VARCHAR(255)'
            ),
            'validation' => array('id', 'optioncatid')
        );
    }

    /**
     * Construct of table kits
     */
    public function kitsTableConstruct(){
        return array(
            'table' => self::VLS_KIT,
            'rows' => array(
                'domain' => 'VARCHAR(255)',
                'kit_id' => 'BIGINT',
                'kit_type' => 'VARCHAR(255)',
                'kit_productcode' => 'VARCHAR(255)',
                'kit_isproductcode' => 'VARCHAR(255)',
                'kit_qty' => 'VARCHAR(255)',
                'lastmodified' => 'VARCHAR(255)',
                'lastmodby' => 'VARCHAR(255)',
                'kit_orderby' => 'VARCHAR(255)'
            ),
            'validation' => array('kit_id', 'kit_type', 'kit_productcode')
        );
    }

    /**
     * Construct of table kits lnk
     */
    public function kitLinksTableConstruct(){
        return array(
            'table' => self::VLS_KIT_LNK,
            'rows' => array(
                'domain' => 'VARCHAR(255)',
                'kitlnk_id' => 'BIGINT',
                'kit_id' => 'BIGINT',
                'kitlnk_productcode' => 'VARCHAR(255)',
                'kitlnk_optionid' => 'BIGINT',
                'kitlnk_qty' => 'VARCHAR(255)',
                'kitlnk_pricediff' => 'VARCHAR(255)'
            ),
            'validation' => array('kitlnk_id', 'kit_id', 'kitlnk_optionid')
        );
    }

    /**
     * Construct of table customer
     */
    public function customersTableConstruct(){
        return array(
            'table' => self::VLS_CUS,
            'rows' => array(
                'domain' => 'VARCHAR(255)',
                'customerid' => 'BIGINT',
                'accesskey' => 'VARCHAR(5)',
                'firstname' => 'VARCHAR(255)',
                'lastname' => 'VARCHAR(255)',
                'companyname' => 'VARCHAR(255)',
                'billingaddress1' => 'VARCHAR(255)',
                'billingaddress2' => 'VARCHAR(255)',
                'city' => 'VARCHAR(255)',
                'state' => 'VARCHAR(255)',
                'postalcode' => 'VARCHAR(255)',
                'country' => 'VARCHAR(255)',
                'phonenumber' => 'VARCHAR(255)',
                'faxnumber' => 'VARCHAR(255)',
                'emailaddress' => 'VARCHAR(255)',
                'emailsubscriber' => 'VARCHAR(5)',
                'lastmodified' => 'VARCHAR(255)'
            ),
            'validation' => array('customerid')
        );
    }

    /**
     * Construct of table order
     */
    public function ordersTableConstruct(){
        return array(
            'table' => self::VLS_ORD,
            'rows' => array(
                'domain' => 'VARCHAR(255)',
                'orderid' => 'BIGINT',
                'customerid' => 'BIGINT',
                'billingcompanyname' => 'VARCHAR(255)',
                'billingfirstname' => 'VARCHAR(255)',
                'billinglastname' => 'VARCHAR(255)',
                'billingaddress1' => 'VARCHAR(255)',
                'billingaddress2' => 'VARCHAR(255)',
                'billingcity' => 'VARCHAR(255)',
                'billingstate' => 'VARCHAR(255)',
                'billingpostalcode' => 'VARCHAR(255)',
                'billingcountry' => 'VARCHAR(255)',
                'billingphonenumber' => 'VARCHAR(255)',
                'billingfaxnumber' => 'VARCHAR(255)',
                'shipcompanyname' => 'VARCHAR(255)',
                'shipfirstname' => 'VARCHAR(255)',
                'shiplastname' => 'VARCHAR(255)',
                'shipaddress1' => 'VARCHAR(255)',
                'shipaddress2' => 'VARCHAR(255)',
                'shipcity' => 'VARCHAR(255)',
                'shipstate' => 'VARCHAR(255)',
                'shippostalcode' => 'VARCHAR(255)',
                'shipcountry' => 'VARCHAR(255)',
                'shipphonenumber' => 'VARCHAR(255)',
                'shipfaxnumber' => 'VARCHAR(255)',
                'shippingmethodid' => 'VARCHAR(255)',
                'totalshippingcost' => 'VARCHAR(255)',
                'salestaxrate' => 'VARCHAR(255)',
                'paymentamount' => 'VARCHAR(255)',
                'paymentmethodid' => 'VARCHAR(255)',
                'cardholdersname' => 'VARCHAR(255)',
                'creditcardexpdate' => 'VARCHAR(255)',
                'creditcardauthorizationnumber' => 'VARCHAR(255)',
                'creditcardtransactionid' => 'VARCHAR(255)',
                'bankname' => 'VARCHAR(255)',
                'orderdate' => 'VARCHAR(255)',
                'orderstatus' => 'VARCHAR(255)',
                'total_payment_received' => 'VARCHAR(255)',
                'total_payment_authorized' => 'VARCHAR(255)',
                'salestax1' => 'VARCHAR(255)',
                'salestax2' => 'VARCHAR(255)',
                'salestax3' => 'VARCHAR(255)',
                'ordernotes' => 'TEXT',
                'order_comments' => 'TEXT',
                'orderdateutc' => 'VARCHAR(255)'
            ),
            'validation' => array('orderid')
        );
    }

    /**
     * Construct of table order details
     */
    public function orderDetailsTableConstruct(){
        return array(
            'table' => self::VLS_ORD_DTL,
            'rows' => array(
                'domain' => 'VARCHAR(255)',
                'orderdetailid' => 'BIGINT',
                'orderid' => 'BIGINT',
                'productcode' => 'VARCHAR(255)',
                'productname' => 'VARCHAR(255)',
                'quantity' => 'VARCHAR(255)',
                'productprice' => 'VARCHAR(255)',
                'totalprice' => 'VARCHAR(255)',
                'optionids' => 'VARCHAR(255)',
                'options' => 'TEXT',
                'discounttype' => 'VARCHAR(255)',
                'discountvalue' => 'VARCHAR(255)'
            ),
            'validation' => array('orderdetailid', 'orderid', 'productcode')
        );
    }

    /**
     * Construct of table review
     */
    public function reviewsTableConstruct(){
        return array(
            'table' => self::VLS_REV,
            'rows' => array(
                'domain' => 'VARCHAR(255)',
                'reviewid' => 'BIGINT',
                'lastmodified' => 'VARCHAR(255)',
                'productcode' => 'VARCHAR(255)',
                'reviewtitle' => 'VARCHAR(255)',
                'rate' => 'INT(5)',
                'customerid' => 'BIGINT',
                'name' => 'VARCHAR(255)',
                'active' => 'VARCHAR(5)',
                'reviewdescription' => 'TEXT'
            ),
            'validation' => array('reviewid', 'productcode')
        );
    }

    /**
     * Clear data if exit in database
     */
    protected function _clearStorageCsv(){
        $nht3915396b5fe58dce8505b3e62c31b4f79b3ccc2c = $this->_getTablesTmp();
        foreach($nht3915396b5fe58dce8505b3e62c31b4f79b3ccc2c as $nhtc3ee137d4f22eb06ed1351d644f3674592c90836){
            $nht2cb0b78846e0a65d35a4cd02d3cb9710abda4912 = $this->getTableName($nhtc3ee137d4f22eb06ed1351d644f3674592c90836);
            $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72 = "DELETE FROM {$nht2cb0b78846e0a65d35a4cd02d3cb9710abda4912} WHERE `domain` = '{$this->_cart_url}'";
            $this->writeQuery($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72);
        }
        $this->_notice['csv_import']['function'] = '_storageCsvExchangeRates';
        return array(
            'result' => 'process',
            'msg' => ''
        );
    }

    /**
     * Storage Csv ExchangeRates to database
     */
    protected function _storageCsvExchangeRates(){
        return $this->_storageCsvByType('exchangeRates', 'taxes');
    }

    /**
     * Storage Csv taxes to database
     */
    protected function _storageCsvTaxes(){
        return $this->_storageCsvByType('taxes', 'categories');
    }

    /**
     * Storage Csv categories to database
     */
    protected function _storageCsvCategories(){
        return $this->_storageCsvByType('categories', 'products');
    }

    /**
     * Storage Csv products to database
     */
    protected function _storageCsvProducts(){
        return $this->_storageCsvByType('products', 'optionCategories', false, false, array('id'));
    }

    /**
     * Storage Csv option categories to database
     */
    protected function _storageCsvOptionCategories(){
        return $this->_storageCsvByType('optionCategories', 'customers', 'options');
    }

    /**
     * Storage Csv options to database
     */
    protected function _storageCsvOptions(){
        return $this->_storageCsvByType('options', 'kits');
    }

    /**
     * Storage Csv kits to database
     */
    protected function _storageCsvKits(){
        return $this->_storageCsvByType('kits', 'kitLinks');
    }

    /**
     * Storage Csv kits lnk to database
     */
    protected function _storageCsvKitLinks(){
        return $this->_storageCsvByType('kitLinks', 'customers');
    }

    /**
     * Storage Csv customers to database
     */
    protected function _storageCsvCustomers(){
        return $this->_storageCsvByType('customers', 'orders');
    }

    /**
     * Storage Csv orders to database
     */
    protected function _storageCsvOrders(){
        return $this->_storageCsvByType('orders', 'orderDetails');
    }

    /**
     * Storage Csv order details to database
     */
    protected function _storageCsvOrderDetails(){
        return $this->_storageCsvByType('orderDetails', 'reviews');
    }

    /**
     * Storage Csv reviews to database
     */
    protected function _storageCsvReviews(){
        return $this->_storageCsvByType('reviews', 'reviews', false, true);
    }

    /**
     * Import parent category if not exists by id
     */
    protected function _importCategoryParent($nht9c4e4a89e3674fc9f382d733f03d24746bdc9d9a){
        $nht393503680458155c94de811a593a7ecab7d4bfa3 = $this->getTableName(self::VLS_CAT);
        $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72 = "SELECT * FROM {$nht393503680458155c94de811a593a7ecab7d4bfa3} WHERE `domain` = '{$this->_cart_url}' AND categoryid = {$nht9c4e4a89e3674fc9f382d733f03d24746bdc9d9a}";
        $nht50b9e78177f37e3c747f67abcc8af36a44f218f5 = $this->readQuery($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72);
        if($nht50b9e78177f37e3c747f67abcc8af36a44f218f5['result'] != 'success'){
            return $this->errorDatabase(true);
        }
        $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165 = isset($nht50b9e78177f37e3c747f67abcc8af36a44f218f5['data'][0]) ? $nht50b9e78177f37e3c747f67abcc8af36a44f218f5['data'][0] : false;
        if(!$nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165){
            return array(
                'result' => 'warning',
            );
        }
        $nht20a70aaf7e25faabeb80d477937f0a1a2d3ba60d = $this->convertCategory($nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165);
        if($nht20a70aaf7e25faabeb80d477937f0a1a2d3ba60d['result'] != 'success'){
            return array(
                'result' => 'warning',
            );
        }
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = $nht20a70aaf7e25faabeb80d477937f0a1a2d3ba60d['data'];
        $nhtb9f469bcd5ef46ebe22ec35325441b19766ebd18 = $this->_process->category($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        if($nhtb9f469bcd5ef46ebe22ec35325441b19766ebd18['result'] == 'success'){
            $this->categorySuccess($nht9c4e4a89e3674fc9f382d733f03d24746bdc9d9a, $nhtb9f469bcd5ef46ebe22ec35325441b19766ebd18['mage_id']);
            $this->afterSaveCategory($nhtb9f469bcd5ef46ebe22ec35325441b19766ebd18['mage_id'], $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165);
        } else {
            $nhtb9f469bcd5ef46ebe22ec35325441b19766ebd18['result'] = 'warning';
        }
        return $nhtb9f469bcd5ef46ebe22ec35325441b19766ebd18;
    }

    /**
     * Check product has children product in product table
     */
    protected function _checkProductHasChild($nht38a007151abe87cc01a5b6e9cc418e85286e2087){
        $nht3d08b35c6f931dc3dbed3e6101c63b0f9f119470 = $this->getTableName(self::VLS_PRO);
        $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72 = "SELECT * FROM {$nht3d08b35c6f931dc3dbed3e6101c63b0f9f119470} WHERE `domain` = '{$this->_cart_url}' AND ischildofproductcode = '{$nht38a007151abe87cc01a5b6e9cc418e85286e2087['productcode']}'";
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->readQuery($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72);
        if($nht37a5301a88da334dc5afc5b63979daa0f3f45e68['result'] != 'success'){
            return false;
        }
        if(!empty($nht37a5301a88da334dc5afc5b63979daa0f3f45e68['data'])){
            return true;
        }
        return false;
    }

    /**
     * Convert data of src cart to magento
     */
    protected function _convertProduct($nht38a007151abe87cc01a5b6e9cc418e85286e2087){
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb = $nht186a48d7f09e97e64ee789c15a3cc850dc3bef68 = array();
        if($nht38a007151abe87cc01a5b6e9cc418e85286e2087['categoryids']){
            $nhtf1cd78704413f8eaaaf660e19f1baced53f34a3e = explode(',', $nht38a007151abe87cc01a5b6e9cc418e85286e2087['categoryids']);
            foreach($nhtf1cd78704413f8eaaaf660e19f1baced53f34a3e as $nht9c4e4a89e3674fc9f382d733f03d24746bdc9d9a){
                $nht8229619cee40a6b2a2a6aa16af2cee1b867e46d3 = $this->getIdDescCategory($nht9c4e4a89e3674fc9f382d733f03d24746bdc9d9a);
                if($nht8229619cee40a6b2a2a6aa16af2cee1b867e46d3){
                    $nht186a48d7f09e97e64ee789c15a3cc850dc3bef68[] = $nht8229619cee40a6b2a2a6aa16af2cee1b867e46d3;
                }
            }
        }
        $nhtf3172007d4de5ae8e7692759d79f67f5558242ed = $this->_notice['config']['languages'][1];
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['website_ids'] = $this->_notice['extend']['website_ids'];
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['store_ids'] = array_values($this->_notice['config']['languages']);
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['attribute_set_id'] = $this->_notice['config']['attribute_set_id'];
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['category_ids'] = $nht186a48d7f09e97e64ee789c15a3cc850dc3bef68;
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['sku'] = $this->createProductSku($nht38a007151abe87cc01a5b6e9cc418e85286e2087['productcode'], $this->_notice['config']['languages']);
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['url_key'] = $this->generateProductUrlKey($nht38a007151abe87cc01a5b6e9cc418e85286e2087['productname'], $nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['name'] = $nht38a007151abe87cc01a5b6e9cc418e85286e2087['productname'];
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['description'] = $this->changeImgSrcInText($nht38a007151abe87cc01a5b6e9cc418e85286e2087['productdescription'], $this->_notice['config']['add_option']['img_des']);
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['short_description'] = $this->changeImgSrcInText($nht38a007151abe87cc01a5b6e9cc418e85286e2087['productdescriptionshort'], $this->_notice['config']['add_option']['img_des']);
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['meta_title'] = $nht38a007151abe87cc01a5b6e9cc418e85286e2087['metatag_title'];
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['meta_keyword'] = $nht38a007151abe87cc01a5b6e9cc418e85286e2087['metatag_keywords'];
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['meta_description'] = $nht38a007151abe87cc01a5b6e9cc418e85286e2087['metatag_description'];
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['weight'] = $nht38a007151abe87cc01a5b6e9cc418e85286e2087['productweight'];
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['status'] = 1;
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['price'] = $nht38a007151abe87cc01a5b6e9cc418e85286e2087['productprice'] ? $nht38a007151abe87cc01a5b6e9cc418e85286e2087['productprice'] : 0;
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['tax_class_id'] = 0;
        if($nht38a007151abe87cc01a5b6e9cc418e85286e2087['saleprice']){
            $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['special_price'] = $nht38a007151abe87cc01a5b6e9cc418e85286e2087['saleprice'];
        }
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['create_at'] = date('Y-m-d H:i:s', strtotime($nht38a007151abe87cc01a5b6e9cc418e85286e2087['lastmodified']));
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['visibility'] = ($nht38a007151abe87cc01a5b6e9cc418e85286e2087['hideproduct'] == 'Y') ? \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE : \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH;
        $nhte70be6d283c22cd1a86a7d202736057fb1e9272c = true;
        if($nht38a007151abe87cc01a5b6e9cc418e85286e2087['stockstatus'] === null || $nht38a007151abe87cc01a5b6e9cc418e85286e2087['stockstatus'] == ''){
            $nhte70be6d283c22cd1a86a7d202736057fb1e9272c = false;
        }
        if($this->_notice['config']['add_option']['stock'] && $nht38a007151abe87cc01a5b6e9cc418e85286e2087['stockstatus'] < 1){
            $nhte70be6d283c22cd1a86a7d202736057fb1e9272c = false;
        }
        $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['stock_data'] = array(
            'is_in_stock' => 1,
            'manage_stock' => $nhte70be6d283c22cd1a86a7d202736057fb1e9272c,
            'use_config_manage_stock' => $nhte70be6d283c22cd1a86a7d202736057fb1e9272c,
            'qty' => ($nht38a007151abe87cc01a5b6e9cc418e85286e2087['stockstatus'])? $nht38a007151abe87cc01a5b6e9cc418e85286e2087['stockstatus'] : 0,
            'backorders' => ($nht38a007151abe87cc01a5b6e9cc418e85286e2087['donotallowbackorders'] == 'N') ? 0 : 1
        );
        if($nht38a007151abe87cc01a5b6e9cc418e85286e2087['listprice']){
            $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['msrp'] = $nht38a007151abe87cc01a5b6e9cc418e85286e2087['listprice'];
        }
        $nht978ea7af39ad5fefb6bfbc82a1c5023494bdade2 = (strpos($nht38a007151abe87cc01a5b6e9cc418e85286e2087['photourl'], 'nophoto.gif') === false && $nht38a007151abe87cc01a5b6e9cc418e85286e2087['photourl_large']) ? $nht38a007151abe87cc01a5b6e9cc418e85286e2087['photourl_large'] : $nht38a007151abe87cc01a5b6e9cc418e85286e2087['photourl'];
        if($nht978ea7af39ad5fefb6bfbc82a1c5023494bdade2){
            $nht978ea7af39ad5fefb6bfbc82a1c5023494bdade2 = strtolower($nht978ea7af39ad5fefb6bfbc82a1c5023494bdade2);
            $nhte846ae14e90957cf296de806adc688ca046555c9 = $this->convertUrlToDownload($nht978ea7af39ad5fefb6bfbc82a1c5023494bdade2, $this->_cart_url);
            if($nhte846ae14e90957cf296de806adc688ca046555c9){
                $nht05a2c508d76d1d77d7490b06b59e238d4799fe6c = $this->downloadImage($nhte846ae14e90957cf296de806adc688ca046555c9['domain'], $nhte846ae14e90957cf296de806adc688ca046555c9['path'], 'catalog/product', false, true);
                if($nht05a2c508d76d1d77d7490b06b59e238d4799fe6c){
                    $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['image_import_path'] = array('path' => $nht05a2c508d76d1d77d7490b06b59e238d4799fe6c, 'label' => $nht38a007151abe87cc01a5b6e9cc418e85286e2087['photo_alttext']);
                }
            }
        }
        if($this->_seo){
            $nht6170ca2b023edf54ada0f81d18c7c2b3d6db9553 = $this->_seo->convertProductSeo($this, $nht38a007151abe87cc01a5b6e9cc418e85286e2087);
            if($nht6170ca2b023edf54ada0f81d18c7c2b3d6db9553){
                $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb['seo_url'] = $nht6170ca2b023edf54ada0f81d18c7c2b3d6db9553;
            }
        }
        $nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c = $this->_custom->convertProductCustom($this, $nht38a007151abe87cc01a5b6e9cc418e85286e2087);
        if($nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c){
            $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb = array_merge($nht8a4988b5f3da81038f6461b70095e5144ba1c4fb, $nhtf9ac14b63a75faf57d8db6f919bfabb2502d273c);
        }
        return array(
            'result' => 'success',
            'data' => $nht8a4988b5f3da81038f6461b70095e5144ba1c4fb
        );
    }

    /**
     * Import and create data for configurable product
     */
    protected function _importChildrenProduct($nhtd8fd39d0bbdd2dcf322d8b11390a4c5825b11495){
        $nht3d08b35c6f931dc3dbed3e6101c63b0f9f119470 = $this->getTableName(self::VLS_PRO);
        $nht4a6fc50d86c7b032bbb82959d6af1b818a2074e2 = $this->getTableName(self::VLS_KIT);
        $nht0d5d7d9b91e666af445e1053b1d605f264a944d3 = $this->getTableName(self::VLS_KIT_LNK);
        $nht4f890bda0d4a47e44cf41881bdf22f818aef284b = $this->getTableName(self::VLS_OPT);
        $nht0c859cbe19ba3d91676c36a908a4a07c9482fc3a = $this->getTableName(self::VLS_OPT_CAT);
        $nhtf48dd12495f8e868db407f6a360e3da8621ba601 = "SELECT * FROM {$nht3d08b35c6f931dc3dbed3e6101c63b0f9f119470} WHERE `domain` = '{$this->_cart_url}' AND ischildofproductcode = '{$nhtd8fd39d0bbdd2dcf322d8b11390a4c5825b11495['productcode']}'";
        $nhte453f7198069d9647540acb31a89c9a383c3152f = $this->readQuery($nhtf48dd12495f8e868db407f6a360e3da8621ba601);
        if($nhte453f7198069d9647540acb31a89c9a383c3152f['result'] != 'success'){
            return $this->errorDatabase(true);
        }
        $nht473f9aed6eba8a52376851f8bd44eec39abfb251 = "SELECT * FROM {$nht4a6fc50d86c7b032bbb82959d6af1b818a2074e2} WHERE `domain` = '{$this->_cart_url}' AND kit_productcode = '{$nhtd8fd39d0bbdd2dcf322d8b11390a4c5825b11495['productcode']}' ORDER BY kit_orderby ASC";
        $nhte10244ebfd0d14e6feb7d60a4fbe0b5aab4e1239 = $this->readQuery($nht473f9aed6eba8a52376851f8bd44eec39abfb251);
        if($nhte10244ebfd0d14e6feb7d60a4fbe0b5aab4e1239['result'] != 'success'){
            return $this->errorDatabase(true);
        }
        $nht618db4ed9d719a119dc18c193a6688b9ae1b4b15 = $this->duplicateFieldValueFromList($nhte10244ebfd0d14e6feb7d60a4fbe0b5aab4e1239['data'], 'kit_id');
        $nht7a4b11f409dd0835ce170101ab439ba7bd59ef69 = $this->arrayToInCondition($nht618db4ed9d719a119dc18c193a6688b9ae1b4b15);
        $nht379a751537bd6283582e97a4b2004b4130527660 = "SELECT * FROM {$nht0d5d7d9b91e666af445e1053b1d605f264a944d3} WHERE `domain` = '{$this->_cart_url}' AND kit_id IN {$nht7a4b11f409dd0835ce170101ab439ba7bd59ef69}";
        $nhtc14959708d1ba400138581334c1abd2fe8b4f746 = $this->readQuery($nht379a751537bd6283582e97a4b2004b4130527660);
        if($nhtc14959708d1ba400138581334c1abd2fe8b4f746['result'] != 'success'){
            return $this->errorDatabase(true);
        }
        $nht56a78ba90ef18e3f76ab83284e73db436d14e463 = $this->duplicateFieldValueFromList($nhtc14959708d1ba400138581334c1abd2fe8b4f746['data'], 'kitlnk_optionid');
        $nhtbcc73a55615c38a3cebd5b79ea4b6b4c32a17dfd = $this->arrayToInCondition($nht56a78ba90ef18e3f76ab83284e73db436d14e463);
        $nht2676bc5b16711df6aa10b48ceb61a60686d4fe4d = "SELECT * FROM {$nht4f890bda0d4a47e44cf41881bdf22f818aef284b} WHERE `domain` = '{$this->_cart_url}' AND id IN {$nhtbcc73a55615c38a3cebd5b79ea4b6b4c32a17dfd}";
        $nht513f8de9259fe7658fe14d1352c54ccf070e911f = $this->readQuery($nht2676bc5b16711df6aa10b48ceb61a60686d4fe4d);
        if($nht513f8de9259fe7658fe14d1352c54ccf070e911f['result'] != 'success'){
            return $this->errorDatabase(true);
        }
        $nht52a9e5d8fa1515060754e6d90cf5c6969ab99a09 = $this->duplicateFieldValueFromList($nht513f8de9259fe7658fe14d1352c54ccf070e911f['data'], 'optioncatid');
        $nht4899adbd80e198e6ade95b3ef337a5bccd9a600d = $this->arrayToInCondition($nht52a9e5d8fa1515060754e6d90cf5c6969ab99a09);
        $nht52432d7ff7d0d6a2c6733380a7379816eb1f3fd8 = "SELECT * FROM {$nht0c859cbe19ba3d91676c36a908a4a07c9482fc3a} WHERE `domain` = '{$this->_cart_url}' AND id IN {$nht4899adbd80e198e6ade95b3ef337a5bccd9a600d}";
        $nht49ba7cdad6f9a18274d6d2ce65b918a22c0f463f = $this->readQuery($nht52432d7ff7d0d6a2c6733380a7379816eb1f3fd8);
        if($nht49ba7cdad6f9a18274d6d2ce65b918a22c0f463f['result'] != 'success'){
            return $this->errorDatabase(true);
        }
        if(empty($nhte10244ebfd0d14e6feb7d60a4fbe0b5aab4e1239['data']) || empty($nhtc14959708d1ba400138581334c1abd2fe8b4f746['data']) || empty($nht513f8de9259fe7658fe14d1352c54ccf070e911f['data']) || empty($nht49ba7cdad6f9a18274d6d2ce65b918a22c0f463f['data'])){
            return array(
                'result' => 'success',
                'data' => array(
                    'type_id' => \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
                )
            );
        }
        $nht29a6717d2e239d90e8177f0c72a84c321f70a02e = $this->_importAttribute($nht513f8de9259fe7658fe14d1352c54ccf070e911f, $nht49ba7cdad6f9a18274d6d2ce65b918a22c0f463f);
        if($nht29a6717d2e239d90e8177f0c72a84c321f70a02e['result'] != 'success'){
            return array(
                'result' => "warning",
                'msg' => $this->consoleWarning("Product Code = {$nhtd8fd39d0bbdd2dcf322d8b11390a4c5825b11495['productcode']} import failed. Error: Product attribute could not be created!")
            );
        }
        if($nht29a6717d2e239d90e8177f0c72a84c321f70a02e['type'] == 'change'){
            return array(
                'result' => "success",
                'data' => array(
                    'type_id' => \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
                )
            );
        }
        $nht6907bf7268beeb1d8d7cfc54324e9f94900bff3d = $nht45d3d3e9b557ab737fb8a4f68776bd8325c94ae2 = array();
        $nhta673aa035fbda0a8f4b1bf81286ab8a2a8708ed7 = $this->_objectManager->create('Magento\Eav\Model\Entity')->setType(\Magento\Catalog\Model\Product::ENTITY)->getTypeId();
        $nhtff8660fa516a427b8e99bea52c9919f85d39a4c9 = array();
        foreach($nhte453f7198069d9647540acb31a89c9a383c3152f['data'] as $nht151f616254198f24ad96bd505eef007a65a36586){
            $nht8eef5efb9406d21b92265e6d5c926a7682309e49 = $this->getIdDescProduct($nht151f616254198f24ad96bd505eef007a65a36586['id']);
            if($nht8eef5efb9406d21b92265e6d5c926a7682309e49){
                $nhtff8660fa516a427b8e99bea52c9919f85d39a4c9[] = $nht8eef5efb9406d21b92265e6d5c926a7682309e49;
                // do nothing
            } else {
                $nht01a0945ef79174d17690d8342aaad735dc162a27 = array(
                    'type_id' => \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
                );
                $nht967b7fee8c8e37a84690c372bb18050818d734b3 = $this->_convertProduct($nht151f616254198f24ad96bd505eef007a65a36586);
                if($nht967b7fee8c8e37a84690c372bb18050818d734b3['result'] != 'success'){
                    return array(
                        'result' => "warning",
                        'msg' => $this->consoleWarning("Product Code = {$nhtd8fd39d0bbdd2dcf322d8b11390a4c5825b11495['productcode']} import failed. Error: Product children could not create!(Error code: Product children data not found.)")
                    );
                }
                $nht01a0945ef79174d17690d8342aaad735dc162a27 = array_merge($nht967b7fee8c8e37a84690c372bb18050818d734b3['data'], $nht01a0945ef79174d17690d8342aaad735dc162a27);
                $nht039f46832b14ade2cc01f7b31db030920beee550 = $this->_process->product($nht01a0945ef79174d17690d8342aaad735dc162a27);
                if($nht039f46832b14ade2cc01f7b31db030920beee550['result'] != 'success'){
                    return array(
                        'result' => "warning",
                        'msg' => $this->consoleWarning("Product Id = {$nhtd8fd39d0bbdd2dcf322d8b11390a4c5825b11495['productcode']} import failed. Error: Product children could not create!(Error code: " . $nht039f46832b14ade2cc01f7b31db030920beee550['msg'] . ". )")
                    );
                }
                $this->productSuccess($nht151f616254198f24ad96bd505eef007a65a36586['id'], $nht039f46832b14ade2cc01f7b31db030920beee550['mage_id'], $nht151f616254198f24ad96bd505eef007a65a36586['productcode']);
                $nht8eef5efb9406d21b92265e6d5c926a7682309e49 = $nht039f46832b14ade2cc01f7b31db030920beee550['mage_id'];
                $nhtff8660fa516a427b8e99bea52c9919f85d39a4c9[] = $nht8eef5efb9406d21b92265e6d5c926a7682309e49;
            }
            $nht2b268ceabe2df789699a1a8d11d58b9827a71973 = $this->getRowFromListByField($nhte10244ebfd0d14e6feb7d60a4fbe0b5aab4e1239['data'], 'kit_isproductcode', $nht151f616254198f24ad96bd505eef007a65a36586['productcode']);
            if(!$nht2b268ceabe2df789699a1a8d11d58b9827a71973){
                continue;
            }
            $nht6bb159c8f35bca2ccfeca1f51712c3fc68a5b12f = $this->getListFromListByField($nhtc14959708d1ba400138581334c1abd2fe8b4f746['data'], 'kit_id', $nht2b268ceabe2df789699a1a8d11d58b9827a71973['kit_id']);
            if(!$nht6bb159c8f35bca2ccfeca1f51712c3fc68a5b12f){
                continue ;
            }
            foreach($nht6bb159c8f35bca2ccfeca1f51712c3fc68a5b12f as $nht2c0509dd6d1f52c0afa108c78f2f1d6c35352485){
                $nhtbd23dc1b43f98eaa14e4087f3505e3eebbbc9729 = $this->getRowFromListByField($nht513f8de9259fe7658fe14d1352c54ccf070e911f['data'], 'id', $nht2c0509dd6d1f52c0afa108c78f2f1d6c35352485['kitlnk_optionid']);
                if(!$nhtbd23dc1b43f98eaa14e4087f3505e3eebbbc9729){
                    continue ;
                }
                if(!isset($nht29a6717d2e239d90e8177f0c72a84c321f70a02e['data'][$nhtbd23dc1b43f98eaa14e4087f3505e3eebbbc9729['optioncatid']])){
                    continue ;
                }
                $nht4079c1b7a4a983065653250c614c82c908255ac7 = $nht29a6717d2e239d90e8177f0c72a84c321f70a02e['data'][$nhtbd23dc1b43f98eaa14e4087f3505e3eebbbc9729['optioncatid']];
                $nhta62f2225bf70bfaccbc7f1ef2a397836717377de = 'option_' . $nhtbd23dc1b43f98eaa14e4087f3505e3eebbbc9729['id'];
                if(isset($nht4079c1b7a4a983065653250c614c82c908255ac7['data']['option_ids'][$nhta62f2225bf70bfaccbc7f1ef2a397836717377de])){
                    $nht67df27090c4c6d2b1cfb94bf38cf6484a163638b = $nht4079c1b7a4a983065653250c614c82c908255ac7['data']['option_ids'][$nhta62f2225bf70bfaccbc7f1ef2a397836717377de];
                    $nht4b4faf6d20325fb224ce7559f5d7601e0437bca3 = $nht4079c1b7a4a983065653250c614c82c908255ac7['data']['attribute_id'];
                    $this->setProAttrSelect($nht4b4faf6d20325fb224ce7559f5d7601e0437bca3, $nht8eef5efb9406d21b92265e6d5c926a7682309e49, $nht67df27090c4c6d2b1cfb94bf38cf6484a163638b);
                    $nhtcd165334f7dfd27a79fa0a0255ca45fba7f6dd57 = array(
                        'label' => isset($nht4079c1b7a4a983065653250c614c82c908255ac7['opt_label'][$nhta62f2225bf70bfaccbc7f1ef2a397836717377de]) ? $nht4079c1b7a4a983065653250c614c82c908255ac7['opt_label'][$nhta62f2225bf70bfaccbc7f1ef2a397836717377de] : " ",
                        'attribute_id' => $nht4b4faf6d20325fb224ce7559f5d7601e0437bca3,
                        'value_index' => $nht67df27090c4c6d2b1cfb94bf38cf6484a163638b,
                    );
                    $nht6907bf7268beeb1d8d7cfc54324e9f94900bff3d[$nht8eef5efb9406d21b92265e6d5c926a7682309e49][] = $nhtcd165334f7dfd27a79fa0a0255ca45fba7f6dd57;
                }
            }
        }
        foreach($nht29a6717d2e239d90e8177f0c72a84c321f70a02e['data'] as $nhta62f2225bf70bfaccbc7f1ef2a397836717377de => $nhtf25d8b79025530e1888b3e682c3180c11d1ca118){
            $nhtb13fb7c5c9e9dff62b60e0de72929155d3b6167b = $nhtf25d8b79025530e1888b3e682c3180c11d1ca118['data']['attribute_id'];
            $nhtf6eef8a61ed51c85643c72d8cfa10188b13b3307 = array(
                'label' => $nhtf25d8b79025530e1888b3e682c3180c11d1ca118['label'],
                'attribute_id' => $nhtb13fb7c5c9e9dff62b60e0de72929155d3b6167b,
                'code' => $nhtf25d8b79025530e1888b3e682c3180c11d1ca118['data']['attribute_code'],
                'position' => $nhta62f2225bf70bfaccbc7f1ef2a397836717377de,
            );
            $nht012f589f25cf6f788e77b0eb44a5c80330c1901e = $nhtf25d8b79025530e1888b3e682c3180c11d1ca118['data']['option_ids'];
            $nht048b0cb1b94379c74e7e8c8ede496e3edbea3386 = array();
            foreach($nht012f589f25cf6f788e77b0eb44a5c80330c1901e as $nhtb50d10965d325011c273086651934e5e1c010680 => $nht184ba0a2019bcf02f2cec0f847a77196fe823820){
                $nhtf32b67c7e26342af42efabc674d441dca0a281c5 = array(
                    'include' => 1,
                    'value_index' => $nht184ba0a2019bcf02f2cec0f847a77196fe823820,
                );
                $nht048b0cb1b94379c74e7e8c8ede496e3edbea3386[$nht184ba0a2019bcf02f2cec0f847a77196fe823820] = $nhtf32b67c7e26342af42efabc674d441dca0a281c5;
            }
            $nhtf6eef8a61ed51c85643c72d8cfa10188b13b3307['values'] = $nht048b0cb1b94379c74e7e8c8ede496e3edbea3386;
            $nht45d3d3e9b557ab737fb8a4f68776bd8325c94ae2[$nhtb13fb7c5c9e9dff62b60e0de72929155d3b6167b] = $nhtf6eef8a61ed51c85643c72d8cfa10188b13b3307;
        }
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array(
            'associated_product_ids' => $nhtff8660fa516a427b8e99bea52c9919f85d39a4c9,
            'configurable_attributes_data' => $nht45d3d3e9b557ab737fb8a4f68776bd8325c94ae2,
            'can_save_configurable_attributes' => true,
            'affect_configurable_product_attributes' => true,
        );
        return array(
            'result' => "success",
            'data' => $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd
        );
    }

    /**
     * Import attribute for create configurable product
     */
    protected function _importAttribute($nht513f8de9259fe7658fe14d1352c54ccf070e911f, $nht49ba7cdad6f9a18274d6d2ce65b918a22c0f463f){
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array();
        $nhta673aa035fbda0a8f4b1bf81286ab8a2a8708ed7 = $this->_objectManager->create('Magento\Eav\Model\Entity')->setType(\Magento\Catalog\Model\Product::ENTITY)->getTypeId();
        foreach($nht49ba7cdad6f9a18274d6d2ce65b918a22c0f463f['data'] as $nht909e8ef5e6ff8509055e7bcf2e890843c6a2ea9d){
            $nht93d10cdf6209ce42c86c61d4bc6f913e778c2cf6 = $this->getListFromListByField($nht513f8de9259fe7658fe14d1352c54ccf070e911f['data'], 'optioncatid', $nht909e8ef5e6ff8509055e7bcf2e890843c6a2ea9d['id']);
            if(!$nht93d10cdf6209ce42c86c61d4bc6f913e778c2cf6){
                continue;
            }
            $nht8fec997b85b365794f67f9dd2b8534de71ff7e53 = $nhtf6eef8a61ed51c85643c72d8cfa10188b13b3307 = array();
            $nhtf6eef8a61ed51c85643c72d8cfa10188b13b3307 = array(
                'entity_type_id' => $nhta673aa035fbda0a8f4b1bf81286ab8a2a8708ed7,
                'attribute_code' => $this->joinTextToKey($nht909e8ef5e6ff8509055e7bcf2e890843c6a2ea9d['optioncategoriesdesc'], 27, '_'),
                'attribute_set_id' => $this->_notice['config']['attribute_set_id'],
                'frontend_input' => 'select',
                'frontend_label' => array($nht909e8ef5e6ff8509055e7bcf2e890843c6a2ea9d['optioncategoriesdesc']),
                'is_global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_configurable' => true,
            );
            $nht8fec997b85b365794f67f9dd2b8534de71ff7e53['label'] = $nht909e8ef5e6ff8509055e7bcf2e890843c6a2ea9d['optioncategoriesdesc'];
            $nht048b0cb1b94379c74e7e8c8ede496e3edbea3386 = $nhtb3bb496c11d7e6d1cc4e26e52e09896ff71e2ecb = array();
            foreach($nht93d10cdf6209ce42c86c61d4bc6f913e778c2cf6 as $nht102210fe594ee9b33d82058545b1ed14f4c8206e){
                $nhta62f2225bf70bfaccbc7f1ef2a397836717377de = 'option_' . $nht102210fe594ee9b33d82058545b1ed14f4c8206e['id'];
                $nht048b0cb1b94379c74e7e8c8ede496e3edbea3386[$nhta62f2225bf70bfaccbc7f1ef2a397836717377de] = array(
                    0 => $nht102210fe594ee9b33d82058545b1ed14f4c8206e['optionsdesc']
                );
                $nhtb3bb496c11d7e6d1cc4e26e52e09896ff71e2ecb[$nhta62f2225bf70bfaccbc7f1ef2a397836717377de] = $nht102210fe594ee9b33d82058545b1ed14f4c8206e['optionsdesc'];
            }
            $nhtf6eef8a61ed51c85643c72d8cfa10188b13b3307['option']['value'] = $nht048b0cb1b94379c74e7e8c8ede496e3edbea3386;
            $nht8fec997b85b365794f67f9dd2b8534de71ff7e53['opt_label'] = $nhtb3bb496c11d7e6d1cc4e26e52e09896ff71e2ecb;
            $nhtf25d8b79025530e1888b3e682c3180c11d1ca118 = $this->_process->attribute($nhtf6eef8a61ed51c85643c72d8cfa10188b13b3307, array(
                'is_global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_configurable' => true,
            ));
            if(!$nhtf25d8b79025530e1888b3e682c3180c11d1ca118){
                return array(
                    'result' => "warning",
                    'msg' => ""
                );
            }
            $nht8fec997b85b365794f67f9dd2b8534de71ff7e53['data'] = $nhtf25d8b79025530e1888b3e682c3180c11d1ca118;
            $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd[$nht909e8ef5e6ff8509055e7bcf2e890843c6a2ea9d['id']] = $nht8fec997b85b365794f67f9dd2b8534de71ff7e53;
        }
        if(!$nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd){
            return array(
                'result' => 'success',
                'type' => 'change',
                'data' => array()
            );
        }
        return array(
            'result' => 'success',
            'type' => '',
            'data' => $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd
        );
    }

    /**
     * Get id_desc by type and value
     */
    protected function _getLeCaIpImportIdDescByValue($nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9, $nhtf32b67c7e26342af42efabc674d441dca0a281c5){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->selectTableRow(self::TABLE_IMPORT, array(
            'domain' => $this->_cart_url,
            'type' => $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9,
            'value' => $nhtf32b67c7e26342af42efabc674d441dca0a281c5
        ));
        if(!$nht37a5301a88da334dc5afc5b63979daa0f3f45e68){
            return false;
        }
        return (isset($nht37a5301a88da334dc5afc5b63979daa0f3f45e68['id_desc'])) ? $nht37a5301a88da334dc5afc5b63979daa0f3f45e68['id_desc'] : false;
    }

    protected function _getTablesTmp(){
        return array(
            self::VLS_CUR,
            self::VLS_TAX,
            self::VLS_CAT,
            self::VLS_PRO,
            self::VLS_OPT_CAT,
            self::VLS_OPT,
            self::VLS_KIT,
            self::VLS_KIT_LNK,
            self::VLS_CUS,
            self::VLS_ORD,
            self::VLS_ORD_DTL,
            self::VLS_REV
        );
    }
}