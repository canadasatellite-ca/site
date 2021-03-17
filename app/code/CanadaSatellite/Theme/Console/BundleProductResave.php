<?php

namespace CanadaSatellite\Theme\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class BundleProductResave extends Command
{

    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    const COMMAND_RESAVE_BUNDLE = "product:bundle:resave";

    const BUNDLE_PRODUCT_ID = 'Id';

    public function __construct(
        \Magento\Framework\App\State $appState,
        CollectionFactory $productCollectionFactory,
        ProductFactory $productFactory,
        $name = null
    ){
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->appState = $appState;
        $this->productFactory = $productFactory;
        parent::__construct($name);
    }

    protected function configure()
    {
        $commandOptions = [new InputOption(self::BUNDLE_PRODUCT_ID, null, InputOption::VALUE_REQUIRED, 'Id')];

        $this->setName(self::COMMAND_RESAVE_BUNDLE);
        $this->setDescription('Resave bundle products');
        $this->setDefinition($commandOptions);
        parent::configure();
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appState->setAreaCode('adminhtml');
        if ($bundleProductId = $input->getOption(self::BUNDLE_PRODUCT_ID)){
            $collection = $this->_productCollectionFactory->create();
            $collection
                ->addAttributeToSelect('*')
                ->addFieldToFilter('type_id', array('eq' => 'bundle'));
            foreach ($collection as $product){
                $arrayIds[] = (int)$product->getId();
            }
            if (in_array($bundleProductId, $arrayIds)) {
                $bundleProduct = $this->productFactory->create();
                $bundleProduct->load($bundleProductId);
                $bundleProduct->save();
                $output->writeln('Bundle product ' . $bundleProductId . ' was successfully resaved');
            } else {
                $output->writeln('Product with this ID: ' . $bundleProductId . ' is not bundle');
            }
        } else {
            $output->writeln('Please enter bundle product ID');
        }
    }
}