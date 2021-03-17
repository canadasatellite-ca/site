<?php
namespace MageSuper\CustomProductCategoryUrl\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use MageSuper\CustomProductCategoryUrl\Model\ProductUrlRewriteGenerator;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Store\Model\Store;

class RegenerateProductUrlCommand extends Command
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
    protected $state;

    public function __construct(
        \Magento\Framework\App\State $state,
        Collection $collection,
        ProductCollection $productCollection,
        ProductUrlRewriteGenerator $productUrlRewriteGenerator,
        UrlPersistInterface $urlPersist
    )
    {
        $this->state = $state;
        $this->productCollection = $productCollection;
        $this->collection = $collection;
        $this->productUrlRewriteGenerator = $productUrlRewriteGenerator;
        $this->urlPersist = $urlPersist;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('magesuper:fixproducturls')
            ->setDescription('Regenerate urls for categories');
        return parent::configure();
    }

    public function execute(InputInterface $inp, OutputInterface $out)
    {
        $this->state->setAreaCode('adminhtml');
        $this->productCollection->getConnection()->query('delete from url_rewrite where entity_type=\'product\'');
        $this->productCollection->addAttributeToSelect(['url_path', 'url_key', 'volusion_url','visibility'])->setStore(1);
        foreach ($this->productCollection as $product) {
            try {
                foreach (array(1, 2) as $storeid) {
                    $product->setStoreId($storeid);
                    if ($product->isVisibleInSiteVisibility()) {
                        $this->urlPersist->replace($this->productUrlRewriteGenerator->generate($product));
                    }
                }
            } catch (\Exception $e) {

            }
        }
    }
}
