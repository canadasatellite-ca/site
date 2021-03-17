<?php
namespace Brsw\OrderEmailAddressUpd\Controller\Adminhtml\Index;

class Index extends \Magento\Backend\App\Action
{
    /*****
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    /****
     * @var \Magento\Sales\Model\ResourceModel\Order
     */
    protected $orderResourceModel;
    /****
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;
    /****
     * @var array
     */
    protected $_publicActions = ['index'];

    /****
     * Index constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\ResourceModel\Order $orderResourceModel
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\ResourceModel\Order $orderResourceModel
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->orderResourceModel = $orderResourceModel;
        $this->orderRepository = $orderRepository;
        return parent::__construct($context);
    }

    /****
     * @param $email
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validateEmailFormat($email)
    {
        if (!\Zend_Validate::is($email, 'EmailAddress')) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please enter a valid email address.'));
        }
    }

    /****
     * @return $this
     */
    public function execute()
    {
        $data['error'] = true;
        $data['message'] = __('Please enter your email address.');

        if ($this->getRequest()->isAjax() &&
            $this->getRequest()->isPost() &&
            $this->getRequest()->getPost('email') &&
            $this->getRequest()->getPost('orderId')
        ) {
            $email = (string) $this->getRequest()->getPost('email');
            $orderId = $this->getRequest()->getPost('orderId');
            try {

                $this->validateEmailFormat($email);

                $order = $this->orderRepository->get($orderId);

                if ((string)$order->getCustomerEmail() === $email) {
                    throw new \Exception(__('the current e-mail address is the same'));
                }

                $order->setCustomerEmail($email);
                $this->orderResourceModel->save($order);

                $data['error'] = false;
                $data['message'] = __('The Email was changed.');

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $data['message'] = __('There was a problem with the : %1', $e->getMessage());
            } catch (\Exception $e) {
                $data['message'] = $e->getMessage();
            }
        }else{
            $data['message'] = 'Please check output data....';
        }
        return $this->resultJsonFactory->create()->setData($data);
    }
}