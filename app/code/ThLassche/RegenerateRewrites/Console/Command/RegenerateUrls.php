<?php

namespace ThLassche\RegenerateRewrites\Console\Command;

use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Framework\App\State as AppState;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RegenerateUrls.php
 */
class RegenerateUrls extends Command {
	protected $storeManager;
	protected $urlPersist;
	protected $productUrlRewriteGenerator;
	protected $productFactory;
	protected $appState;

	const DEBUG_OPTION = 'debug';


	public function __construct(
		UrlPersistInterface $urlPersist,
		StoreManagerInterface $storeManager,
		ProductUrlRewriteGenerator $productUrlRewriteGenerator,
		ProductFactory $productFactory,
		AppState $appState
	) {
		$this->appState = $appState;
		$this->urlPersist                 = $urlPersist;
		$this->storeManager               = $storeManager;
		$this->productUrlRewriteGenerator = $productUrlRewriteGenerator;
		$this->productFactory             = $productFactory;
		parent::__construct();
	}

	/**
	 * Configure the command
	 */
	protected function configure() {
		$this->setName('thlassche:regenerate_product_urls')->setDescription('Regenerate Url Rewrites for all products');

		$this->addOption(
			self::DEBUG_OPTION,
			'd',
			InputOption::VALUE_NONE,
			'Debug mode'
		);
		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$productCollection = $this->getProductCollection();


		$arrProducts = $productCollection->getItems();

		try {
			$this->appState->setAreaCode('adminhtml');
		} catch (\Exception $ex) {
			# Void, already set
		}


		$progressBar = new ProgressBar($output, count($arrProducts));
		$progressBar->setFormat(
			'%current%/%max% [%bar%] %percent:3s%% %elapsed% %memory:6s%'
		);
		$output->writeln('<info>Started generating URL rewrites.</info>');
		$progressBar->start();

		$debug = $input->getOption(self::DEBUG_OPTION);

		if (!$debug)
			$progressBar->display();



		foreach ($arrProducts as $objProduct)
		{
			// Delete existing
			$this->urlPersist->deleteByData([
				UrlRewrite::ENTITY_ID => $objProduct->getId(),
				UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
				UrlRewrite::REDIRECT_TYPE => 0
   			]);
			foreach ($this->storeManager->getStores() as $store)
			{
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$objProduct = $objectManager->get('Magento\Catalog\Api\ProductRepositoryInterface')->getById($objProduct->getId(), false, $store->getId());

				$saved = false;
				$i = 1;
				do {
					try {
						$i++;
						$arrUrls = $this->productUrlRewriteGenerator->generate($objProduct);

						if ($debug)
						{
							foreach ($arrUrls as $url)
								$output->writeln('Product '.$objProduct->getId().' :: '. $url->getStoreId(). ' :: '. $url->getRequestPath());
						}
						$this->urlPersist->replace($arrUrls);
						$saved = true;
					} catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
						$objProduct->setUrlKey(preg_match('/(.*)-(\d+)$/', $objProduct->getUrlKey(), $matches) ? $matches[1] . '-' . ($matches[2] + 1) : $objProduct->getUrlKey() . '-1');
					}
				} while (!$saved && $i < 10);
			}

			if (!$debug)
				$progressBar->advance();
		}

		$progressBar->finish();
  		$output->writeln('');
  		$output->writeln('<info>Regenerated URL rewrites successfully</info>');
	}

	protected function getProductCollection() {
		return $this->productFactory->create()->getCollection();
	}
}