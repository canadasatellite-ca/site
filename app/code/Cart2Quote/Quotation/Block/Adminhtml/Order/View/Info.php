<?php
/**
 * Cart2Quote
 */
namespace Cart2Quote\Quotation\Block\Adminhtml\Order\View;

/**
 * Class Info
 * @package Cart2Quote\Quotation\Block\Adminhtml\Order\View
 */
class Info extends \Magento\Sales\Block\Adminhtml\Order\View\Info
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection\Factory $_quoteCollectionFactory
     */
    protected $_quoteCollectionFactory;

    /**
     * Info constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Customer\Api\CustomerMetadataInterface $metadata
     * @param \Magento\Customer\Model\Metadata\ElementFactory $elementFactory
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $_quoteCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Api\CustomerMetadataInterface $metadata,
        \Magento\Customer\Model\Metadata\ElementFactory $elementFactory,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $_quoteCollectionFactory,
        array $data = []
    ) {
        $this->_quoteCollectionFactory = $_quoteCollectionFactory;
        parent::__construct(
            $context,
            $registry,
            $adminHelper,
            $groupRepository,
            $metadata,
            $elementFactory,
            $addressRenderer,
            $data
        );
    }

    /**
     * Get quote view URL.
     *
     * @param int quote id
     * @return string
     */
    public function getQuoteViewUrl($quoteId)
    {
        return $this->getUrl('quotation/quote/view', ['quote_id' => $quoteId]);
    }

    /**
     * Get quote number
     *
     * @param int quote id
     * @return string
     */
    public function getQuoteNumber($quoteId)
    {
        $quote = $this->_quoteCollectionFactory->getQuote($quoteId);

        if (is_array($quote)) {
            return $quote['increment_id'];
        } else {
            return false;
        }
    }
}
