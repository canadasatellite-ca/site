<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote;

/**
 * Class Status
 *
 * @method string getStatus()
 * @method string getLabel()
 */
class Status extends \Magento\Sales\Model\Order\Status
{
    use \Cart2Quote\Features\Traits\Model\Quote\Status {
        unassignState as private traitUnassignState;
        validateBeforeUnassign as private traitValidateBeforeUnassign;
        getFrontendButtonHtmlFlag as private traitGetFrontendButtonHtmlFlag;
        canAccept as private traitCanAccept;
        showPrices as private traitShowPrices;
        showQuotableQuote as private traitShowQuotableQuote;
        statusIsAccepted as private traitStatusIsAccepted;
        _construct as private _traitConstruct;
    }

    const STATE_OPEN = 'open';
    const STATE_HOLDED = 'holded';
    const STATE_CANCELED = 'canceled';
    const STATE_PENDING = 'pending';
    const STATE_COMPLETED = 'completed';
    const STATUS_OPEN = 'open';
    const STATUS_NEW = 'new';
    const STATUS_CHANGE_REQUEST = 'change_request';
    const STATUS_QUOTE_AVAILABLE = 'quote_available';
    const STATUS_HOLDED = 'holded';
    const STATUS_WAITING_SUPPLIER = 'waiting_supplier';
    const STATUS_CANCELED = 'canceled';
    const STATUS_OUT_OF_STOCK = 'out_of_stock';
    const STATUS_PROPOSAL_EXPIRED = 'proposal_expired';
    const STATUS_PROPOSAL_REJECTED = 'proposal_rejected';
    const STATUS_PENDING = 'pending';
    const STATUS_PROPOSAL_SENT = 'proposal_sent';
    const STATUS_AUTO_PROPOSAL_SENT = 'auto_proposal_sent';
    const STATUS_ORDERED = 'ordered';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_CLOSED = 'closed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_PHONE_ONLY = 'phone_only';
    const STATUS_PRINT_ONLY = 'print_only';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $storeManager,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_storeManager = $storeManager;
    }

    /**
     * Unassigns quote status from particular state
     *
     * @param string $state
     * @return $this
     * @throws \Exception
     */
    public function unassignState($state)
    {
        return $this->traitUnassignState($state);
    }

    /**
     * Validate before unassign
     *
     * @param string $state
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validateBeforeUnassign($state)
    {
        $this->traitValidateBeforeUnassign($state);
    }

    /**
     * Get the flag to decide whether or not the button on the customer dashboard quote detail view should be enabled
     *
     * @return string
     */
    public function getFrontendButtonHtmlFlag()
    {
        return $this->traitGetFrontendButtonHtmlFlag();
    }

    /**
     * Function to check whether the quote can be accepted based on its state and status
     *
     * @param null|string $state
     * @param null|string $status
     * @return bool
     */
    public function canAccept($state = null, $status = null)
    {
        return $this->traitCanAccept($state, $status);
    }

    /**
     * Function to check whether the quote can show prices based on its state and status
     *
     * @param null|string $state
     * @param null|string $status
     * @return bool
     */
    public function showPrices($state = null, $status = null)
    {
        return $this->traitShowPrices($state, $status);
    }

    /**
     * Function to get statuses where display modified quote
     *
     * @param null|string $state
     * @param null|string $status
     * @return bool
     */
    public function showQuotableQuote($state = null, $status = null)
    {
        return $this->traitShowQuotableQuote($state, $status);
    }

    /**
     * Returns true if this->getStatus() is STATUS_ACCEPTED
     *
     * @return bool
     */
    public function statusIsAccepted()
    {
        return $this->traitStatusIsAccepted();
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_traitConstruct();
    }
}
