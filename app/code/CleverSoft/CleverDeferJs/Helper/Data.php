<?php
/**
 * @category    CleverSoft
 * @package     CleverDeferJs
 * @copyright   Copyright © 2017 CleverSoft., JSC. All Rights Reserved.
 * @author 		ZooExtension.com
 * @email       magento.cleversoft@gmail.com
 */

namespace CleverSoft\CleverDeferJs\Helper;
use Magento\Customer\Model\Session as CustomerSession;
class Data extends \Magento\Framework\App\Helper\AbstractHelper{
    public function getConfig($optionString, $scopeCode = null){
        return $this->scopeConfig->getValue($optionString, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scopeCode);
    }

}