<?php  namespace Magedelight\Faqs\Block\Adminhtml\Category\Edit\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Product extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $logger;
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_faqFactory;

    public $status;
    
    public $questiontype;
    
    protected $_productCollectionFactory;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magedelight\Faqs\Model\Source\Faq\Status $status,
        \Magedelight\Faqs\Model\Source\Faq\Questiontype $questiontype,
        \Magedelight\Faqs\Model\FaqFactory $faqFactory,
        \Magedelight\Faqs\Model\ResourceModel\Question\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_faqFactory = $faqFactory;
        $this->status = $status;
        $this->questiontype = $questiontype;
        $this->_coreRegistry = $coreRegistry;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('catalog_category_question');
        $this->setDefaultSort('question_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    /**
     * @return array|null
     */
    public function getItem()
    {
        return $this->_coreRegistry->registry('md_faq_category');
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {

        // Set custom filter for in category flag
        if ($column->getId() == 'in_category') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('question_id', ['in' => $productIds]);
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('question_id', ['nin' => $productIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        if ($this->getItem()->getId()) {
            $this->setDefaultFilter(['in_category' => 1]);
        }       
        $collection = $this->_faqFactory->create()->getCollection();
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        if ($storeId > 0) {
            $collection->addStoreFilter($storeId);
        }


        $this->setCollection($collection);

        if ($this->getItem()->getProductsReadonly()) {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            $this->getCollection()->addFieldToFilter('question_id', ['in' => $productIds]);
        } 

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        if (!$this->getItem()->getProductsReadonly()) {
            $this->addColumn(
                'in_category',
                [
                    'type' => 'checkbox',
                    'name' => 'in_category',
                    'values' => $this->_getSelectedProducts(),
                    'index' => 'question_id',
                    'header_css_class' => 'col-select col-massaction',
                    'column_css_class' => 'col-select col-massaction'
                ]
            );
        }
        $this->addColumn(
            'question_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'question_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        
        $this->addColumn('question', 
            [
                'header' => __('Question'),
                'index' => 'question'
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->status->getOptionArray(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );
        $this->addColumn(
            'question_type',
            [
                'header' => __('Question Type'),
                'index' => 'question_type',
                'type' => 'options',
                'options' => $this->questiontype->getOptionArray(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );
        
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/ProductsGrid', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_products');
        if ($products === null) {   
            $vProducts = $this->_productCollectionFactory->create()
                                ->addFieldToFilter('category_id',$this->getItem()->getCategoryId()) 
                                ->addFieldToSelect('question_id');
            $products = array();
            foreach($vProducts as $pdct){      
                $products[]  = $pdct->getQuestionId();
            }       
        }
        return $products;
    }
}  
?>