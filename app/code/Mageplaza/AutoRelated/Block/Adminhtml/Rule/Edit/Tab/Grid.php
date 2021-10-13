<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_AutoRelated
 * @copyright   Copyright (c) 2017-2018 Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AutoRelated\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Magento\Framework\Module\Manager;
use Magento\Store\Model\WebsiteFactory;
use Mageplaza\AutoRelated\Model\RuleFactory;

/**
 * Class Grid
 * @package Mageplaza\AutoRelated\Block\Adminhtml\Rule\Edit\Tab
 */
class Grid extends \Magento\Catalog\Block\Adminhtml\Product\Grid
{
    /**
     * @var \Mageplaza\AutoRelated\Model\RuleFactory
     */
    protected $autoRelatedRuleFac;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Type $type
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $status
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Mageplaza\AutoRelated\Model\RuleFactory $autoRelatedRuleFac
     * @param array $data
     */
    function __construct(
        Context $context,
        Data $backendHelper,
        WebsiteFactory $websiteFactory,
        CollectionFactory $setsFactory,
        ProductFactory $productFactory,
        Type $type,
        Status $status,
        Visibility $visibility,
        Manager $moduleManager,
        RuleFactory $autoRelatedRuleFac,
        array $data = []
    )
    {
        parent::__construct($context, $backendHelper, $websiteFactory, $setsFactory, $productFactory, $type, $status, $visibility, $moduleManager, $data);

        $this->autoRelatedRuleFac = $autoRelatedRuleFac;
    }

    /**
     * @param \Magento\Framework\Data\Collection $collection
     * @return bool|void
     */
    function setCollection($collection)
    {
        $ruleId = $this->getRequest()->getParam('id');
        $rule   = $this->autoRelatedRuleFac->create()->load($ruleId);
        if (!$rule) {
            return false;
        }
        $productIds = $rule->getMatchingProductIds($this->getRequest()->getParam('type'));
        $collection->addIdFilter($productIds);

        parent::setCollection($collection);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->removeColumn('websites')
            ->removeColumn('edit')
            ->unsetChild('grid.bottom.links');

        $this->setFilterVisibility(false);

        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     * @return string
     */
    function getRowUrl($row)
    {
        return $this->getUrl(
            'catalog/product/edit',
            ['store' => $this->getRequest()->getParam('store'), 'id' => $row->getId()]
        );
    }
}
