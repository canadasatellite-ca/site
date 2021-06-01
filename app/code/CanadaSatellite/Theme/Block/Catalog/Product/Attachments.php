<?php
namespace CanadaSatellite\Theme\Block\Catalog\Product;

use Magento\Framework\View\Element\Template;
use MageWorx\Downloads\Model\Attachment;
use MageWorx\Downloads\Model\Attachment\Product as AttachmentProduct;
use MageWorx\Downloads\Helper\Data as HelperData;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Attachments extends  \CanadaSatellite\Theme\Block\Catalog\AttachmentContainer
{
    const LIMIT_QTY_LINKS = 'mageworx_downloads/main/limit_qty_links';

    private $attachmentCollection;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     *
     * @var \MageWorx\Downloads\Model\Attachment\Product
     */
    protected $attachmentProduct;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Framework\Registry $registry
     * @param HelperData $helperData
     * @param AttachmentProduct $attachmentProduct
     * @param array $data
     */
    function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Registry $registry,
        HelperData $helperData,
        AttachmentProduct $attachmentProduct,
        ScopeConfigInterface $scopeConfig,
        \MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory,
        \MageWorx\Downloads\Model\ResourceModel\Section\CollectionFactory $sectionCollectionFactory,
        \MageWorx\Downloads\Model\AttachmentFactory $attachmentFactory,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->attachmentProduct = $attachmentProduct;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($helperData, $httpContext, $context, $attachmentCollectionFactory, $sectionCollectionFactory, $attachmentFactory, $data);
        $this->setTabTitle();
    }

    function getPdfUrl()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (!$orderId) {
            return false;
        }
        return $this->_urlBuilder->getBaseUrl() . 'sales/order/view/order_id/' . $orderId;
    }

    /**
     * @param boolean $isUseCustomerGroupFilter
     * @return \MageWorx\Downloads\Model\ResourceModel\Attachment\Collection
     */
    function getAttachmentCollection($isUseCustomerGroupFilter = true)
    {
        $id = $this->getProductId();
        if (!$id) {
            $id = $this->getData('product_id');
            if ($this->getProductItem() && $productId = $this->getProductItem()->getProductId()) {
                $id = $productId;
            }
        }
        $collection = $this->attachmentProduct->getSelectedAttachmentsCollection($id);
        $collection->addFieldToFilter('is_active', Attachment::STATUS_ENABLED);
        $collection->addFieldToFilter('section_table.is_active', \MageWorx\Downloads\Model\Section::STATUS_ENABLED);
        $collection->addFieldToFilter('is_visible_top', Attachment::STATUS_ENABLED);
        $collection->addStoreFilter($this->_storeManager->getStore()->getId());
        if ($isUseCustomerGroupFilter) {
            $collection->addCustomerGroupFilter($this->getCustomerGroupId());
        }
        $collection->addSortOrder();
        $collection->getSelect()->order('name');
        $collection->setPageSize($this->getLimitQtyLinks());
        $collection->load();
        $this->attachmentCollection = $collection;
        return $this->attachmentCollection;
    }

    /**
     * Retrieve array of attachment object that allow for view
     *
     * @return array
     */
    function getAttachments()
    {
        $this->attachments = [];
        if ($this->helperData->isHideFiles()) {
            $collection = $this->getAttachmentCollection(true);
            $inGroupIds = $collection->getAllIds();
        } else {
            $collection = $this->getAttachmentCollection(false);
            $inGroupIds = $this->getAttachmentCollection(true)->getAllIds();
        }

        foreach ($collection->getItems() as $item) {
            if (!$this->isAllowByCount($item)) {
                continue;
            }

            if ($this->isAllowByCustomerGroup($item, $inGroupIds)) {
                $item->setIsInGroup('1');
            } else {
                $this->isHasNotAllowedLinks = true;
            }

            $this->attachments[] = $item;
        }

        return $this->attachments;
    }

    /**
     * Get current product id
     *
     * @return null|int
     */
    function getProductId()
    {
        $product = $this->coreRegistry->registry('product');
        return $product ? $product->getId() : null;
    }

    function getLimitQtyLinks($storeId = null)
    {
        return $this->scopeConfig->getValue(self::LIMIT_QTY_LINKS,ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $template = $this->getLayout()->createBlock('Magento\Framework\View\Element\Template');
        $template->setFragment('catalog.product.top.list.mageworx.downloads.attachments');
        return parent::_prepareLayout();
    }
}
