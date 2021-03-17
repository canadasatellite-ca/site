<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Sales\Order\Api;

use Cart2Quote\Quotation\Model\Quote;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Model\Order;

/**
 * Class OrderRepositoryInterface
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Sales\Order\Api
 */
class OrderRepositoryInterface
{
    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @var \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Quote\Model\QuoteFactory $mageQuoteFactory
     */
    protected $mageQuoteFactory;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl;

    /**
     * OrderRepositoryInterface constructor
     *
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Magento\Quote\Model\QuoteFactory $mageQuoteFactory
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Sales\Api\Data\OrderExtensionFactory|null $orderExtensionFactory
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\QuoteFactory $mageQuoteFactory,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory = null
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->mageQuoteFactory = $mageQuoteFactory;
        $this->backendUrl = $backendUrl;

        $this->orderExtensionFactory = $orderExtensionFactory ?: ObjectManager::getInstance()
            ->get(\Magento\Sales\Api\Data\OrderExtensionFactory::class);
    }

    /**
     * After get plugin
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Model\Order
     */
    public function afterGet($orderRepositoryInterface, $order)
    {
        return $this->addQuoteExtensionAttributesToOrder($order);
    }

    /**
     * After get list plugin
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface
     * @param \Magento\Sales\Api\Data\OrderSearchResult $searchResult
     * @return \Magento\Sales\Api\Data\OrderSearchResult
     */
    public function afterGetList($orderRepositoryInterface, $searchResult)
    {
        foreach ($searchResult->getItems() as $order) {
            $this->addQuoteExtensionAttributesToOrder($order);
        }

        return $searchResult;
    }

    /**
     * Add the quote extension attributes to the order
     *
     * @param Order $order
     * @return Order
     */
    private function addQuoteExtensionAttributesToOrder($order)
    {
        $quoteId = $order->getQuoteId();

        /** @var \Magento\Quote\Model\Quote $mageQuote */
        $mageQuote = $this->mageQuoteFactory->create()->load($quoteId);
        if (!$mageQuote->getId() || !$mageQuote->getLinkedQuotationId()) {
            //no mage quote data available or no quotation quote linked
            return $order;
        }

        $quotationId = $mageQuote->getLinkedQuotationId();

        /** @var Quote $quotation */
        $quotation = $this->quoteFactory->create()->load($quotationId);
        if (!$quotation->getId()) {
            //no quote data available
            return $order;
        }

        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        }

        //modify extension attributes, add the quote increment id
        $extensionAttributes->setQuoteationIncrementId($quotation->getIncrementId());

        //add a backend url
        $backendUrl = $this->backendUrl->getUrl(
            'quotation/quote/view',
            ['quote_id' => $quotation->getId()]
        );
        $extensionAttributes->setQuoteationBackendUrl($backendUrl);

        //set extension attributes
        $order->setExtensionAttributes($extensionAttributes);

        return $order;
    }
}
