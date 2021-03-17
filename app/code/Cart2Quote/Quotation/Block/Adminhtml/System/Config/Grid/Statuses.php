<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Grid;

/**
 * Class Statuses
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Grid
 */
class Statuses extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'Cart2Quote_Quotation::system/config/grid/statuses.phtml';

    /**
     * Quote Status Collection
     *
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection
     */
    protected $statusCollection;

    /**
     * Statuses constructor.
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->statusCollection = $statusCollection;
    }

    /**
     * Get grid configuration
     *
     * @return string
     */
    public function getStatusesGridConfig()
    {
        return json_encode([
            'field' => [
                'name' => $this->getElement()->getName(),
                'label' => $this->getElement()->getLabel(),
                'fieldValue' => $this->getElement()->getValue(),
                'htmlId' => $this->getElement()->getHtmlId(),
                'statuses' => $this->getAllStatussesAsArray()
            ]
        ]);
    }

    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);

        return $this->toHtml();
    }

    /**
     * Get all statusses in ascending order
     *
     * @return array
     */
    public function getAllStatussesAsArray()
    {
        return $this->statusCollection->addOrder('sort', 'ASC')->toOptionArray();
    }
}
