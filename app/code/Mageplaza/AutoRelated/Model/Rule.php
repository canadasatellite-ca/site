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

namespace Mageplaza\AutoRelated\Model;

use Magento\Rule\Model\AbstractModel;

/**
 * Class Rule
 * @package Mageplaza\AutoRelated\Model
 */
class Rule extends AbstractModel
{
    /**
     * Store matched product Ids
     *
     * @var array
     */
    protected $productIds;

    /**
     * Store matched product Ids in condition tab
     *
     * @var array
     */
    protected $productConditionsIds;

    /**
     * Store matched product Ids with rule id
     *
     * @var array
     */
    protected $dataProductIds;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Iterator
     */
    protected $resourceIterator;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $productStatus;

    /**
     * @var \Magento\CatalogRule\Model\Rule\Condition\CombineFactory
     */
    protected $_productCombineFactory;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\CombineFactory
     */
    protected $_salesCombineFactory;

    /**
     * Rule constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\CatalogRule\Model\Rule\Condition\CombineFactory $catalogCombineFactory
     * @param \Magento\SalesRule\Model\Rule\Condition\CombineFactory $salesCombineFactory
     * @param \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\CatalogRule\Model\Rule\Condition\CombineFactory $catalogCombineFactory,
        \Magento\SalesRule\Model\Rule\Condition\CombineFactory $salesCombineFactory,
        \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->_productCombineFactory = $catalogCombineFactory;
        $this->_salesCombineFactory   = $salesCombineFactory;
        $this->resourceIterator       = $resourceIterator;
        $this->productFactory         = $productFactory;
        $this->productVisibility      = $productVisibility;
        $this->productStatus          = $productStatus;

        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('Mageplaza\AutoRelated\Model\ResourceModel\Rule');
        $this->setIdFieldName('rule_id');
    }

    /**
     * Get rule condition combine model instance
     *
     * @return \Magento\CatalogRule\Model\Rule\Condition\Combine|\Magento\SalesRule\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        $type = $this->_registry->registry('autorelated_type');
        if ($type == 'cart') {
            return $this->_salesCombineFactory->create();
        }

        return $this->_productCombineFactory->create();
    }

    /**
     * Get rule condition product combine model instance
     *
     * @return \Magento\CatalogRule\Model\Rule\Condition\Combine
     */
    public function getActionsInstance()
    {
        return $this->_productCombineFactory->create();
    }

    /**
     * @param string $formName
     * @return string
     */
    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }

    /**
     * @param string $formName
     * @return string
     */
    public function getActionsFieldSetId($formName = '')
    {
        return $formName . 'rule_actions_fieldset_' . $this->getId();
    }

    /**
     * @param $id
     */
    public function deleteById($id)
    {
        $this->getResource()->deleteById($id);
    }

    /**
     * @return bool
     */
    public function hasChild()
    {
        $ruleChild = $this->getChild();
        if (!empty($ruleChild)) {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getChild()
    {
        return $this->getResource()->getChildById($this->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        if ($this->getCustomerGroupIds() || $this->getStoreIds()) {
            $this->getResource()->deleteOldData($this->getId());
            if ($storeIds = $this->getStoreIds()) {
                $this->getResource()->updateStore($storeIds, $this->getId());
            }
            if ($groupIds = $this->getCustomerGroupIds()) {
                $this->getResource()->updateCustomerGroup($groupIds, $this->getId());
            }
        }

        $this->getMatchingProductIds();
        $this->getResource()->deleteMultipleData('mageplaza_autorelated_actions_index', ['rule_id = ?' => $this->getId()]);
        if (!empty($this->dataProductIds) && is_array($this->dataProductIds)) {
            $this->getResource()->updateMultipleData('mageplaza_autorelated_actions_index', $this->dataProductIds);
        }

        return parent::afterSave();
    }

    /**
     * Get array of product ids which are matched by rule
     *
     * @return array
     */
    public function getMatchingProductIds($type = null)
    {
        if ($this->productIds === null) {
            $this->productIds = [];
            $this->setCollectedAttributes([]);

            /** @var $productCollection \Magento\Catalog\Model\ResourceModel\Product\Collection */
            $productCollection = $this->productFactory->create()->getCollection();
            $productCollection->addAttributeToSelect('*')
                ->addAttributeToFilter('visibility', 4)->addAttributeToFilter('status', 1);

            $this->getActions()->collectValidatedAttributes($productCollection);
            $this->resourceIterator->walk(
                $productCollection->getSelect(),
                [[$this, 'callbackValidateProduct']],
                [
                    'attributes' => $this->getCollectedAttributes(),
                    'product'    => $this->productFactory->create()
                ]
            );
            if ($this->getBlockType() == 'product') {
                $this->getConditions()->collectValidatedAttributes($productCollection);
                $this->resourceIterator->walk(
                    $productCollection->getSelect(),
                    [[$this, 'callbackValidateProductConditions']],
                    [
                        'attributes' => $this->getCollectedAttributes(),
                        'product'    => $this->productFactory->create()
                    ]
                );
            }
        }
        if ($type && $type == 'cond') {
            return $this->productConditionsIds;
        }

        return $this->productIds;
    }

    /**
     * Callback function for product matching
     *
     * @param array $args
     * @return void
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);
        $ruleId = $this->getRuleId();
        if ($ruleId && $this->getActions()->validate($product)) {
            $this->productIds[]     = $product->getId();
            $this->dataProductIds[] = ['rule_id' => $ruleId, 'product_id' => $product->getId()];
        }
    }

    /**
     * Callback function for product matching (conditions)
     *
     * @param array $args
     * @return void
     */
    public function callbackValidateProductConditions($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);
        $ruleId = $this->getRuleId();
        if ($ruleId && $this->getConditions()->validate($product)) {
            $this->productConditionsIds[] = $product->getId();
        }
    }
}
