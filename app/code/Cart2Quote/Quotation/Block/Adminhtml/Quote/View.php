<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote;

/**
 * Adminhtml quotation quote view
 *
 * Class View
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote
 */
class View extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Block group
     *
     * @var string
     */
    protected $_blockGroup = 'Cart2Quote_Quotation';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Editable Status
     *
     * @var array
     */
    protected $_editableStatus = [
        'proposal_sent',
        'proposal_expired',
        'ordered'
    ];

    /**
     * View constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $_extQuoteId = $this->getQuote()->getExtQuoteId();
        if ($_extQuoteId) {
            $_extQuoteId = '[' . $_extQuoteId . '] ';
        } else {
            $_extQuoteId = '';
        }

        return __(
            'Quote # %1 %2 | %3',
            $this->getQuote()->getRealQuoteId(),
            $_extQuoteId,
            $this->formatDate(
                $this->_localeDate->date(new \DateTime($this->getQuote()->getQuotationCreatedAt())),
                \IntlDateFormatter::MEDIUM,
                true
            )
        );
    }

    /**
     * Retrieve quote model object
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->_coreRegistry->registry('current_quote');
    }

    /**
     * Hold URL getter
     *
     * @return string
     */
    public function getHoldUrl()
    {
        return $this->getUrl('quotation/*/hold');
    }

    /**
     * URL getter
     *
     * @param string $params
     * @param array $params2
     * @return string
     */
    public function getUrl($params = '', $params2 = [])
    {
        $params2['quote_id'] = $this->getQuoteId();

        return parent::getUrl($params, $params2);
    }

    /**
     * Retrieve Quote Identifier
     *
     * @return int
     */
    public function getQuoteId()
    {
        return $this->getQuote() ? $this->getQuote()->getId() : null;
    }

    /**
     * Comment URL getter
     *
     * @return string
     */
    public function getCommentUrl()
    {
        return $this->getUrl('quotation/*/comment');
    }

    /**
     * Return back url for view grid
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getQuote() && $this->getQuote()->getBackUrl()) {
            return $this->getQuote()->getBackUrl();
        }

        return $this->getUrl('quotation/*/');
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'quotation_id';
        $this->_controller = 'adminhtml_quote';
        $this->_mode = 'view';

        parent::_construct();

        $this->setId('quotation_quote_view');
        $quote = $this->getQuote();

        if (!$quote) {
            return;
        }

        $this->removeButton('save');

        if (in_array($quote->getStatus(), $this->_editableStatus)) {
            $this->addButton(
                'cancel',
                [
                    'label' => __('Cancel Quote'),
                    'class' => 'cancel',
                    'onclick' => 'quote.cancel(" ' . $this->getCancelUrl() . ' ");'
                ],
                1
            );

            $this->addButton(
                'edit',
                [
                    'label' => __('Edit Quote'),
                    'class' => 'edit',
                    'onclick' => 'quote.edit("' . $this->getEditUrl() . '");'
                ],
                1
            );
        }

        $this->addButton(
            'duplicate',
            [
                'label' => __('Duplicate Quote'),
                'class' => 'secondary',
                'class_name' => \Magento\Backend\Block\Widget\Button\SplitButton::class,
                'options' => [
                    [
                        'label' => __('Duplicate Quote'),
                        'onclick' => 'quote.duplicate("' . $this->getDuplicateUrl() . '");',
                        'class' => 'duplicate'
                    ],
                    [
                        'label' => __('Duplicate Quote (assign customer)'),
                        'data_attribute' => [
                            'mage-init' => [
                                'buttonAdapter' => [
                                    'actions' => [
                                        [
                                            'targetName' => 'customer_select_form.customer_select_form.select_customer_modal',
                                            'actionName' => 'toggleModal'
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'class' => 'duplicate-assign-customer'
                    ],
                ],
            ],
            1
        );

        $this->addButton(
            'saveQuote',
            [
                'label' => __('Save'),
                'class' => 'save primary',
                'onclick' => 'quote.submit();'
            ],
            1
        );
    }

    /**
     * Edit URL getter
     *
     * @return string
     */
    public function getEditUrl()
    {
        return $this->getUrl('quotation/quote/edit');
    }

    /**
     * Cancel URL getter
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->getUrl('quotation/quote/cancel');
    }

    /**
     * Duplicate URL getter
     *
     * @return string
     */
    public function getDuplicateUrl()
    {
        return $this->getUrl('quotation/quote/duplicate');
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Get edit message
     *
     * @return \Magento\Framework\Phrase
     * @internal param \Cart2Quote\Quotation\Model\Quote $quote
     */
    protected function getEditMessage()
    {
        return __('Are you sure? This quote will be canceled and a new one will be created instead.');
    }
}
