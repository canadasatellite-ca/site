<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\Casat\Block\Checkout;

use Magento\Checkout\Helper\Data;
use Magento\Framework\App\ObjectManager;

class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     */
    function process($jsLayout)
    {
        // The following code is a workaround for custom address attributes
//        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
//            ['payment']['children']['afterMethods']['children']['billing-address-form']
//        )) {
//            $billing_address = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
//                ['payment']['children']['afterMethods']['children']['billing-address-form'];
//            $billing_address['displayArea']='billing-address-form';
//            $billing_address['component']='Magestore_OneStepCheckout/js/view/billing-address';
//            foreach($billing_address['children']['form-fields']['children'] as $key=>$billing_field){
//                if(isset($billing_field['config']['elementTmpl']) && $billing_field['config']['elementTmpl']=='ui/form/element/input'){
//                    $billing_address['children']['form-fields']['children'][$key]['config']['template']='Magestore_OneStepCheckout/form/field';
//                    $billing_address['children']['form-fields']['children'][$key]['config']['elementTmpl']='Magestore_OneStepCheckout/form/element/input';
//                }
//                if(isset($billing_field['config']['template']) && $billing_field['config']['template']=='ui/group/group'){
//                    $billing_address['children']['form-fields']['children'][$key]['component'] = 'Magestore_OneStepCheckout/js/view/form/components/group';
//                    foreach($billing_address['children']['form-fields']['children'][$key]['children'] as $key2 => $field){
//                        //$billing_address['children']['form-fields']['children'][$key]['children'][$key2]['config']['template']='Magestore_OneStepCheckout/form/field';
//                        $billing_address['children']['form-fields']['children'][$key]['children'][$key2]['config']['elementTmpl']='Magestore_OneStepCheckout/form/element/input';
//                    }
//                }
//            }
//           /* unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
//                ['payment']['children']['afterMethods']['children']['billing-address-form']);*/
//            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
//            ['shippingAddress']['children']['billing-address-form'] = $billing_address;
//        }


        return $jsLayout;
    }
}
