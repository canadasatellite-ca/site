<?php
namespace Magedelight\Faqs\Model\ResourceModel;
use Magento\Framework\Model\AbstractModel;

class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    // @codingStandardsIgnoreStart
    protected function _construct()
    {
        $this->_init('md_categories', 'category_id');
    }
    // @codingStandardsIgnoreEnd
    
    protected function _afterSave(AbstractModel $object)
    {
        $this->saveProductRelation($object);
        return parent::_afterSave($object);
    }
    
    public function saveProductRelation($category)
    {    
        
        $id = $category->getId();

        /**
         * new category-product relationships
         */
        $questions = $category->getPostedQuestion();
        /**
         * Example re-save category
         */
        if ($questions === null) {
            return $this;
        }

        /**
         * old category-question relationships
         */
        $oldQuestions = $this->lookupProductIds($category);
        
        if(count($oldQuestions) > 0)
        {
        $oldQuestions = array_flip($oldQuestions);
        }
        else
        {
        $oldQuestions = $oldQuestions;
        }
        $insert = array_diff_key($questions, $oldQuestions);
        $delete = array_diff_key($oldQuestions, $questions);
        $connection = $this->getConnection();

        /**
         * Delete question from category
         */
        if (!empty($delete)) {
            $cond = ['question_id IN(?)' => array_keys($delete), 'category_id=?' => $id];
            $connection->delete($this->getTable('md_category_question'), $cond);
        }
        /**
         * Add products to category
         */
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $categoryId => $position) {
                $data[] = [
                    'category_id' => (int)$id,
                    'question_id' => (int)$categoryId,
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
    public function lookupProductIds($category)
    {   
        $select = $this->getConnection()->select()->from(
            $this->getTable('md_category_question'),
            ['question_id']
        )->where(
            'category_id = ?', (int) $category->getId()
        );
        return $this->getConnection()->fetchCol($select);
    }
}
