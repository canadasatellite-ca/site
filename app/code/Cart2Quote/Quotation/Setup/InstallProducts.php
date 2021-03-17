<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Setup;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class InstallProducts
 * @package Cart2Quote\Quotation\Setup
 */
class InstallProducts extends UpgradeData
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $moduleReader;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $filesystemIo;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\OptionFactory
     */
    protected $productOptionFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $productModel;

    /**
     * @var \Magento\Store\Model\ResourceModel\Website\CollectionFactory
     */
    protected $websiteCollectionFactory;

    /**
     * UpgradeData constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     * @param \Magento\Framework\Filesystem\Io\File $filesystemIo
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Filesystem\Io\File $filesystemIo,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory
    ) {
        $this->storeManager = $storeManager;
        $this->objectManager = $objectManager;
        $this->moduleReader = $moduleReader;
        $this->filesystemIo = $filesystemIo;
        $this->productFactory = $productFactory;
        $this->productOptionFactory = $productOptionFactory;
        $this->productRepository = $productRepository;
        $this->productModel = $productModel;
        $this->websiteCollectionFactory = $websiteCollectionFactory;
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function installCustomProduct()
    {

        if (!$this->productModel->getIdBySku('custom-product')) {
            $options = [];

            $options[] = [
                'title' => 'name',
                'type' => 'field',
                'is_require' => true,
                'sort_order' => 1,
                'price' => 0,
                'price_type' => 'fixed',
                'max_characters' => 50,
            ];

            $options[] = [
                'title' => 'sku',
                'type' => 'field',
                'is_require' => true,
                'sort_order' => 2,
                'price' => 0,
                'price_type' => 'fixed',
                'max_characters' => 50,
            ];

            $options[] = [
                'title' => 'price',
                'type' => 'field',
                'is_require' => true,
                'sort_order' => 3,
                'price' => 0,
                'price_type' => 'fixed',
                'max_characters' => 50,
            ];

            $viewDir = $this->moduleReader->getModuleDir(
                \Magento\Framework\Module\Dir::MODULE_VIEW_DIR,
                'Cart2Quote_Quotation'
            );

            $imagePath = $viewDir . '/adminhtml/web/images/custom_product.png';
            if (file_exists($imagePath)) {
                /* @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                $mediaDirectory = $this->objectManager->get(\Magento\Framework\Filesystem::class)
                    ->getDirectoryRead(DirectoryList::MEDIA);
                $copyToPath = $mediaDirectory->getAbsolutePath() . 'quotation/custom_product.png';
                $this->filesystemIo->cp($imagePath, $copyToPath);
            }

            /* @var \Magento\Catalog\Model\Product $customProduct */
            $customProduct = $this->productFactory->create();
            $customProduct->setName('Custom Product')
                ->setSku('custom-product')
                ->setPrice(0)
                ->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
                ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE)
                ->setAttributeSetId($customProduct->getDefaultAttributeSetId())
                ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                ->setStockData(
                    [
                        'use_config_manage_stock' => 0,
                        'manage_stock' => 0
                    ]
                );

            if (isset($copyToPath) && file_exists($copyToPath)) {
                $customProduct->addImageToMediaGallery(
                    $copyToPath,
                    [
                        'image',
                        'small_image',
                        'thumbnail'
                    ],
                    false,
                    false
                );
            }

            $customProduct->save();

            foreach ($options as $option) {
                $customOption = $this->productOptionFactory->create(['data' => $option]);
                $customOption->setProductSku($customProduct->getSku());
                $customOptions[] = $customOption;
            }

            if (isset($customOptions)) {
                $product = $this->productRepository->getById($customProduct->getId());
                $product->setCanSaveCustomOptions(true)
                    ->setOptions($customOptions)
                    ->setHasOptions(true)
                    ->save();
            }
        }
    }

    /**
     * @return \Magento\Store\Model\ResourceModel\Website\Collection
     */
    public function getWebsiteCollection() {
        return $this->websiteCollectionFactory->create();

    }

    /**
     * Upgrade
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function installCustomForm()
    {

        if (!$this->productModel->getIdBySku('custom-request-form')) {
            $options = [];

            $options[] = [
                'title' => 'Product Description:',
                'type' => 'area',
                'is_require' => true,
                'sort_order' => 1,
                'price' => 0,
                'price_type' => 'fixed',
            ];

            $options[] = [
                'title' => 'Special Features:',
                'type' => 'area',
                'is_require' => false,
                'sort_order' => 2,
                'price' => 0,
                'price_type' => 'fixed',
            ];

            $options[] = [
                'title' => 'Remarks:',
                'type' => 'area',
                'is_require' => false,
                'sort_order' => 3,
                'price' => 0,
                'price_type' => 'fixed',
            ];

            $options[] = [
                'title' => 'Quantity:',
                'type' => 'area',
                'is_require' => true,
                'sort_order' => 4,
                'price' => 0,
                'price_type' => 'fixed',
            ];

            $options[] = [
                'title' => 'Required By:',
                'type' => 'date',
                'is_require' => false,
                'sort_order' => 5,
                'price' => 0,
                'price_type' => 'fixed',
            ];

            $viewDir = $this->moduleReader->getModuleDir(
                \Magento\Framework\Module\Dir::MODULE_VIEW_DIR,
                'Cart2Quote_Quotation'
            );

            $imagePath = $viewDir . '/adminhtml/web/images/request_form.png';
            if (file_exists($imagePath)) {
                /* @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                $mediaDirectory = $this->objectManager->get(\Magento\Framework\Filesystem::class)
                    ->getDirectoryRead(DirectoryList::MEDIA);
                $copyToPath = $mediaDirectory->getAbsolutePath() . 'quotation/request_form.png';
                $this->filesystemIo->cp($imagePath, $copyToPath);
            }

            /* @var \Magento\Catalog\Model\Product $customProduct */
            $customFormRequest = $this->productFactory->create();
            $customFormRequest->setName('Custom Request Form')
                ->setSku('custom-request-form')
                ->setPrice(0)
                ->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL)
                ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG)
                ->setAttributeSetId($customFormRequest->getDefaultAttributeSetId())
                ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                ->setWebsiteIds($this->getWebsiteCollection()->getAllIds())
                ->setStockData(
                    [
                        'use_config_manage_stock' => 0,
                        'manage_stock' => 0,
                        'is_in_stock' => 1,
                        'qty' => 1
                    ]
                );

            if (isset($copyToPath) && file_exists($copyToPath)) {
                $customFormRequest->addImageToMediaGallery(
                    $copyToPath,
                    [
                        'image',
                        'small_image',
                        'thumbnail'
                    ],
                    false,
                    false
                );
            }

            $customFormRequest->save();

            foreach ($options as $option) {
                $customOption = $this->productOptionFactory->create(['data' => $option]);
                $customOption->setProductSku($customFormRequest->getSku());
                $customOptions[] = $customOption;
            }

            if (isset($customOptions)) {
                $product = $this->productRepository->getById($customFormRequest->getId());
                $product->setCanSaveCustomOptions(true)
                    ->setOptions($customOptions)
                    ->setHasOptions(true)
                    ->save();
            }
        }
    }
}
