<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin;

/**
 * Class Quote
 *
 * @package Cart2Quote\Quotation\Model\Plugin\Quote
 */
class Quote
{
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Section\Provider
     */
    private $provider;
    /**
     * @var \Magento\Quote\Api\Data\CartExtensionFactory
     */
    private $quoteExtensionFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Section\UnassignedCreator
     */
    private $unassignedSectionCreator;

    /**
     * Repository constructor.
     *
     * @param \Cart2Quote\Quotation\Model\Quote\Section\UnassignedCreator $unassignedSectionCreator
     * @param \Cart2Quote\Quotation\Model\Quote\Section\Provider $provider
     * @param \Magento\Quote\Api\Data\CartExtensionFactory $quoteExtensionFactory
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Quote\Section\UnassignedCreator $unassignedSectionCreator,
        \Cart2Quote\Quotation\Model\Quote\Section\Provider $provider,
        \Magento\Quote\Api\Data\CartExtensionFactory $quoteExtensionFactory
    ) {
        $this->provider = $provider;
        $this->quoteExtensionFactory = $quoteExtensionFactory;
        $this->unassignedSectionCreator = $unassignedSectionCreator;
    }

    /**
     * After load plugin
     *
     * @param \Cart2Quote\Quotation\Model\Quote $subject
     * @param \Cart2Quote\Quotation\Model\Quote $result
     * @return \Cart2Quote\Quotation\Model\Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterLoad(\Cart2Quote\Quotation\Model\Quote $subject, \Cart2Quote\Quotation\Model\Quote $result)
    {
        return $this->loadExtensionAttributes($result);
    }

    /**
     * @param \Cart2Quote\Quotation\Model\Quote $subject
     * @param \Cart2Quote\Quotation\Model\Quote $result
     * @return \Cart2Quote\Quotation\Model\Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterCreate(
        \Cart2Quote\Quotation\Model\Quote $subject,
        \Cart2Quote\Quotation\Model\Quote $result
    ) {
        $this->unassignedSectionCreator->create($result);

        return $this->loadExtensionAttributes($result);
    }

    /**
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return \Cart2Quote\Quotation\Model\Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function loadExtensionAttributes(\Cart2Quote\Quotation\Model\Quote $quote)
    {
        $extensionAttributes = $quote->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->quoteExtensionFactory->create();
        }
        $this->addSections($extensionAttributes, $quote->getId());
        $quote->setExtensionAttributes($extensionAttributes);

        return $quote;
    }

    /**
     * @param \Magento\Quote\Api\Data\CartExtension $extensionAttributes
     * @param string|int $quoteId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addSections(\Magento\Quote\Api\Data\CartExtension $extensionAttributes, $quoteId)
    {
        $sections = $this->provider->getSections($quoteId);
        $extensionAttributes->setSections($sections);
    }

    /**
     * @param \Cart2Quote\Quotation\Model\Quote $subject
     * @param \Magento\Quote\Api\Data\CartExtension $result
     * @return \Magento\Quote\Api\Data\CartExtension
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetExtensionAttributes(
        \Cart2Quote\Quotation\Model\Quote $subject,
        \Magento\Quote\Api\Data\CartExtension $result
    ) {
        if ($result
            && ($subject->getIsQuote() == \Cart2Quote\Quotation\Model\Quote::IS_QUOTE)
            && !$result->getSections()
        ) {
            $this->addSections($result, $subject->getId());
        }

        return $result;
    }
}
