<?php 
namespace Magedelight\Faqs\Block\Adminhtml\Faq\Edit\Tab;

    class AssignProducts extends \Magento\Backend\Block\Template
    {
        /**
         * Block template
         *
         * @var string
         */
        protected $_template = 'products/assign_products.phtml';

        /**
         * @var \Magento\Catalog\Block\Adminhtml\Faq\Tab\Product
         */
        protected $blockGrid;

        /**
         * @var \Magento\Framework\Registry
         */
        protected $registry;

        /**
         * @var \Magento\Framework\Json\EncoderInterface
         */
        protected $jsonEncoder;


        protected $_productCollectionFactory;

        /**
         * AssignProducts constructor.
         *
         * @param \Magento\Backend\Block\Template\Context $context
         * @param \Magento\Framework\Registry $registry
         * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
         * @param array $data
         */
        public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            \Magento\Framework\Registry $registry,
            \Magento\Framework\Json\EncoderInterface $jsonEncoder,
            \Magedelight\Faqs\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
            array $data = []
        ) {
            $this->registry = $registry;
            $this->jsonEncoder = $jsonEncoder;
            $this->_productCollectionFactory = $productCollectionFactory;
            parent::__construct($context, $data);
        }

        /**
         * Retrieve instance of grid block
         *
         * @return \Magento\Framework\View\Element\BlockInterface
         * @throws \Magento\Framework\Exception\LocalizedException
         */
        public function getBlockGrid()
        {

            if (null === $this->blockGrid) {
                $this->blockGrid = $this->getLayout()->createBlock(
                    'Magedelight\Faqs\Block\Adminhtml\Faq\Edit\Tab\Product',
                    'category.product.grid'
                );
            }
            return $this->blockGrid;
        }

        /**
         * Return HTML of grid block
         *
         * @return string
         */
        public function getGridHtml()
        {

            return $this->getBlockGrid()->toHtml();
        }

        /**
         * @return string
         */
        public function getProductsJson()
        {

            $vProducts = $this->_productCollectionFactory->create()
                                    ->addFieldToFilter('question_id',$this->getItem()->getQuestionId()) 
                                    ->addFieldToSelect('product_id');   
            $products = array();
            foreach($vProducts as $pdct){      
                $products[$pdct->getProductId()]  = '';
            }       

            if (!empty($products)) {
                return $this->jsonEncoder->encode($products);
            }
            return '{}';
        }

        public function getItem()
        {
            return $this->registry->registry('md_faqs_question');
        }
    }
?>