<?php

namespace CanadaSatellite\AstIntegration\LogicProcessors;

class OrderCustomOptionsHelper {
    private $optionsSku;
    private $options;
    private $optionsId;

    /**
     * @param \Magento\Sales\Model\Order\Item $item
     * @param boolean $useSku Toggles parsing options SKU values or titles
     */
    public function __construct($item, $useSku = true) {
        $product = $item->getProduct();

        // Parse options sku's and ids
        $this->optionsSku = array();
        $this->optionsId = array();
        foreach ($product->getOptions() as $option) {
            $this->optionsId[$option->getOptionId()] = $option->getTitle();

            $values = $option->getValues();
            if (is_null($values)) continue;

            $valMap = array();
            foreach ($values as $val) {
                $valMap[$val->getOptionTypeId()] = $useSku
                    ? $val->getSku() ?: $val->getTitle()
                    : $val->getTitle();
            }
            $this->optionsSku[$option->getTitle()] = $valMap;
        }

        // Parse options
        $this->options = array();
        $options = $item->getProductOptionByCode('options');
        if (!is_null($options)) {
            foreach ($options as $opt) {
                $label = $opt['label'];
                $value = $opt['option_value'];

                $this->options[$label] = isset($this->optionsSku[$label])
                    ? $this->optionsSku[$label][$value]
                    : $value;
            }
        }
    }

    /**
     * @param string $label
     * @return string|null An option value or null
     */
    public function getOptionValue($label) {
        return isset($this->options[$label])
            ? $this->options[$label]
            : null;
    }

    /**
     * @param string ...$labels
     * @return string|null A first exists option value or null
     */
    public function getFirstExistOptionValue(...$labels) {
        foreach ($labels as $label) {
            if (isset($this->options[$label])) {
                return $this->options[$label];
            }
        }
        return null;
    }

    /**
     * @return array An array where key is a label and value is an option value
     */
    public function getAllOptions() {
        return $this->options;
    }

    /**
     * @return array An array where key is id and value is a label
     */
    public function getAllOptionIds() {
        return $this->optionsId;
    }

    /**
     * @param integer $id
     * @return string|null An option label or null
     */
    public function getOptionLabelById($id) {
        return isset($this->optionsId[$id])
            ? $this->optionsId[$id]
            : null;
    }

    /**
     * @param integer $id
     * @return string|null An option value or null
     */
    public function getOptionValueById($id) {
        $label = $this->getOptionLabelById($id);
        return isset($title)
            ? $this->getOptionValue($label)
            : null;
    }
}