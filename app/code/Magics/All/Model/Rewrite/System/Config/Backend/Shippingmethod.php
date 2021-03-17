<?php

namespace Magics\All\Model\Rewrite\System\Config\Backend;

class Shippingmethod extends \Magedelight\Subscribenow\Model\System\Config\Backend\Shippingmethod
{
    /**
     * @return array
     */
    public function toOptionArray($isActiveOnlyFlag = false) {
        $methods = [['value' => '', 'label' => '']];
        $shippings = $this->_shippingConfig->getActiveCarriers();
        foreach ($shippings as $shippingCode => $shippingModel) {
            $shippingTitle = $this->_scopeConfig->getValue('carriers/' . $shippingCode . '/title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $methods[$shippingCode] = ['label' => $shippingTitle, 'value' => []];
            if (!empty($shippingModel->getAllowedMethods())) {
                foreach ($shippingModel->getAllowedMethods() as $methodCode => $methodTitle) {
                    $methods[$shippingCode]['value'][] = [
                        'value' => $shippingCode,
                        'label' => '[' . $shippingCode . '] ' . $methodTitle
                    ];
                }
            }
        }

        return $methods;
    }
}
