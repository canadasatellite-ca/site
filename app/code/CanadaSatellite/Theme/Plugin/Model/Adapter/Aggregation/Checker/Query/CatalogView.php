<?php
namespace CanadaSatellite\Theme\Plugin\Model\Adapter\Aggregation\Checker\Query;

use Magento\CatalogSearch\Model\Adapter\Aggregation\Checker\Query\CatalogView as QuerryCatalogView;
use Magento\Framework\Search\Request;
use Magento\Framework\Search\Request\QueryInterface;
use Magento\Framework\Search\Request\Query\BoolExpression;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class CatalogView
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StoreManagerInterface $storeManager
     */
    function __construct(
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
    }

    function afterIsApplicable(QuerryCatalogView $subject, $result, Request $request)
    {
        if (!$result){
            $queryType = $request->getQuery()->getType();

            if ($queryType === QueryInterface::TYPE_BOOL) {
                $categories = $this->getCategoriesFromQuery($request->getQuery(), $subject);

                /** @var \Magento\Catalog\Api\Data\CategoryInterface $category */
                foreach ($categories as $category) {
                    if ($category->getLayeredNavigation()) {
                        $result = true;
                        break;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get categories based on query filter data.
     *
     * Get categories from query will allow to check if category is anchor
     * And proceed with attribute aggregation if it's not
     *
     * @param QueryInterface $queryExpression
     * @return \Magento\Catalog\Api\Data\CategoryInterface[]|[]
     */
    private function getCategoriesFromQuery(QueryInterface $queryExpression, $subject)
    {
        /** @var BoolExpression $queryExpression */
        $categoryIds = $this->getCategoryIdsFromQuery($queryExpression);
        $categories = [];

        foreach ($categoryIds as $categoryId) {
            try {
                $categories[] = $this->categoryRepository
                    ->get($categoryId, $this->storeManager->getStore()->getId());
            } catch (NoSuchEntityException $e) {
                // do nothing if category is not found by id
            }
        }

        return $categories;
    }

    /**
     * Get Category Ids from search query.
     *
     * Get Category Ids from Must and Should search queries.
     *
     * @param QueryInterface $queryExpression
     * @return array
     */
    private function getCategoryIdsFromQuery(QueryInterface $queryExpression)
    {
        $queryFilterArray = [];
        /** @var BoolExpression $queryExpression */
        $queryFilterArray[] = $queryExpression->getMust();
        $queryFilterArray[] = $queryExpression->getShould();
        $categoryIds = [];

        foreach ($queryFilterArray as $item) {
            if (!empty($item) && isset($item['category'])) {
                $queryFilter = $item['category'];
                /** @var \Magento\Framework\Search\Request\Query\Filter $queryFilter */
                $categoryIds[] = $queryFilter->getReference()->getValue();
            }
        }

        return $categoryIds;
    }
}