<?php

namespace CanadaSatellite\Theme\Plugin\Block\Product\Compare;

use Magento\Catalog\Block\Product\Compare\ListCompare as ParentListCompare;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductFactory;
use Magento\Customer\Model\Visitor;

class ListCompare
{
    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * Customer visitor
     *
     * @var Visitor
     */
    protected $_customerVisitor;

    public function __construct(
        ProductFactory $productFactory,
        Visitor $customerVisitor
    ) {
        $this->_productFactory = $productFactory;
        $this->_customerVisitor = $customerVisitor;
    }

    public function afterGetItems(ParentListCompare $subject, $result)
    {
        if ($this->_customerVisitor->getId() == null || (isset($_SESSION['catalog']['is_incognito']) && $_SESSION['catalog']['is_incognito'] == 1)) {

            $sessionIds = [];

            if (isset($_SESSION['catalog']['compare_ids'])) {
                $sessionIds = $_SESSION['catalog']['compare_ids'];
            }

            $goodArray = explode(',', $subject->getRequest()->getParam('items'));
            $goodArray = array_unique(array_merge_recursive($sessionIds, $goodArray));

            $resultCollection = $this->getProductsById($goodArray);

            foreach ($result->getItems() as $productId => $productValue) {
                $result->removeItemByKey($productId);
            }

            foreach ($resultCollection as $item) {
                $result->addItem($item);
            }

            $_SESSION['catalog']['compare_ids'] = $goodArray;
            $_SESSION['catalog']['is_incognito'] = 1;
        }

        return $result;
    }

    private function getProductsById($productIds) {
        $productCollection = $this->_productFactory->create();
        $resultCollection = $productCollection->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', ['in' => $productIds])
            ->load();
        return $resultCollection;
    }
}
