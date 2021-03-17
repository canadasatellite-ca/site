<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Quote\Section;

/**
 * Class Unassigned
 * @package Cart2Quote\Quotation\Plugin\Quote\Section
 */
class Unassigned
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section
     */
    private $sectionResourceModel;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Section\UnassignedCreator
     */
    private $unassignedCreator;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Section\Provider
     */
    private $provider;
    /**
     * @var \Magento\Quote\Api\Data\CartExtensionFactory
     */
    private $quoteExtensionFactory;

    /**
     * Unassigned constructor.
     * @param \Cart2Quote\Quotation\Model\Quote\Section\UnassignedCreator $unassignedCreator
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section $sectionResourceModel
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Quote\Section\Provider $provider,
        \Magento\Quote\Api\Data\CartExtensionFactory $quoteExtensionFactory,
        \Cart2Quote\Quotation\Model\Quote\Section\UnassignedCreator $unassignedCreator,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section $sectionResourceModel
    ) {
        $this->sectionResourceModel = $sectionResourceModel;
        $this->unassignedCreator = $unassignedCreator;
        $this->provider = $provider;
        $this->quoteExtensionFactory = $quoteExtensionFactory;
    }

    /**
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote $subject
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote $result
     * @param \Magento\Quote\Model\Quote $object
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    public function afterLoad(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote $subject,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote $result,
        \Magento\Quote\Model\Quote $object
    ) {
        if ($object instanceof \Cart2Quote\Quotation\Model\Quote) {
            if ($object->getId() && !$this->sectionResourceModel->unassignedExistsForQuote($object->getId())) {
                $this->unassignedCreator->create($object);

                $extensionAttributes = $object->getExtensionAttributes();
                if ($extensionAttributes === null) {
                    $extensionAttributes = $this->quoteExtensionFactory->create();
                }
                $sections = $this->provider->getSections($object->getId());
                $extensionAttributes->setSections($sections);
                $object->setExtensionAttributes($extensionAttributes);
            }
        }
    }
}