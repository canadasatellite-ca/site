<?php


namespace MageSuper\CustomProductCategoryUrl\Observer\Catalog;

use Magento\Catalog\Model\Product;
use MageSuper\CustomProductCategoryUrl\Model\ProductUrlRewriteGenerator;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Framework\Event\ObserverInterface;

class ProductSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var ProductUrlRewriteGenerator
     */
    protected $productUrlRewriteGenerator;

    /**
     * @var UrlPersistInterface
     */
    protected $urlPersist;
    protected $indexerRegistry;

    /**
     * @param ProductUrlRewriteGenerator $productUrlRewriteGenerator
     * @param UrlPersistInterface $urlPersist
     */
    public function __construct(
        ProductUrlRewriteGenerator $productUrlRewriteGenerator,
        UrlPersistInterface $urlPersist,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
    ) {
        $this->productUrlRewriteGenerator = $productUrlRewriteGenerator;
        $this->urlPersist = $urlPersist;
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getEvent()->getProduct();

        if ($product->dataHasChangedFor('url_key')
            || $product->getIsChangedCategories()
            || $product->getIsChangedWebsites()
            || $product->dataHasChangedFor('visibility')
            || $product->dataHasChangedFor('volusion_url')
            || $product->getData('volusion_url')
        ) {
            $this->urlPersist->deleteByData([
                UrlRewrite::ENTITY_ID => $product->getId(),
                UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
                UrlRewrite::REDIRECT_TYPE => 0,
                UrlRewrite::STORE_ID => $product->getStoreId()
            ]);

            if ($product->isVisibleInSiteVisibility()) {
                $this->urlPersist->replace($this->productUrlRewriteGenerator->generate($product));
            }
        }
        $this->updatePosition($product);
        //$this->reindexCategoryProduct($product);
    }
    public function updatePosition($product){
        /** @var \Magento\Catalog\Model\Product $product */
        $productId = $product->getEntityId();
        # 2021-03-22 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		# "«A non-numeric value encountered
		# in app/code/MageSuper/CustomProductCategoryUrl/Observer/Catalog/ProductSaveAfter.php on line 73»
		# on saving a new product in the Magento backend": https://github.com/canadasatellite-ca/site/issues/31
        $position = 2 - (int)$product->getData('mst_search_weight');

        $connection = $product->getResource()->getConnection();

        $tableName = $connection->getTableName('catalog_category_product');
        $sql = "UPDATE ".$tableName. " SET position='".$position."' WHERE product_id='".$productId."'";
        $connection->query($sql);

        $tableName = $connection->getTableName('catalog_category_product_index');
        $sql = "UPDATE ".$tableName. " SET position='".$position."' WHERE product_id='".$productId."'";
        $connection->query($sql);

        return $this;
    }
    /**
     * Retrieve category link repository instance
     *
     * @return \Magento\Catalog\Api\CategoryLinkRepositoryInterface
     */
    private function getCategoryLinkRepository()
    {
        if (null === $this->categoryLinkRepository) {
            $this->categoryLinkRepository = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Catalog\Api\CategoryLinkRepositoryInterface');
        }
        return $this->categoryLinkRepository;
    }
    protected function reindexCategoryProduct($product){
        $productCategoryIndexer = $this->indexerRegistry->get(\Magento\Catalog\Model\Indexer\Product\Category::INDEXER_ID);
        $productCategoryIndexer->reindexRow($product->getId());
        /*$productCategoryIndexer = $this->indexerRegistry->get(\Magento\CatalogInventory\Model\Indexer\Stock\Processor::INDEXER_ID);
        $productCategoryIndexer->reindexRow($product->getId());*/
    }
}
