<?php
namespace Magedelight\Faqs\Model\Source\Faq;
 
class Category implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magedelight\Faqs\Model\Category
     */
    public $category;
 
    /**
     * Constructor
     *
     * @param \Magedelight\Faqs\Model\Category $category
     */
    public function __construct(\Magedelight\Faqs\Model\Category $category)
    {
        $this->category = $category;
    }
 
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => __('Select FAQ Category'), 'value' => ''];
        $categoryCollection = $this->category->getCollection()
            ->addFieldToSelect('category_id')
            ->addFieldToSelect('title');
        foreach ($categoryCollection as $category) {
            $options[] = [
                'label' => $category->getTitle(),
                'value' => $category->getCategoryId(),
            ];
        }
        return $options;
    }
}
