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

namespace Mageplaza\AutoRelated\Model\ResourceModel;

use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\AutoRelated\Helper\Data;
use Mageplaza\AutoRelated\Model\RuleFactory;

/**
 * Class Rule
 * @package Mageplaza\AutoRelated\Model\ResourceModel
 */
class Rule extends AbstractDb
{
    /**
     * Date model
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Mageplaza\AutoRelated\Model\RuleFactory
     */
    protected $autoRelatedRuleFac;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Mageplaza\AutoRelated\Helper\Data
     */
    protected $helperData;

    /**
     * Rule constructor.
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mageplaza\AutoRelated\Model\RuleFactory $autoRelatedRuleFac
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Mageplaza\AutoRelated\Helper\Data $helperData
     */
    function __construct(
        DateTime $date,
        Context $context,
        SessionFactory $customerSession,
        StoreManagerInterface $storeManager,
        RuleFactory $autoRelatedRuleFac,
        Cart $cart,
        ProductFactory $productFactory,
        Data $helperData
    )
    {
        parent::__construct($context);

        $this->customerSession    = $customerSession;
        $this->storeManager       = $storeManager;
        $this->autoRelatedRuleFac = $autoRelatedRuleFac;
        $this->cart               = $cart;
        $this->productFactory     = $productFactory;
        $this->date               = $date;
        $this->helperData         = $helperData;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    function _construct()
    {
        $this->_init('mageplaza_autorelated_block_rule', 'rule_id');
    }

    /**
     * @inheritdoc
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->date->date());
        } else {
            $bind = ['display' => new \Zend_Db_Expr(1)];
            $this->getConnection()->update($this->getMainTable(), $bind);
        }

        return parent::_beforeSave($object);
    }

    /**
     * customer group rule config
     *
     * @param string $ruleId
     * @return array
     */
    function getCustomerGroupByRuleId($ruleId)
    {
        $tableName = $this->getTable('mageplaza_autorelated_block_rule_customer_group');
        $select    = $this->getConnection()->select()
            ->from($tableName, 'customer_group_id')
            ->where('rule_id = ?', $ruleId);

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * store view rule config
     *
     * @param string $ruleId
     * @return array
     */
    function getStoresByRuleId($ruleId)
    {
        $tableName = $this->getTable('mageplaza_autorelated_block_rule_store');
        $select    = $this->getConnection()->select()
            ->from($tableName, 'store_id')
            ->where('rule_id = ?', $ruleId);

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * customer group rule config
     *
     * @param $ruleId
     * @return array|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    function getChildById($ruleId)
    {
        $connection = $this->getConnection();
        $data       = $connection->fetchRow(
            $connection->select()->from(
                $this->getMainTable()
            )->where(
                'parent_id = ?',
                $ruleId
            )->limit(
                1
            )
        );
        if ($data) {
            return $data;
        }

        return false;
    }

    /**
     * delete rule
     *
     * @param $ruleId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    function deleteById($ruleId)
    {
        if ($ruleId) {
            $this->autoRelatedRuleFac->create()->load($ruleId)->delete();
            $this->deleteMultipleData('mageplaza_autorelated_actions_index', ['rule_id = ?' => $ruleId]);
            $child = $this->getChildById($ruleId);
            if (!empty($child)) {
                $this->deleteMultipleData('mageplaza_autorelated_actions_index', ['rule_id = ?' => $child['rule_id']]);
                $this->autoRelatedRuleFac->create()->load($child['rule_id'])->delete();
            }
        }
    }

    /**
     * delete store view and customer rule
     *
     * @param string $ruleId
     * @return void
     */
    function deleteOldData($ruleId)
    {
        if ($ruleId) {
            $where = ['rule_id = ?' => $ruleId];
            $this->deleteMultipleData('mageplaza_autorelated_block_rule_store', $where);
            $this->deleteMultipleData('mageplaza_autorelated_block_rule_customer_group', $where);
        }
    }

    /**
     * update store view
     *
     * @param array $data
     * @param string $ruleId
     * @return void
     */
    function updateStore($data = [], $ruleId)
    {
        $dataInsert = [];
        foreach ($data as $storeId) {
            $dataInsert[] = [
                'rule_id'  => $ruleId,
                'store_id' => $storeId
            ];
        }
        $this->updateMultipleData('mageplaza_autorelated_block_rule_store', $dataInsert);
    }

    /**
     * update customer group
     *
     * @param array $data
     * @param string $ruleId
     * @return void
     */
    function updateCustomerGroup($data = [], $ruleId)
    {
        $dataInsert = [];
        foreach ($data as $customerGroupId) {
            $dataInsert[] = [
                'rule_id'           => $ruleId,
                'customer_group_id' => $customerGroupId
            ];
        }
        $this->updateMultipleData('mageplaza_autorelated_block_rule_customer_group', $dataInsert);
    }

    /**
     * update database
     *
     * @param string $tableName
     * @param array $data
     * @return void
     */
    function updateMultipleData($tableName, $data = [])
    {
        $table = $this->getTable($tableName);
        if ($table && !empty($data)) {
            $this->getConnection()->insertMultiple($table, $data);
        }
    }

    /**
     * delete database
     *
     * @param string $tableName
     * @param array $where
     * @return void
     */
    function deleteMultipleData($tableName, $where = [])
    {
        $table = $this->getTable($tableName);
        if ($table && !empty($where)) {
            $this->getConnection()->delete($table, $where);
        }
    }

    /**
     * get products by rule
     *
     * @param string $ruleId
     * @param string $productId
     * @return array
     */
    protected function getProductListByRuleId($ruleId, $productId = null)
    {
        $adapter    = $this->getConnection();
        $indexTable = $this->getTable('mageplaza_autorelated_actions_index');
        $select     = $adapter->select()
            ->from($indexTable, 'product_id')
            ->where('rule_id = ?', $ruleId)
            ->where('product_id != ?', $productId);

        return $adapter->fetchCol($select);
    }

    /**
     * get products with page
     *
     * @param $pageType
     * @param null $id
     * @param array $ruleParamIds
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Serializer_Exception
     */
    function getProductList($pageType, $id = null, $ruleParamIds = [])
    {
        $storeId       = $this->storeManager->getStore()->getId();
        $result        = [];
        $customerGroup = 0;
        if ($this->customerSession->create()->isLoggedIn()) {
            $customerGroup = $this->customerSession->create()->getCustomer()->getGroupId();
        }
        $adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from(['main' => $this->getMainTable()])
            ->join(
                ['group' => $this->getTable('mageplaza_autorelated_block_rule_customer_group')],
                'main.rule_id=group.rule_id',
                ['customer_group_id' => 'group.customer_group_id']
            )
            ->join(
                ['store' => $this->getTable('mageplaza_autorelated_block_rule_store')],
                'main.rule_id=store.rule_id',
                ['store_id' => 'store.store_id']
            )
            ->where('customer_group_id = ?', $customerGroup)
            ->where('store_id = 0 OR store_id = ?', $storeId)
            ->where('from_date is null OR from_date <= ?', $this->date->date())
            ->where('to_date is null OR to_date >= ?', $this->date->date());
        if ($pageType != 'cms') {
            $select->where('block_type = ?', $pageType);
        }
        $select->where('display = ?', true)
            ->where('is_active = ?', true)
            ->order('sort_order ' . Select::SQL_ASC);
        $ruleIds = array_unique($adapter->fetchCol($select));
        if (!empty($ruleParamIds)) {
            $ruleIds = array_intersect($ruleIds, $ruleParamIds);
        }
        if (!empty($ruleIds)) {
            foreach ($ruleIds as $ruleId) {
                $rule       = $this->getRuleById($ruleId);
                $productIds = $this->getProductIds($pageType, $ruleId, $id);
                if (!empty($productIds)) {
                    if ($rule['parent_id'] && in_array($rule['parent_id'], $ruleIds) && !empty($this->getProductIds($pageType, $rule['parent_id'], $id))) {
                        continue;
                    }
                    $key = $rule['location'];
                    if ($pageType == 'cms') {
                        $key = 'cms-' . $ruleId;
                        if ($rule['parent_id']) {
                            $key = 'cms-' . $rule['parent_id'];
                        }
                    }
                    $result[$key][] = [
                        'product_ids' => array_unique($productIds),
                        'rule'        => $rule
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * get product ids by rule
     *
     * @param $pageType
     * @param $ruleId
     * @param null $id
     * @return array
     * @throws \Zend_Serializer_Exception
     */
    protected function getProductIds($pageType, $ruleId, $id = null)
    {
        $rule       = $this->autoRelatedRuleFac->create()->load($ruleId);
        $quote      = $this->cart->getQuote();
        $productIds = [];
        if ($pageType == 'product') {
            $product = $this->productFactory->create()->load($id);
            if ($rule->getConditions()->validate($product)) {
                $productIds = $this->getProductListByRuleId($ruleId, $id);
            }
        } else if ($pageType == 'category' && $rule->getCategoryConditionsSerialized()) {
            $categoryIds = $this->helperData->unserialize($rule->getCategoryConditionsSerialized());
            if (in_array($id, $categoryIds)) {
                $productIds = $this->getProductListByRuleId($ruleId);
            }
        } else if ($pageType == 'cart' && $quote) {
            $items    = $quote->getAllVisibleItems();
            $totalQty = 0;
            $weight   = 0;
            foreach ($items as $item) {
                $totalQty += (int)$item->getQty();
                $weight   += ($item->getWeight() * $item->getQty());
            }

            $quote->setWeight($weight)
                ->setTotalQty($totalQty)
                ->setQuote($quote);
            if ($rule->getConditions()->validate($quote)) {
                $productIds = $this->getProductListByRuleId($ruleId);
            }
        } else if ($pageType == 'cms') {
            $productIds = $this->getProductListByRuleId($ruleId);
        }

        return $productIds;
    }

    /**
     * get rule by id
     *
     * @param null $ruleId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    function getRuleById($ruleId = null)
    {
        if ($ruleId) {
            $adapter = $this->getConnection();
            $select  = $adapter->select()
                ->from($this->getMainTable())
                ->where('rule_id = ?', $ruleId);

            return $adapter->fetchRow($select);
        }

        return [];
    }

    /**
     * update impression rule by id
     *
     * @param $pageType
     * @param null $id
     * @param null $ruleId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Serializer_Exception
     */
    function updateImpression($pageType, $id = null, $ruleId = null)
    {
        if ($ruleId) {
            $bind2  = [];
            $where2 = [];
            $bind   = [
                'impression'       => new \Zend_Db_Expr('impression+1'),
                'total_impression' => new \Zend_Db_Expr('total_impression+1')
            ];
            $where  = ['rule_id = ?' => (int)$ruleId];
            $rule   = $this->getRuleById($ruleId);
            if ($rule['parent_id'] && $rule['is_active']) {
                $parentRule = $this->getRuleById($rule['parent_id']);
                if ($parentRule['is_active']) {
                    $bind2 = [
                        'total_impression' => new \Zend_Db_Expr('total_impression+1'),
                        'display'          => new \Zend_Db_Expr(1)
                    ];
                    if (!empty($this->getProductIds($pageType, $rule['parent_id'], $id))) {
                        $bind['display'] = new \Zend_Db_Expr(0);
                    }
                    $where2 = ['rule_id = ?' => (int)$parentRule['rule_id']];
                }
            }
            $child = $this->getChildById($ruleId);
            if (!empty($child) && $child['is_active']) {
                if (!empty($this->getProductIds($pageType, $child['rule_id'], $id))) {
                    $bind['display'] = new \Zend_Db_Expr(0);
                }
                $bind2  = ['display' => new \Zend_Db_Expr(1)];
                $where2 = ['rule_id = ?' => (int)$child['rule_id']];
            }
            if (!empty($bind2) && !empty($where2)) {
                $this->getConnection()->update($this->getMainTable(), $bind2, $where2);
            }
            $this->getConnection()->update($this->getMainTable(), $bind, $where);
        }
    }

    /**
     * update click rule by id
     *
     * @param null $ruleId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    function updateClick($ruleId = null)
    {
        if ($ruleId) {
            $bind  = [
                'click'       => new \Zend_Db_Expr('click+1'),
                'total_click' => new \Zend_Db_Expr('total_click+1')
            ];
            $where = ['rule_id = ?' => (int)$ruleId];
            $rule  = $this->getRuleById($ruleId);
            if ($rule['parent_id']) {
                $bind2  = [
                    'total_click' => new \Zend_Db_Expr('total_click+1')
                ];
                $where2 = ['rule_id = ?' => (int)$rule['parent_id']];
                $this->getConnection()->update($this->getMainTable(), $bind2, $where2);
            }
            $this->getConnection()->update($this->getMainTable(), $bind, $where);
        }
    }
}
