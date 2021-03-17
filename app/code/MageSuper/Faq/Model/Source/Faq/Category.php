<?php
namespace MageSuper\Faq\Model\Source\Faq;

use Magento\Framework\App\RequestInterface;
class Category implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magedelight\Faqs\Model\Category
     */
    public $category;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $categoriesTree;

    /**
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request,
        \Magedelight\Faqs\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->request = $request;
    }
    /**
     * Get options
     *
     * @return array
     */
    /*public function toOptionArray()
    {
        $options[] = ['label' => __('Root'), 'value' => '0'];
        $categoryCollection = $this->category->getCollection()
            ->addFieldToSelect('category_id')
            ->addFieldToSelect('title')
            ->addFieldToSelect('parent_id');
        foreach ($categoryCollection as $category) {
            $options[] = [
                'label' => $category->getTitle(),
                'value' => $category->getCategoryId(),
            ];
        }
        return $options;
    }*/

    public function toOptionArray()
    {
        return $this->getCategoriesTree();
    }

    /**
     * Retrieve categories tree
     *
     * @return array
     */
    protected function getCategoriesTree()
    {
        if ($this->categoriesTree === null) {
            $storeId = $this->request->getParam('store');
            /* @var $matchingNamesCollection \Magento\Catalog\Model\ResourceModel\Category\Collection */
            $matchingNamesCollection = $this->categoryCollectionFactory->create();

            $matchingNamesCollection->addFieldToSelect('path');

            $shownCategoriesIds = [];

            /** @var \Magento\Catalog\Model\Category $category */
            foreach ($matchingNamesCollection as $category) {
                foreach (explode('/', $category->getPath()) as $parentId) {
                    $shownCategoriesIds[$parentId] = 1;
                }
            }

            /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
            $collection = $this->categoryCollectionFactory->create();

            $collection->addFieldToFilter('category_id', ['in' => array_keys($shownCategoriesIds)])
                ->addFieldToSelect(['category_id','title', 'parent_id']);

            $categoryById = [
                0 => [
                    'value' => 0
                ],
            ];

            foreach ($collection as $category) {
                foreach ([$category->getCategoryId(), $category->getParentId()] as $categoryId) {
                    if (!isset($categoryById[$categoryId])) {
                        $categoryById[$categoryId] = ['value' => $categoryId];
                    }
                }

                $categoryById[$category->getCategoryId()]['is_active'] = 1;
                $categoryById[$category->getCategoryId()]['label'] = $category->getTitle();
                $categoryById[$category->getParentId()]['optgroup'][] = &$categoryById[$category->getCategoryId()];
            }

            $this->categoriesTree = $categoryById[0]['optgroup'];
        }

        return $this->categoriesTree;
    }

}
