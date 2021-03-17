<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper;

/**
 * Class Cloning
 *
 * @package Cart2Quote\Quotation\Helper
 */
class Cloning extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Item
     */
    private $itemResourceModel;

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section
     */
    private $sectionResourceModel;

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section
     */
    private $sectionItemResourceModel;

    /**
     * @var \Cart2Quote\Quotation\Model\QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var array
     */
    protected $sectionsMapping = [];

    /**
     * Cloning constructor.
     *
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item $itemResourceModel
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section $sectionResourceModel
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section $sectionItemResourceModel
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\ResourceModel\Quote\Item $itemResourceModel,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section $sectionResourceModel,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section $sectionItemResourceModel,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->itemResourceModel = $itemResourceModel;
        $this->sectionResourceModel = $sectionResourceModel;
        $this->sectionItemResourceModel = $sectionItemResourceModel;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Function to clone a quote
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param boolean $originalQuote
     * @return \Cart2Quote\Quotation\Model\Quote
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function cloneQuote(\Cart2Quote\Quotation\Model\Quote $quote, $originalQuote = false)
    {
        $newQuote = $this->createNewQuote($quote, $originalQuote);
        $this->addSections($quote->getExtensionAttributes()->getSections(), $newQuote);
        $this->addItems($quote->getAllVisibleItems(), $newQuote);
        $this->addAddresses($quote->getAddressesCollection(), $newQuote);
        $this->addPayments($quote->getPaymentsCollection(), $newQuote);
        $this->addShippingMethod($quote->getShippingAddress()->getShippingMethod(), $newQuote);
        $newQuote->collectShippingRates();
        $newQuote->setRecollect(true);
        $newQuote->saveQuote();
        $this->sectionsMapping = [];

        return $newQuote;
    }

    /**
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function createOriginalQuote(\Cart2Quote\Quotation\Model\Quote $quote)
    {
        $clonedQuote = $this->cloneQuote($quote, true);
        $clonedQuote->setClonedQuote(true);
        $quote->setClonedQuoteId($clonedQuote->getQuoteId());
        $quote->save();
        $clonedQuote->save();
    }

    /**
     * Function to create a new quote
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param bool $originalQuote
     * @return \Cart2Quote\Quotation\Model\Quote
     * @throws \Exception
     */
    private function createNewQuote($quote, $originalQuote)
    {
        $newQuote = $this->quoteFactory->create();
        $excludeFromCopy = [
            'id',
            'increment_id',
            'quotation_created_at',
            'entity_id',
            'quote_id'
        ];

        if ($originalQuote) {
            $excludeFromCopy = [
                'id',
                'entity_id',
                'quote_id'
            ];
        }
        $data = array_diff_key($quote->getData(), array_flip($excludeFromCopy));
        $newQuote->setData($data);
        $newQuote->save();

        return $newQuote;
    }

    /**
     * Add sections to the quote
     *
     * @param \Cart2Quote\Quotation\Api\Data\Quote\SectionInterface[] $originalSections
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function addSections($originalSections, $quote)
    {
        $this->sectionsMapping = $sections = [];
        foreach ($originalSections as $originalSection) {
            $clonedSection = clone $originalSection;
            $clonedSection->setId(null);
            $clonedSection->setSectionId(null);
            $clonedSection->setQuoteId($quote->getQuoteId());
            $this->sectionResourceModel->save($clonedSection);
            $this->sectionsMapping[$originalSection->getSectionId()] = $clonedSection->getSectionId();
            $sections[] = $clonedSection;
        }
        $quote->getExtensionAttributes()->setSections($sections);
    }

    /**
     * Add items to a given quote
     *
     * @param \Magento\Quote\Model\Quote\Item[] $items
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function addItems($items, $quote)
    {
        /**
         * @var \Magento\Quote\Model\Quote\Item $item
         */
        foreach ($items as $item) {
            $sectionItemItemId = $item->getExtensionAttributes()->getSection()->getSectionId();
            if (isset($this->sectionsMapping[$sectionItemItemId])) {
                $cloneItems = $this->cloneItem($item, $quote, false, $this->sectionsMapping[$sectionItemItemId]);
            } else {
                $cloneItems = $this->cloneItem($item, $quote, false, null);
            }

            foreach ($cloneItems as $cloneItem) {
                $quote->getItemsCollection()->addItem($cloneItem);
            }
        }
    }

    /**
     * Add addresses to a given quote
     *
     * @param \Magento\Quote\Model\Quote\Address[] $addresses
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     */
    private function addAddresses($addresses, $quote)
    {
        $quote->getAddressesCollection()->removeAllItems();
        foreach ($addresses as $key => $address) {
            $clonedAddress = clone $address;
            $clonedAddress->setId(null);
            $clonedAddress->setQuote($quote);
            $clonedAddress->setPreviousId($address->getId());
            $clonedAddress->setPreviousQuoteId($address->getQuoteId());
            $quote->addAddress($clonedAddress);
        }
    }

    /**
     * Add payments to a given quote
     *
     * @param \Magento\Quote\Model\Quote\Payment[] $payments
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     */
    private function addPayments($payments, $quote)
    {
        $quote->getPaymentsCollection()->removeAllItems();
        foreach ($payments as $payment) {
            $clonedPayment = clone $payment;
            $clonedPayment->setId(null);
            $clonedPayment->setQuote($quote);
            $clonedPayment->setPreviousId($payment->getId());
            $clonedPayment->setPreviousQuoteId($payment->getQuoteId());
            $quote->setPayment($clonedPayment);
        }
    }

    /**
     * Clone a quote item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param \Cart2Quote\Quotation\Model\Quote|boolean $quote
     * @param bool $useOrignalSectionItem
     * @param null|int $sectionItemId
     * @param null|\Magento\Quote\Model\Quote\Item $parentItem
     * @return \Magento\Quote\Model\Quote\Item|array
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function cloneItem(
        \Magento\Quote\Model\Quote\Item $item,
        $quote = false,
        $useOrignalSectionItem = true,
        $sectionItemId = null,
        $parentItem = null
    ) {
        $clonedItems = [];
        $clonedItem = clone $item;
        $clonedItem->setId(null);

        if ($quote) {
            $clonedItem->setQuote($quote);
        }

        $clonedItem->setParentItemId(null);

        $options = $item->getOptions();
        foreach ($options as $option) {
            $clonedOption = clone $option;
            $clonedOption->setOptionId(null);
            $clonedOption->setItemId(null);
            $clonedItem->setOptions($clonedOption);
        }

        if ($item->getTierItems()) {
            $clonedItem->setTierItem(null);
            $clonedItem->setTierItems($item->getTierItems());
            $clonedItem->setCurrentTierItemId(null);
            $clonedItem->setCurrentTierItem(null);
            $currentTierItemId = $item->getCurrentTierItemId();
        }

        if (isset($parentItem)) {
            $clonedItem->setParentItem($parentItem);
        }

        $this->itemResourceModel->save($clonedItem);

        foreach ($item->getChildren() as $childItem) {
            $clonedItems[] = $this->cloneItem($childItem, $quote, $useOrignalSectionItem, $sectionItemId, $clonedItem);
        }

        /**
         * @var \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
         */
        foreach ($clonedItem->getTierItems() as $tierItem) {
            $currentTierItem = false;
            if ($currentTierItemId == $tierItem->getId()) {
                $currentTierItem = true;
            }
            $tierItem->setId(null);
            $tierItem->setItemId($clonedItem->getItemId());
            $tierItem->setItem($clonedItem);
            $tierItem->save();

            if ($currentTierItem) {
                $clonedItem->setCurrentTierItemId($tierItem->getId());
                $clonedItem->setCurrentTierItem($tierItem);
            }
        }

        /**
         * @var \Cart2Quote\Quotation\Model\Quote\Item\Section $originalItemSection
         */
        $originalItemSection = $item->getExtensionAttributes()->getSection();
        if ($originalItemSection->getSectionId()) {
            $clonedItemSection = clone $originalItemSection;
            $clonedItemSection->setId(null);
            if ($useOrignalSectionItem) {
                $sectionItemId = $originalItemSection->getSectionId();
            }
            $clonedItemSection->setSectionId($sectionItemId);
            $clonedItemSection->setItemId($clonedItem->getItemId());
            $this->sectionItemResourceModel->save($clonedItemSection);
        }

        if (isset($parentItem)) {
            return $clonedItem;
        }
        $clonedItems[] = $clonedItem;

        return $clonedItems;
    }

    /**
     * Add shipping methods to a quote
     *
     * @param string $shippingMethod
     * @param \Cart2Quote\Quotation\Model\Quote $newQuote
     */
    private function addShippingMethod($shippingMethod, $newQuote)
    {
        $newQuote->getShippingAddress()->setShippingMethod($shippingMethod);
    }
}
