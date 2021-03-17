<?php

namespace MageSuper\Faq\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;

/**
 * Faq post mysql resource
 * @method array|null getProductsData()
 */
class Faq extends \Magedelight\Faqs\Model\ResourceModel\Faq {

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
    public function getFaqIds($productId) {
        $tbl = $this->getTable('md_faq_product');
        $select = $this->getConnection()->select()->from(
                        $tbl, ['question_id']
                )
                ->where(
                'product_id = ?', (int) $productId
        );
        $questionIds = $this->getConnection()->fetchCol($select);

        $tbl = $this->getTable('md_category_question');
        $select = $this->getConnection()->select()->from(
            array('main_table'=>$tbl), ['question_id']
        )->joinRight(array('pcp'=>$this->getTable('md_category_product')),'pcp.category_id=main_table.category_id')
            ->where(
                'pcp.product_id = ?', (int) $productId
            );
        $questionIds2 = $this->getConnection()->fetchCol($select);
        $allIds = array_merge($questionIds,$questionIds2);
        $allIds = array_unique($allIds);
        return $allIds;
    }
    public function getCategoryFaqIds($categoryId) {
        $tbl = $this->getTable('md_pcategory_question');
        $select = $this->getConnection()->select()->from(
            $tbl, ['question_id']
        )
            ->where(
                'pcategory_id = ?', (int) $categoryId
            );
        $questionIds = $this->getConnection()->fetchCol($select);

        $tbl = $this->getTable('md_category_question');
        $select = $this->getConnection()->select()->from(
            array('main_table'=>$tbl), ['question_id']
        )->joinRight(array('pcc'=>$this->getTable('md_pcategory_category')),'pcc.category_id=main_table.category_id')
            ->where(
                'pcc.pcategory_id = ?', (int) $categoryId
            );
        $questionIds2 = $this->getConnection()->fetchCol($select);
        $allIds = array_merge($questionIds,$questionIds2);
        $allIds = array_unique($allIds);
        return $allIds;
    }

}
