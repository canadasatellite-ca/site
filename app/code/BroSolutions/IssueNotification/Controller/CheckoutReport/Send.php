<?php

namespace BroSolutions\IssueNotification\Controller\CheckoutReport;

/**
 * Class Send
 * @package BroSolutions\IssueNotification\Controller\CheckoutReport
 */
class Send extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $jsonFactory;

    /**
     * @var \BroSolutions\IssueNotification\Model\IssueNotificationFactory
     */
    public $issueNotification;

    /**
     * @var \BroSolutions\IssueNotification\Model\Email\Sender
     */
    public $email;

    protected $escaper;

    /**
     * Send constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \BroSolutions\IssueNotification\Model\IssueNotificationFactory $issueNotification
     * @param \BroSolutions\IssueNotification\Model\Email\Sender $email
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \BroSolutions\IssueNotification\Model\IssueNotificationFactory $issueNotification,
        \BroSolutions\IssueNotification\Model\Email\Sender $email,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->issueNotification = $issueNotification;
        $this->email = $email;
        $this->escaper = $escaper;
        $this->formKey = $formKey;
        return parent::__construct($context);
    }

    /**
     * @return false|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax() || !$this->getRequest()->getParam('email')) {
            return false;
        }

        $response = $this->jsonFactory->create();


        if ($this->formKey->getFormKey() !== $this->getRequest()->getParam('formKey')) {
            $this->messageManager->addErrorMessage('Form key is not valid. Please refresh the page');
            return $response->setData(false);
        }

        $report = $this->issueNotification->create();
        $report->setCustomerEmail($this->getRequest()->getParam('email'))
            ->setIssueComment(
                    $this->escaper->escapeHtml($this->getRequest()->getParam('comment'))
            )
            ->setStatus('new');
        try {
            $report->save();
            $this->messageManager->addSuccessMessage('Your request has been sent');
            $data = [
                'email_sender' => $this->getRequest()->getParam('email'),
                'sender_comment' => $this->escaper->escapeHtml($this->getRequest()->getParam('comment'))
            ];
            $this->email->sendEmail($data);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $response->setData(false);
        }

        return $response->setData(true);
    }
}
