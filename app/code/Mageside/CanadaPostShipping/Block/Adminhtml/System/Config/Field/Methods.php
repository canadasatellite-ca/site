<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Block\Adminhtml\System\Config\Field;

class Methods extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected $_template = 'Mageside_CanadaPostShipping::system/config/form/field/array.phtml';

    /**
     * @var \Mageside\CanadaPostShipping\Helper\Carrier
     */
    protected $_carrier;

    /**
     * Methods constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Mageside\CanadaPostShipping\Model\Carrier $carrier
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Mageside\CanadaPostShipping\Model\Carrier $carrier,
        array $data = []
    ) {
        $this->_carrier = $carrier;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getArrayRows() {
        $element = $this->getElement();
        $value = $element->getValue();

        if (is_string($value)) {
            $value = unserialize($value);
        }

        if (empty($value)) {
            $value = [];
        }

        $methods = $this->_carrier->getCode('method');
        foreach ($methods as $code => $title) {
            $key = strtolower(str_replace('.', '_', $code));
            if (!key_exists($key, $value)) {
                $value[$key] = [
                    'code'          => $code,
                    'default_label' => $title,
                    'renamed_label' => $title,
                    'non_delivery'  => '',
                ];
            }
        }

        foreach ($value as $key => $item) {
            // $nonDeliveryOptions = $this->_carrier->getCode('nonDeliveryOptions', $item['code']);
            $nonDeliveryOptions = ['RASE', 'ABAN', 'RTS'];
            $options = [];
            $options[] = ['value' => '', 'label' => __('Default')];
            if ($nonDeliveryOptions) {
                foreach ($nonDeliveryOptions as $option) {
                    $options[] = ['value' => $option, 'label' => $this->_carrier->getCode('nonDeliveryLabels', $option)];
                }
            }
            $value[$key]['nonDeliveryOptions'] = $options;
            if (!isset($value[$key]['non_delivery'])) {
                $value[$key]['non_delivery'] = '';
            }
        }

        $element->setValue($value);

        return parent::getArrayRows();
    }

    /**
     * @param string $name
     * @param array $params
     */
    public function addColumn($name, $params)
    {
        $this->_columns[$name] = [
            'label' => $this->_getParam($params, 'label', 'Column'),
            'size' => $this->_getParam($params, 'size', false),
            'style' => $this->_getParam($params, 'style'),
            'class' => $this->_getParam($params, 'class'),
            'type' => $this->_getParam($params, 'type'),
            'readonly' => $this->_getParam($params, 'readonly'),
            'renderer' => false,
        ];
        if (!empty($params['renderer']) && $params['renderer'] instanceof \Magento\Framework\View\Element\AbstractBlock) {
            $this->_columns[$name]['renderer'] = $params['renderer'];
        }
    }

    /**
     * @param string $columnName
     * @return string
     * @throws \Exception
     */
    public function renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new \Exception('Wrong column name specified.');
        }
        $column = $this->_columns[$columnName];
        $inputName = $this->_getCellInputElementName($columnName);

        if ($column['renderer']) {
            return $column['renderer']->setInputName(
                $inputName
            )->setInputId(
                $this->_getCellInputElementId('<%- _id %>', $columnName)
            )->setColumnName(
                $columnName
            )->setColumn(
                $column
            )->toHtml();
        }

        if ($column['type'] == 'select') {
            return '<select' .
                ' id="' . $this->_getCellInputElementId('<%- _id %>', $columnName) . '"' .
                ' name="' . $inputName . '"' .
                ($column['size'] ? ' size="' . $column['size'] . '"' : '') .
                ($column['readonly'] ? ' readonly="readonly"' : '') .
                ' class="' . (isset($column['class']) ? $column['class'] : '') . '"' .
                (isset($column['style']) ? ' style="' . $column['style'] . '"' : '') .
                '>' .
                '<% _.each(nonDeliveryOptions, function(item, key) { %>' .
                '<option value="<%= item.value %>" <% if (item.value == ' . $columnName . ') { %>selected="selected"<% } %>><%= item.label %></option>' .
                '<% }); %>' .
                '</select>';
        }

        return '<input type="' . $column['type'] . '"' .
            ' id="' . $this->_getCellInputElementId('<%- _id %>', $columnName) . '"' .
            ' name="' . $inputName .
            ' value="<%- ' . $columnName . ' %>"' .
            ($column['size'] ? ' size="' . $column['size'] . '"' : '') .
            ($column['readonly'] ? ' readonly="readonly"' : '') .
            ' class="' . (isset($column['class']) ? $column['class'] : 'input-text') . '"' .
            (isset($column['style']) ? ' style="' . $column['style'] . '"' : '') .
            '/>';
    }

    /**
     * Add Columns
     */
    public function _prepareToRender()
    {
        $this->addColumn('code', ['label' => __('Method'), 'type' => 'hidden', 'size' => '10', 'readonly' => true]);
        $this->addColumn('default_label', ['label' => __('Default Label'), 'type' => 'hidden', 'readonly' => false]);
        $this->addColumn('renamed_label', ['label' => __('Label'), 'type' => 'text', 'readonly' => false]);
        $this->addColumn('non_delivery', ['label' => __('Non-delivery'), 'type' => 'select', 'readonly' => false]);
    }
}
