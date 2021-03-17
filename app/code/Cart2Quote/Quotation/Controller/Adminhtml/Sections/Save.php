<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Sections;

/**
 * Class Save
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Sections
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section
     */
    private $sectionResourceModel;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\SectionFactory
     */
    private $sectionFactory;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Section\Provider
     */
    private $provider;

    /**
     * Save constructor.
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section $sectionResourceModel
     * @param \Cart2Quote\Quotation\Model\Quote\SectionFactory $sectionFactory
     * @param \Cart2Quote\Quotation\Model\Quote\Section\Provider $provider
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section $sectionResourceModel,
        \Cart2Quote\Quotation\Model\Quote\SectionFactory $sectionFactory,
        \Cart2Quote\Quotation\Model\Quote\Section\Provider $provider,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->sectionResourceModel = $sectionResourceModel;
        $this->sectionFactory = $sectionFactory;
        $this->provider = $provider;
    }

    /**
     * Execute (controller entrypoint)
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute()
    {
        $sections = $this->getRequest()->getParam('sections');
        foreach ($sections as $sectionData) {
            $section = $this->sectionFactory->create();
            $section->setData(array_filter($sectionData));
            $section->isDeleted($section->getIsDeleted());
            $section->setIsUnassigned(false);

            try {
                $this->sectionResourceModel->save($section);
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage(
                    __('Please (re)move all items in a section before deleting the section.')
                );
            }
        }
    }

    /**
     * ACL check
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
