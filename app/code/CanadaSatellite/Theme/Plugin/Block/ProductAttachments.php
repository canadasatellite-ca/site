<?php

namespace CanadaSatellite\Theme\Plugin\Block;

use MageWorx\Downloads\Model\Attachment;
use MageWorx\Downloads\Block\Catalog\Product\Attachments;
use MageWorx\Downloads\Model\Attachment\Product as AttachmentProduct;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Http\Context;
use Magento\Customer\Model\Context as ModelContext;

class ProductAttachments
{

    /**
     * @var AttachmentProduct
     */
    private $attachmentProduct;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @var Context
     */
    private $httpContext;

    /**
     * ProductAttachments constructor.
     * @param AttachmentProduct $attachmentProduct
     * @param StoreManagerInterface $storeManager
     * @param Context $httpContext
     */
    public function __construct(
        AttachmentProduct $attachmentProduct,
        StoreManagerInterface $storeManager,
        Context $httpContext
    ) {
        $this->attachmentProduct = $attachmentProduct;
        $this->_storeManager = $storeManager;
        $this->httpContext = $httpContext;
    }

    /**
     * @param Attachments $subject
     * @param callable $proceed
     * @param bool $isUseCustomerGroupFilter
     * @return \MageWorx\Downloads\Model\ResourceModel\Attachment\Collection
     */
    public function aroundGetAttachmentCollection(
        Attachments $subject,
        callable $proceed,
        $isUseCustomerGroupFilter = true
    ) {
        $collection = $this->attachmentProduct->getSelectedAttachmentsCollection($subject->getProductId());
        $collection->addFieldToFilter('is_active', Attachment::STATUS_ENABLED);
        $collection->addFieldToFilter('section_table.is_active', \MageWorx\Downloads\Model\Section::STATUS_ENABLED);
        $collection->addStoreFilter($this->_storeManager->getStore()->getId());
        if ($isUseCustomerGroupFilter) {
            $collection->addCustomerGroupFilter($this->getCustomerGroupId(
                $subject->getRequest()->getParam('cid')
            ));
        }
        $collection->addSortOrder();
        $collection->getSelect()->order('name');
        $collection->load();
        $subject->attachmentCollection = $collection;
        return $subject->attachmentCollection;
    }

    /**
     * @param $customerGroupId
     * @return mixed|null
     */
    private function getCustomerGroupId($customerGroupId)
    {
        if (!$customerGroupId) {
            $customerGroupId = $this->httpContext->getValue(ModelContext::CONTEXT_GROUP);
        }
        return $customerGroupId;
    }

}
