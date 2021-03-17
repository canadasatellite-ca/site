<?php
namespace Magedelight\Faqs\Model\Config\Source;
 
class Layout implements \Magento\Framework\Data\OptionSourceInterface
{
    
    const Category_view_list = 1;

    const Category_view_grid = 2;
    
    const Category_and_question_List_view = 3;
    
    const Category_and_question_Grid_view = 4;
    
    public static function getOptionArray()
    {
        return [
            self::Category_view_list => __('Category List View'),
            self::Category_view_grid => __('Category Grid View'),
            self::Category_and_question_List_view => __('Category and Question List View'),
            self::Category_and_question_Grid_view => __('Category and Question Grid View')
        ];
    }
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        foreach ($this->getOptionArray() as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
