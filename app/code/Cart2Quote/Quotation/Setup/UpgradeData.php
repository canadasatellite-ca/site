<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 * @codingStandardsIgnoreFile
 */

namespace Cart2Quote\Quotation\Setup;

use Cart2Quote\Quotation\Model\SalesSequence\Config as SequenceConfig;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\SalesSequence\Model\Builder;
use Magento\Store\Model\StoreManagerInterface;
use Quotation\Setup\InstallProducts;

/**
 * Upgrade Data script
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Cart2Quote\Quotation\Setup\QuoteSetup
     */
    protected $quoteSetup;

    /**
     * @var \Cart2Quote\Quotation\Model\SalesSequence\EntityPool
     */
    protected $entityPool;

    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * @var Builder
     */
    protected $sequenceBuilder;

    /**
     * @var SequenceConfig
     */
    protected $sequenceConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Sales setup factory
     *
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    private $configResourceModel;

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $quotationCollectionFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Magento\Catalog\Model\Product\OptionFactory
     */
    protected $productOptionFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $productModel;

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
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Cart2Quote\Quotation\Setup\InstallProducts
     */
    protected $installProducts;

    /**
     * @var \Magento\Store\Model\StoreRepository
     */
    protected $storeRepository;

    /**
     * UpgradeData constructor
     *
     * @param \Cart2Quote\Quotation\Setup\QuoteSetup $quoteSetup
     * @param \Cart2Quote\Quotation\Model\SalesSequence\EntityPool $entityPool
     * @param \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
     * @param \Magento\SalesSequence\Model\Builder $sequenceBuilder
     * @param \Cart2Quote\Quotation\Model\SalesSequence\Config $sequenceConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Cart2Quote\Quotation\Setup\SalesSetupFactory $salesSetupFactory
     * @param \Magento\Config\Model\ResourceModel\Config $configResourceModel
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\State $state
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     * @param \Magento\Framework\Filesystem\Io\File $filesystemIo
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Cart2Quote\Quotation\Setup\InstallProducts $installProducts
     * @param \Magento\Store\Model\StoreRepository $storeRepository
     */
    public function __construct(
        \Cart2Quote\Quotation\Setup\QuoteSetup $quoteSetup,
        \Cart2Quote\Quotation\Model\SalesSequence\EntityPool $entityPool,
        CategorySetupFactory $categorySetupFactory,
        Builder $sequenceBuilder,
        SequenceConfig $sequenceConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        SalesSetupFactory $salesSetupFactory,
        \Magento\Config\Model\ResourceModel\Config $configResourceModel,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\State $state,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Filesystem\Io\File $filesystemIo,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Framework\Filesystem $filesystem,
        \Cart2Quote\Quotation\Setup\InstallProducts $installProducts,
        \Magento\Store\Model\StoreRepository $storeRepository
    ) {
        $this->quoteSetup = $quoteSetup;
        $this->entityPool = $entityPool;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->sequenceBuilder = $sequenceBuilder;
        $this->sequenceConfig = $sequenceConfig;
        $this->storeManager = $storeManager;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->configResourceModel = $configResourceModel;
        $this->quotationCollectionFactory = $collectionFactory;
        $this->logger = $logger;
        $this->objectManager = $objectManager;
        $this->state = $state;
        $this->productOptionFactory = $productOptionFactory;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->productModel = $productModel;
        $this->moduleReader = $moduleReader;
        $this->filesystemIo = $filesystemIo;
        $this->filesystem = $filesystem;
        $this->installProducts = $installProducts;
        $this->storeRepository = $storeRepository;
    }

    /**
     * Upgrade
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Db_Statement_Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.5') < 0) {
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $entityTypeId = $catalogSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $catalogSetup->updateAttribute($entityTypeId, 'cart2quote_quotable', 'is_used_in_grid', true);
            $catalogSetup->updateAttribute($entityTypeId, 'cart2quote_quotable', 'used_in_product_listing', true);
        }

        if (version_compare($context->getVersion(), '1.0.11') < 0) {
            $defaultStoreIds = [0, 1];
            $storeIds = array_keys($this->storeManager->getStores(true));
            foreach ($storeIds as $storeId) {
                if (in_array($storeId, $defaultStoreIds)) {
                    //already done in installData
                    continue;
                }
                foreach ($this->entityPool->getEntities() as $entityType) {
                    $this->sequenceBuilder->setPrefix($this->sequenceConfig->get('prefix'))
                        ->setSuffix($this->sequenceConfig->get('suffix'))
                        ->setStartValue($this->sequenceConfig->get('startValue'))
                        ->setStoreId($storeId)
                        ->setStep($this->sequenceConfig->get('step'))
                        ->setWarningValue($this->sequenceConfig->get('warningValue'))
                        ->setMaxValue($this->sequenceConfig->get('maxValue'))
                        ->setEntityType($entityType)
                        ->create();
                }
            }
        }

        if (version_compare($context->getVersion(), '1.0.19', '<')) {
            /** @var \Magento\Sales\Setup\SalesSetup $salesSetup */
            $salesSetup = $this->salesSetupFactory->create();

            $salesSetup->updateEntityType(
                'quote',
                'entity_model',
                \Cart2Quote\Quotation\Model\ResourceModel\Quote::class
            );
        }

        if (version_compare($context->getVersion(), '1.1.1') < 0) {
            $dataStatuses = [];
            $dataStateStatusRelation = [];

            $changedStatuses = [
                'open' => 'Open',
                'new' => 'Open, New',
                'processing' => 'Open, In Process',
                'change_request' => 'Open, Change Request',
                'holded' => 'On Hold',
                'waiting_supplier' => 'On Hold, Waiting for supplier',
                'canceled' => 'Canceled',
                'out_of_stock' => 'Canceled, Out of Stock',
                'pending' => 'Pending',
                'proposal_sent' => 'Pending, Proposal sent',
                'ordered' => 'Completed, Ordered',
                'accepted' => 'Completed, Accepted',
                'closed' => 'Closed',
                'quote_available' => 'Pending, Quote Available',
                'proposal_expired' => 'Pending, Proposal Expired'
            ];

            $newStatuses = [
                'proposal_sent_completed' => 'Completed, Proposal sent',
                'out_of_stock_holded' => 'On Hold, Out of Stock'
            ];

            $newStatusSortNumbers = [
                'proposal_sent_completed' => 550,
                'out_of_stock_holded' => 650
            ];

            $newStatusStates = [
                'proposal_sent_completed' => 'completed',
                'out_of_stock_holded' => 'holded'
            ];

            asort($changedStatuses);
            $sortNumber = 0;

            foreach ($changedStatuses as $changedStatus => $label) {
                $sortNumber += 100;
                $this->quoteSetup->getConnection()->update(
                    $this->quoteSetup->getTable('quotation_quote_status'),
                    [
                        'label' => $label,
                        'sort' => $sortNumber
                    ],
                    [
                        "status = ?" => $changedStatus
                    ]
                );
            }

            foreach ($newStatuses as $newStatus => $label) {
                $dataStatuses[] = [
                    'status' => $newStatus,
                    'label' => $label,
                    'sort' => $newStatusSortNumbers[$newStatus]
                ];

                $dataStateStatusRelation[] = [
                    'status' => $newStatus,
                    'state' => $newStatusStates[$newStatus],
                    'is_default' => '1',
                    'visible_on_front' => '1'
                ];
            }

            $query = $this->quoteSetup->getConnection()
                ->query('SELECT * FROM ' . $this->quoteSetup->getTable('quotation_quote_status'));
            if ($query->rowCount() == 0) {
                $this->quoteSetup->getConnection()->insertOnDuplicate(
                    $this->quoteSetup->getTable('quotation_quote_status'),
                    $dataStatuses,
                    [
                        'status',
                        'label',
                        'sort'
                    ]
                );
            }

            $query = $this->quoteSetup->getConnection()->query(
                'SELECT * FROM ' . $this->quoteSetup->getTable('quotation_quote_status_state')
            );
            if ($query->rowCount() == 0) {
                $this->quoteSetup->getConnection()->insertOnDuplicate(
                    $this->quoteSetup->getTable('quotation_quote_status_state'),
                    $dataStateStatusRelation,
                    [
                        'status',
                        'state',
                        'is_default',
                        'visible_on_front'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '1.1.2', '<')) {
            /** @var \Magento\Sales\Setup\SalesSetup $salesSetup */
            $salesSetup = $this->salesSetupFactory->create();

            $salesSetup->updateEntityType(
                'quote',
                'increment_pad_length',
                9
            );
        }

        if (version_compare($context->getVersion(), '1.1.3') < 0) {
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $entityTypeId = $catalogSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $catalogSetup->updateAttribute($entityTypeId, 'cart2quote_quotable', 'backend_model', null);
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'cart2quote_quotable',
                'source_model',
                \Cart2Quote\Quotation\Model\Config\Backend\Product\Quotable::class
            );
        }

        if (version_compare($context->getVersion(), '1.2.2') < 0) {
            $dataStatuses = [];
            $dataStateStatusRelation = [];

            $newStatuses = [
                'auto_proposal_sent' => 'Pending, Auto Proposal Sent'
            ];

            $newStatusSortNumbers = [
                'auto_proposal_sent' => 1250
                // The sort number of status "pending" is 1200, the sort number of status "proposal_expired" is 1300.
                // Finally, status "auto_proposal_sent" is inserted between status "pending" and "proposal_expired".
            ];

            $newStatusStates = [
                'auto_proposal_sent' => 'pending'
            ];

            foreach ($newStatuses as $newStatus => $label) {
                $dataStatuses[] = [
                    'status' => $newStatus,
                    'label' => $label,
                    'sort' => $newStatusSortNumbers[$newStatus]
                ];

                $dataStateStatusRelation[] = [
                    'status' => $newStatus,
                    'state' => $newStatusStates[$newStatus],
                    'is_default' => '1',
                    'visible_on_front' => '1'
                ];
            }

            $this->quoteSetup->getConnection()->insertOnDuplicate(
                $this->quoteSetup->getTable('quotation_quote_status'),
                $dataStatuses,
                [
                    'status',
                    'label',
                    'sort'
                ]
            );

            $this->quoteSetup->getConnection()->insertOnDuplicate(
                $this->quoteSetup->getTable('quotation_quote_status_state'),
                $dataStateStatusRelation,
                [
                    'status',
                    'state',
                    'is_default',
                    'visible_on_front'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.0.3') < 0) {
            $changedStatuses = [
                'ordered' => 'Accepted, Ordered',
                'accepted' => 'Accepted',
            ];

            asort($changedStatuses);
            $sortNumber = 0;

            foreach ($changedStatuses as $changedStatus => $label) {
                $sortNumber += 100;
                $this->quoteSetup->getConnection()->update(
                    $this->quoteSetup->getTable('quotation_quote_status'),
                    [
                        'label' => $label,
                        'sort' => $sortNumber
                    ],
                    [
                        "status = ?" => $changedStatus
                    ]
                );
            }

            $this->quoteSetup->getConnection()->update(
                $setup->getTable('core_config_data'),
                ['path' => 'cart2quote_pdf/quote/pdf_footer_text'],
                ['path = ?' => 'cart2quote_quotation/global/pdf_footer_text']
            );
        }

        if (version_compare($context->getVersion(), '2.0.3.1') < 0) {
            $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $entityTypeId = $catalogSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $catalogSetup->updateAttribute($entityTypeId, 'cart2quote_quotable', 'default_value', '2');
        }

        if (version_compare($context->getVersion(), '2.1.1') < 0) {
            $connection = $this->quoteSetup->getConnection();
            $select = $connection->select()
                ->joinLeft(
                    ['join_table' => $this->quoteSetup->getTable('quote')],
                    "`main_table`.`quote_id` = `join_table`.`entity_id`",
                    ['quotation_created_at' => 'created_at']
                );

            $query = $connection->updateFromSelect(
                $select,
                ['main_table' => $this->quoteSetup->getTable('quotation_quote')]
            );
            $connection->query($query);
        }

        if (version_compare($context->getVersion(), '2.1.1.1') < 0) {
            try {
                $this->state->getAreaCode();
            } catch (\Magento\Framework\Exception\SessionException $e) {
                $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
            }

            /** @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $collection */
            $collection = $this->quotationCollectionFactory->create();
            $collection->join(
                ['q' => $this->quoteSetup->getTable('quote')],
                'main_table.quote_id = q.entity_id'
            );
            $collection->addFieldToSelect('main_table.quote_id');
            $collection->addFieldToFilter('main_table.is_quote', ['eq' => 1]);
            $collection->addFieldToFilter('q.is_quotation_quote', ['neq' => true]);
            $ids = $collection->getAllIds();

            if (is_array($ids)) {
                $connection = $this->quoteSetup->getConnection();
                $connection->update(
                    $this->quoteSetup->getTable('quote'),
                    ['is_quotation_quote' => true],
                    ['entity_id IN (?)' => $ids]
                );
            }
        }

        if (version_compare($context->getVersion(), '2.1.6') < 0) {
            $this->quoteSetup->getConnection()->update(
                $setup->getTable('core_config_data'),
                ['value' => 'null'],
                ['path = ?' => 'cart2quote_quote_form_settings/quote_form_settings_configuration/billing_address_grid']
            );
            $this->quoteSetup->getConnection()->update(
                $setup->getTable('core_config_data'),
                ['value' => 'null'],
                ['path = ?' => 'cart2quote_quote_form_settings/quote_form_settings_configuration/shipping_address_grid']
            );
        }

        if (version_compare($context->getVersion(), '2.2.0') < 0) {
            try {
                $this->state->getAreaCode();
            } catch (\Magento\Framework\Exception\SessionException $e) {
                $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
            }

            $this->installProducts->installCustomProduct();
        }

        if (version_compare($context->getVersion(), '2.2.4') < 0) {
            $changedStatuses = [
                'phone_only' => 'Phone Request',
                'print_only' => 'Printed Quote',
            ];

            $sortNumber = 1000;

            foreach ($changedStatuses as $changedStatus => $label) {
                $sortNumber += 10;
                $this->quoteSetup->getConnection()->insertOnDuplicate(
                    $this->quoteSetup->getTable('quotation_quote_status'),
                    [
                        'label' => $label,
                        'sort' => $sortNumber,
                        'status' => $changedStatus
                    ],
                    [
                        'label',
                        'sort'
                    ]
                );
            }

            try {
                $this->state->getAreaCode();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
            }

            $this->installProducts->installCustomForm();
        }

        if (version_compare($context->getVersion(), '3.0.5') < 0) {
            $connection = $this->quoteSetup->getConnection();

            //add proposal_rejected status  (originaly executed in 2.2.7)
            $quotationQuoteStatusTable = $this->quoteSetup->getTable('quotation_quote_status');
            $connection->insertOnDuplicate(
                $quotationQuoteStatusTable,
                [
                    'label' => 'Proposal Rejected By Client',
                    'sort' => '777',
                    'status' => 'proposal_rejected',
                ]
            );

            //add proposal_rejected status_state
            $quotationQuoteStatusStateTable = $this->quoteSetup->getTable('quotation_quote_status_state');
            $connection->insertOnDuplicate(
                $quotationQuoteStatusStateTable,
                [
                    'status' => 'proposal_rejected',
                    'state' => 'canceled',
                    'is_default' => '0',
                    'visible_on_front' => '1'
                ]
            );

            //add phone_only status_state
            $quotationQuoteStatusStateTable = $this->quoteSetup->getTable('quotation_quote_status_state');
            $connection->insertOnDuplicate(
                $quotationQuoteStatusStateTable,
                [
                    'status' => 'phone_only',
                    'state' => 'open',
                    'is_default' => '0',
                    'visible_on_front' => '1'
                ]
            );

            //add print_only status_state
            $quotationQuoteStatusStateTable = $this->quoteSetup->getTable('quotation_quote_status_state');
            $connection->insertOnDuplicate(
                $quotationQuoteStatusStateTable,
                [
                    'status' => 'print_only',
                    'state' => 'open',
                    'is_default' => '0',
                    'visible_on_front' => '1'
                ]
            );
        }

        $setup->endSetup();
    }
}
