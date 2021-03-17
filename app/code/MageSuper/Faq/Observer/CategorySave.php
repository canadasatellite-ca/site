<?php

namespace MageSuper\Faq\Observer;

use Magento\Framework\Event\ObserverInterface;

class CategorySave implements ObserverInterface
{
    public $request;

    public $scopeConfig;

    public $objectFactory;

    public $faq;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig,
        \Magento\Framework\ObjectManagerInterface $objectFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magedelight\Faqs\Model\ResourceModel\Faq $faq
    )
    {

        $this->objectFactory = $objectFactory;
        $this->request = $request;
        $this->scopeConfig = $_scopeConfig;
        $this->faq = $faq;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $object = $observer->getDataObject();
        if (isset($object->eventPrefix) && $object->eventPrefix == 'faqs' && isset($object->eventObject) && $object->eventObject == 'category') {
            $connection = $this->faq->getConnection();
            if ($object->getId() == $object->getParentId()) {
                $cond = ['category_id=?' => $object->getId()];
                $connection->update($this->faq->getTable('md_categories'), array('parent_id' => 0, 'path' => '0/' . $object->getId()), $cond);
            } else {
                $parentId = $object->getParentId();
                $parent_path = '0';
                if ($parentId) {
                    $parent_path = $connection->fetchOne('SELECT path from md_categories where category_id=' . $parentId);
                }
                $cond = ['category_id=?' => $object->getId()];
                $connection->update($this->faq->getTable('md_categories'), array('path' => $parent_path . '/' . $object->getId()), $cond);
            }

            $this->saveQcategoryProducts($object);
            $question = $object;
            $id = $question->getId();

            /**
             * new category-product relationships
             */
            $pcategories = $question->getData('pcategory_ids');
            /**
             * Example re-save category
             */
            if ($pcategories === null) {
                $pcategories = [];
            }

            /**
             * old category-question relationships
             */
            $oldPcategories = $this->lookupPcategoryIds($question);

            if (count($oldPcategories) > 0) {
                $oldPcategories = array_flip($oldPcategories);
            } else {
                $oldPcategories = $oldPcategories;
            }
            $insert = array_diff_key($pcategories, $oldPcategories);
            $delete = array_diff_key($oldPcategories, $pcategories);


            /**
             * Delete question from category
             */
            if (!empty($delete)) {
                $cond = ['pcategory_id IN(?)' => array_keys($delete), 'category_id=?' => $id];
                $connection->delete($this->faq->getTable('md_pcategory_category'), $cond);
            }
            /**
             * Add products to category
             */
            if (!empty($insert)) {
                $data = [];
                foreach ($insert as $categoryId) {
                    $data[] = [
                        'category_id' => (int)$id,
                        'pcategory_id' => (int)$categoryId,
                    ];
                }

                $connection->insertMultiple($this->faq->getTable('md_pcategory_category'), $data);
            }

            return $this;
        }

        if (isset($object->eventPrefix) && $object->eventPrefix == 'faqs' && isset($object->eventObject) && $object->eventObject == 'faq') {
            $question = $object;
            $id = $question->getId();

            /**
             * new category-product relationships
             */
            $pcategories = $question->getData('pcategory_ids');
            /**
             * Example re-save category
             */
            if ($pcategories === null) {
                $pcategories = [];
            }

            /**
             * old category-question relationships
             */
            $oldPcategories = $this->lookupQuestionPcategoryIds($question);

            if (count($oldPcategories) > 0) {
                $oldPcategories = array_flip($oldPcategories);
            } else {
                $oldPcategories = $oldPcategories;
            }
            $insert = array_diff_key($pcategories, $oldPcategories);
            $delete = array_diff_key($oldPcategories, $pcategories);
            $connection = $this->faq->getConnection();

            /**
             * Delete question from category
             */
            if (!empty($delete)) {
                $cond = ['pcategory_id IN(?)' => array_keys($delete), 'question_id=?' => $id];
                $connection->delete($this->faq->getTable('md_pcategory_question'), $cond);
            }
            /**
             * Add products to category
             */
            if (!empty($insert)) {
                $data = [];
                foreach ($insert as $categoryId) {
                    $data[] = [
                        'question_id' => (int)$id,
                        'pcategory_id' => (int)$categoryId,
                    ];
                }

                $connection->insertMultiple($this->faq->getTable('md_pcategory_question'), $data);
            }

            return $this;
        }
    }

    public function lookupPcategoryIds($category)
    {
        $select = $this->faq->getConnection()->select()->from(
            $this->faq->getTable('md_pcategory_category'),
            ['pcategory_id']
        )->where(
            'category_id = ?', (int)$category->getId()
        );
        return $this->faq->getConnection()->fetchCol($select);
    }

    protected function saveQcategoryProducts($category)
    {
        $data = $this->request->getPostValue();
        $products = $category->getPostedProducts();
        if (isset($data['category_products'])
            && is_string($data['category_products'])
        ) {
            $products = json_decode($data['category_products'], true);
            if (isset($products['on'])) {
                unset($products['on']);
            }
            $category->setPostedProducts($products);
        }

        $id = $category->getId();

        /**
         * Example re-save category
         */
        if ($products === null) {
            return $this;
        }

        /**
         * old category-question relationships
         */
        $oldProducts = $this->lookupQcategoryProductsIds($category);

        if (count($oldProducts) > 0) {
            $oldProducts = array_flip($oldProducts);
        } else {
            $oldProducts = $oldProducts;
        }
        $insert = array_diff_key($products, $oldProducts);
        $delete = array_diff_key($oldProducts, $products);
        $connection = $this->faq->getConnection();

        /**
         * Delete question from category
         */
        if (!empty($delete)) {
            $cond = ['product_id IN(?)' => array_keys($delete), 'category_id=?' => $id];
            $connection->delete($this->faq->getTable('md_category_product'), $cond);
        }
        /**
         * Add products to category
         */
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $productId => $position) {
                $data[] = [
                    'category_id' => (int)$id,
                    'product_id' => (int)$productId,
                ];
            }

            $connection->insertMultiple($this->faq->getTable('md_category_product'), $data);
        }

    }

    public function lookupQcategoryProductsIds($category)
    {
        $select = $this->faq->getConnection()->select()->from(
            $this->faq->getTable('md_category_product'),
            ['product_id']
        )->where(
            'category_id = ?', (int)$category->getId()
        );
        return $this->faq->getConnection()->fetchCol($select);
    }

    public function lookupQuestionPcategoryIds($question)
    {
        $select = $this->faq->getConnection()->select()->from(
            $this->faq->getTable('md_pcategory_question'),
            ['pcategory_id']
        )->where(
            'question_id = ?', (int)$question->getId()
        );
        return $this->faq->getConnection()->fetchCol($select);
    }
}
