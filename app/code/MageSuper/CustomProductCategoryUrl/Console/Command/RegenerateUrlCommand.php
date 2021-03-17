<?php
namespace MageSuper\CustomProductCategoryUrl\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Store\Model\Store;

class RegenerateUrlCommand extends Command
{
    /**
     * @var ProductUrlRewriteGenerator
     */
    protected $productUrlRewriteGenerator;

    /**
     * @var UrlPersistInterface
     */
    protected $urlPersist;

    /**
     * @var ProductRepositoryInterface
     */
    protected $collection;
    protected $productCollection;

    public function __construct(
        \Magento\Framework\App\State $state,
        Collection $collection,
        ProductCollection $productCollection,
        ProductUrlRewriteGenerator $productUrlRewriteGenerator,
        UrlPersistInterface $urlPersist
    ) {
        $this->productCollection = $productCollection;
        $this->collection = $collection;
        $this->productUrlRewriteGenerator = $productUrlRewriteGenerator;
        $this->urlPersist = $urlPersist;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('magesuper:fixcategoryurls')
            ->setDescription('Regenerate urls for categories');
        return parent::configure();
    }

    public function execute(InputInterface $inp, OutputInterface $out)
    {

        $this->collection->addAttributeToSelect(['url_path', 'url_key']);
        $list = $this->collection->load();
        $fixDuplication = [];
        foreach($list as $category)
        {
            try {
                if($category->getLevel()<2){
                    continue;
                }
                $url_key = $category->getUrlKey();
                if(isset($fixDuplication[$url_key])){
                    $category->setUrlKey($url_key.'-'.$category->getEntityId());
                    $category->setUrlPath($url_key.'-'.$category->getEntityId());
                    $category->getResource()->saveAttribute($category, 'url_key');
                    $category->getResource()->saveAttribute($category, 'url_path');
                    $category->save();
                }
                $fixDuplication[$url_key] = true;
            }
            catch(\Exception $e) {
                $out->writeln('<error>Duplicated url for '. $category->getEntityId() .'</error>');
            }
        }
        $this->productCollection->addAttributeToSelect(['url_path', 'url_key']);
        foreach($this->productCollection as $product){
            $url_key= $product->getUrlKey();
            if(isset($fixDuplication[$url_key])){
                $product->setUrlKey($url_key.'-'.$product->getEntityId());
                $product->setUrlPath($url_key.'-'.$product->getEntityId());
                $product->getResource()->saveAttribute($product, 'url_key');
                $product->getResource()->saveAttribute($product, 'url_path');
            }
            $fixDuplication[$url_key] = true;
        }
    }
}
