<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Config\Model;

use Magento\Sales\Api\Data\OrderAddressInterface;

/**
 * Class ConfigPlugin
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Config\Model
 */
class ConfigPlugin
{
    /**
     * @var \Cart2Quote\Quotation\Helper\Address $addressHelper
     */
    private $addressHelper;

    /**
     * ConfigPlugin constructor
     *
     * @param \Cart2Quote\Quotation\Helper\Address $addressHelper
     */
    public function __construct(\Cart2Quote\Quotation\Helper\Address $addressHelper)
    {
        $this->addressHelper = $addressHelper;
    }

    /**
     * After save trigger
     *
     * @param \Magento\Config\Model\Config $subject
     * @return \Magento\Config\Model\Config $subject
     */
    public function afterSave(\Magento\Config\Model\Config $subject)
    {
        $data = $subject->getData();
        if (isset($data['groups']['address']['fields'])) {
            $fields = $data['groups']['address']['fields'];
            $configs = $this->addressHelper->getAddressConfigArrays();
            foreach ($configs as &$config) {
                if (is_array($config)) {
                    foreach ($fields as $key => $field) {
                        //check if value isset
                        if (!isset($field['value'])) {
                            continue;
                        }

                        foreach ($config as &$value) {
                            if (($key == $value['name'] . "_show")
                                || (($value['name'] == OrderAddressInterface::VAT_ID) && ($key == 'taxvat_show'))
                            ) {
                                if ($field['value'] == 'req') {
                                    $value['locked'] = true;
                                    $value['enabled'] = true;
                                    $value['required'] = true;
                                } else {
                                    $value['locked'] = false;
                                }
                            }
                        }
                    }
                }
            }

            $this->addressHelper->setAddressConfig($configs);
        }

        return $subject;
    }
}
