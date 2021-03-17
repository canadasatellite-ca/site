<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Config\Source;

use Mageside\CanadaPostShipping\Helper\Carrier as Helper;

/**
 * Class DeliveryToPostofficeLayoutProcessor
 */
class DeliveryToPostofficeLayoutProcessor
{
    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param Helper $helper
     */
    public function __construct(
        Helper $helper,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->_helper = $helper;
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        if ($this->_helper->isModuleEnabled() && $this->_helper->getConfigCarrier('enable_d2po')) {
            $jsLayoutDelivery = [
                'components' => [
                    'checkout' => [
                        'children' => [
                            'steps' => [
                                'children' => [
                                    'shipping-step' => [
                                        'children' => [
                                            'shippingAddress' => [
                                                'children' => [
                                                    'shippingAdditional' => [
                                                        'component' => 'uiComponent',
                                                        'displayArea' => 'shippingAdditional',
                                                        'children' => [
                                                            'delivery-to-postoffice' => [
                                                                'config' => [
                                                                    'component' => 'Mageside_CanadaPostShipping/js/view/form/element/delivery-to-postoffice',
                                                                    'displayArea' => 'delivery-to-postoffice',
                                                                    'options' => [
                                                                        'availableMethods' => $this->getAvailableShippingMethods(),
                                                                        'getOfficesUrl' => $this->_urlBuilder->getUrl('canadapost/postoffice/getList'),
                                                                        'getOfficeDetailUrl' => $this->_urlBuilder->getUrl('canadapost/postoffice/getDetail'),
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $jsLayout = array_merge_recursive($jsLayout, $jsLayoutDelivery);
        }

        return $jsLayout;
    }

    private function getAvailableShippingMethods()
    {
        return ['DOM.EP', 'DOM.XP'];
    }
}
