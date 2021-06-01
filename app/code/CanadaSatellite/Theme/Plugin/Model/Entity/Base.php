<?php

namespace CanadaSatellite\Theme\Plugin\Model\Entity;

use \MageWorx\OptionBase\Model\Entity\Base as ParentBase;
use \Magento\Catalog\Model\Product\Option;
use \Magento\Catalog\Model\Product\Option\Value;
use \MageWorx\OptionBase\Model\Product\Option\Attributes as OptionAttributes;
use \MageWorx\OptionBase\Model\Product\Option\Value\Attributes as OptionValueAttributes;

class Base
{

    /**
     * @var OptionValueAttributes
     */
    protected $optionValueAttributes;

    /**
     * @var OptionAttributes
     */
    protected $optionAttributes;

    /**
     * Base constructor.
     * @param OptionValueAttributes $optionValueAttributes
     * @param OptionAttributes $optionAttributes
     */

    function __construct(
        OptionValueAttributes $optionValueAttributes,
        OptionAttributes $optionAttributes
    ){
        $this->optionAttributes = $optionAttributes;
        $this->optionValueAttributes = $optionValueAttributes;
    }

    /**
     * @param ParentBase $subject
     * @param callable $proceed
     * @param $object
     * @return array
     */

    function aroundGetOptionsAsArray(
        ParentBase $subject,
        callable $proceed,
        $object
    ) {
        $options = $object->getOptions();

        if ($options == null) {
            $options = [];
        }

        $showPrice = true;
        $results = [];

        foreach ($options as $option) {
            /* @var $option Option */
            $result = [];
            $result['id'] = $option->getOptionId();
            $result['item_count'] = $object->getItemCount();
            $result['option_id'] = $option->getOptionId();
            $result['title'] = $option->getTitle();
            $result['type'] = $option->getType();
            $result['is_require'] = $option->getIsRequire();
            $result['sort_order'] = $option->getSortOrder();
            $result['currency_code'] = $option->getCurrencyCode();
            $result['can_edit_price'] = $object->getCanEditPrice();
            $result['group_option_id'] = $option->getGroupOptionId();
            if (!empty($object->getGroupId())) {
                $result['group_id'] = $object->getGroupId();
            }

            if ($option->getGroupByType() == Option::OPTION_GROUP_SELECT &&
                $option->getValues()
            ) {
                $itemCount = 0;
                foreach ($option->getValues() as $value) {
                    $i = $value->getOptionTypeId();
                    /* @var $value Value */
                    $result['values'][$i] = [
                        'item_count' => max($itemCount, $value->getOptionTypeId()),
                        'option_id' => $value->getOptionId(),
                        'option_type_id' => $value->getOptionTypeId(),
                        'title' => $value->getTitle(),
                        'price' => $showPrice ?
                            $subject->getPriceValue((float)$value->getPrice(), $value->getPriceType()) :
                            0,
                        'price_type' => $showPrice && $value->getPriceType() ?
                            $value->getPriceType() :
                            'fixed',
                        'sku' => $value->getSku(),
                        'sort_order' => $value->getSortOrder(),
                        'group_option_value_id' => $value->getGroupOptionValueId(),
                    ];
                    if (!empty($object->getGroupId())) {
                        $result['values'][$i]['group_id'] = $object->getGroupId();
                    }
                    // Add option value attributes specified in the third-party modules to the option values
                    $result['values'][$i] = $this->addSpecificOptionValueAttributes($result['values'][$i], $value);
                }
            } else {
                $result['price'] = $showPrice ? $subject->getPriceValue(
                    (float)$option->getPrice(),
                    $option->getPriceType()
                ) : 0;
                $result['price_type'] = $option->getPriceType() ? $option->getPriceType() : 'fixed';
                $result['sku'] = $option->getSku();
                $result['max_characters'] = $option->getMaxCharacters();
                $result['file_extension'] = $option->getFileExtension();
                $result['image_size_x'] = $option->getImageSizeX();
                $result['image_size_y'] = $option->getImageSizeY();
                $result['values'] = null;
            }

            // Add option attributes specified in the third-party modules to the option
            $result = $this->addSpecificOptionAttributes($result, $option);
            $results[$option->getOptionId()] = $result;
        }

        return $results;
    }

    /**
     * Add specific third-party modules option value attributes
     *
     * @param $result
     * @param $value
     * @return array
     */

    protected function addSpecificOptionValueAttributes($result, $value)
    {
        $attributes = $this->optionValueAttributes->getData();
        return $this->addSpecificAttributes($attributes, $result, $value);
    }

    /**
     * Add specific third-party modules option attributes
     *
     * @param $result
     * @param $option
     * @return array
     */

    protected function addSpecificOptionAttributes($result, $option)
    {
        $attributes = $this->optionAttributes->getData();
        return $this->addSpecificAttributes($attributes, $result, $option);
    }

    /**
     * Add specific third-party modules attributes
     *
     * @param $attributes
     * @param $result
     * @param $object
     * @return array
     */

    protected function addSpecificAttributes($attributes, $result, $object)
    {
        foreach ($attributes as $attribute) {
            $attributeName = $attribute->getName();
            $data = $object->getData();
            if (isset($data[$attributeName])) {
                $result[$attributeName] = $data[$attributeName];
            }
        }
        return $result;
    }

}
