<?php

namespace Magedelight\Faqs\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;

/**
 * Faq post mysql resource
 * @method array|null getProductsData()
 */
class Faq extends AbstractDb {

    /**
     * Initialize resource model
     *
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _construct() {
        // Table Name and Primary Key column
        $this->_init('md_faq', 'question_id');
    }

    protected function _afterSave(AbstractModel $object) {
        $this->saveProductRelation($object);
        $this->saveCategoryRelation($object);
        return parent::_afterSave($object);
    }

    // @codingStandardsIgnoreEnd
    public function saveProductRelation($faq) {

        $id = $faq->getId();
        /**
         * new category-product relationships
         */
        $products = $faq->getPostedProducts();
        /**
         * Example re-save category
         */
        if ($products === null) {
            return $this;
        }
        /**
         * old category-product relationships
         */
        $oldProducts = $this->lookupProductIds($faq);
        if(count($oldProducts) > 0)
        {
        $oldProducts = array_flip($oldProducts);
        }
        else
        {
        $oldProducts = $oldProducts;
        }

        
        $insert = array_diff_key($products, $oldProducts);
        $delete = array_diff_key($oldProducts, $products);


        $connection = $this->getConnection();

        /**
         * Delete products from category
         */
        if (!empty($delete)) {
            $cond = ['product_id IN(?)' => array_keys($delete), 'question_id=?' => $id];
            $connection->delete($this->getTable('md_faq_product'), $cond);
        }
        /**
         * Add products to category
         */
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $productId => $position) {
                $data[] = [
                    'question_id' => (int) $id,
                    'product_id' => (int) $productId,
                ];
            }
            $connection->insertMultiple($this->getTable('md_faq_product'), $data);
        }

        return $this;
    }

    public function saveCategoryRelation($faq) {

        $id = $faq->getId();
        /**
         * new category-question relationships
         */
        $categories = $faq->getCategoryId();
        /**
         * Example re-save category
         */
        if ($categories === null) {
            return $this;
        }

        /**
         * old category-question relationships
         */
        $oldCategory = $this->lookupCategoryIds($faq);
        $insert = array_diff($categories, $oldCategory);
        $delete = array_diff($oldCategory, $categories);
        $connection = $this->getConnection();

        /**
         * Delete question from category
         */
        if ($delete) {
                $where = [
                    'question_id = ?' => (int) $faq->getId(),
                    'category_id IN (?)' => $delete
                ];
                $this->getConnection()->delete($this->getTable('md_category_question'), $where);
        }
        /**
         * Add products to category
         */
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $categoryId) {
                $data[] = [
                    'question_id' => (int) $id,
                    'category_id' => (int) $categoryId,
                ];
            }
            $connection->insertMultiple($this->getTable('md_category_question'), $data);
        }

        return $this;
    }

    /**
     * Get question ids to which specified item is assigned
     *
     * @param int $category
     * @return array
     */
    public function lookupCategoryIds($faq) {
        $select = $this->getConnection()->select()->from(
                        $this->getTable('md_category_question'), ['category_id']
                )->where(
                'question_id = ?', (int) $faq->getId()
        );
        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Get product ids to which specified item is assigned
     *
     * @param int $faqId
     * @return array
     */
    public function lookupProductIds($faq) {
        $select = $this->getConnection()->select()->from(
                        $this->getTable('md_faq_product'), ['product_id']
                )->where(
                'question_id = ?', (int) $faq->getId()
        );
        return $this->getConnection()->fetchCol($select);
    }

    public function saveFaqRelation($faqIds, $questionProductId) {
        $oldProducts = $this->lookupFaqsIds($questionProductId);
        $newProducts = $faqIds;
        if (isset($newProducts)) {
            $table = $this->getTable('md_faq_product');
            $insert = array_diff($newProducts, $oldProducts);
            $delete = array_diff($oldProducts, $newProducts);
            if ($delete) {
                $where = [
                    'product_id = ?' => (int) $questionProductId,
                    'question_id IN (?)' => $delete
                ];
                $this->getConnection()->delete($table, $where);
            }
            if ($insert) {
                $data = [];
                foreach ($insert as $productId) {
                    $data[] = [
                        'question_id' => (int) $productId,
                        'product_id' => (int) $questionProductId
                    ];
                }
                $this->getConnection()->insertMultiple($table, $data);
            }
        }

        return $this;
    }

    public function lookupFaqsIds($productId) {
        $adapter = $this->getConnection();
        $select = $adapter->select()->from($this->getTable('md_faq_product'), 'question_id')
                ->where('product_id = ?', (int) $productId);
        return $adapter->fetchCol($select);
    }

    public function getProducts($object) {
        $tbl = $this->getTable('md_faq_product');
        $select = $this->getConnection()->select()->from(
                        $tbl, ['product_id']
                )
                ->where(
                'question_id = ?', (int) $object->getId()
        );
        return $this->getConnection()->fetchCol($select);
    }
    public function getCategoriesIds($questionId) {
        $tbl = $this->getTable('md_category_question');
        $select = $this->getConnection()->select()->from(
                        $tbl, ['category_id']
                )
                ->where(
                'question_id = ?', (int) $questionId
        );
        return $this->getConnection()->fetchCol($select);
    }
    public function getQuestionIds($categoryId) {
        $tbl = $this->getTable('md_category_question');
        $select = $this->getConnection()->select()->from(
                        $tbl, ['question_id']
                )
                ->where(
                'category_id = ?', (int) $categoryId
        );
        return $this->getConnection()->fetchCol($select);
    }

    public function getFaqIds($productId) {
        $tbl = $this->getTable('md_faq_product');
        $select = $this->getConnection()->select()->from(
                        $tbl, ['question_id']
                )
                ->where(
                'product_id = ?', (int) $productId
        );
        return $this->getConnection()->fetchCol($select);
    }

    public function addProductsids($faqId, $productId) {
        $table = $this->getTable('md_faq_product');
        $data[] = [
            'question_id' => (int) $faqId,
            'product_id' => (int) $productId
        ];
        $this->getConnection()->insertMultiple($table, $data);
        return $this;
    }

}
