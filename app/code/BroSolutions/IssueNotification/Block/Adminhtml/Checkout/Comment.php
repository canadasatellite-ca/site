<?php

namespace BroSolutions\IssueNotification\Block\Adminhtml\Checkout;

/**
 * Class Comment
 * @package BroSolutions\IssueNotification\Block\Adminhtml\Checkout
 */
class Comment extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'BroSolutions_IssueNotification::test.phtml';

    private $item;

    /**
     * @var \BroSolutions\IssueNotification\Model\IssueNotification
     */
    public $issueNotification;

    /**
     * Comment constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \BroSolutions\IssueNotification\Model\IssueNotification $issueNotification
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \BroSolutions\IssueNotification\Model\IssueNotification $issueNotification,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->issueNotification = $issueNotification;
    }

    /**
     * @return \BroSolutions\IssueNotification\Model\IssueNotification
     */
    private function getItem()
    {
        if ($this->item == null) {
            $this->item = $this->issueNotification->load($this->getRequest()->getParam('issue_id'));
        }

        return $this->item;
    }

    /**
     * @return string|false
     */
    public function getCheckoutComment()
    {
        if ($this->getItem()->getIssueComment()) {
            return wordwrap($this->getItem()->getIssueComment(), 80, PHP_EOL, true);
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getCustomerEmail()
    {
        return $this->getItem()->getCustomerEmail();
    }
}
