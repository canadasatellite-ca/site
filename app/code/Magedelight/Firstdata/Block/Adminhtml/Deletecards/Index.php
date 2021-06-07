<?php
namespace Magedelight\Firstdata\Block\Adminhtml\Deletecards;

class Index extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Backend\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        array $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->_backendUrl = $backendUrl;
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        /** @var \Magento\Backend\Block\Widget\Button $buttonBlock  */
        $buttonBlock = $this->getForm()->getParent()->getLayout()->createBlock('Magento\Backend\Block\Widget\Button');

        $url = $this->_backendUrl->getUrl("md_firstdata/deletecards/");
        
        $js = "require([
    'Magento_Ui/js/modal/confirm'
], function(confirmation) {   
    confirmation({
        title: 'Delete Cards',
        content: 'Are You sure, You will lose all customers saved card detail.',
        actions: {
            confirm: function(){setLocation('$url')},
            cancel: function(){},
            always: function(){}
        }
    });
  
});";
        $data = [
            'label' => __('Delete Cards'),
            'onclick' => $js,
            'class' => '',
        ];

        $html = $buttonBlock->setData($data)->toHtml();
        return $html;
    }
}
