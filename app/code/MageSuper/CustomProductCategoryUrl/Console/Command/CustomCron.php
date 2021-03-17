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

class CustomCron extends Command
{
    protected $state;
    protected $import;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Directory\Model\Observer $import
    ) {
        $this->import = $import;
        $this->state = $state;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('magesuper:customcron')
            ->setDescription('custom');
        return parent::configure();
    }

    public function execute(InputInterface $inp, OutputInterface $out)
    {
        try {
            $this->state->setAreaCode('adminhtml');
            $this->import->scheduledUpdateCurrencyRates(null);
        }
        catch(\Exception $e) {
            $out->writeln('<error>Duplicated url for '. $e->getMessage() .'</error>');
        }
    }
}
