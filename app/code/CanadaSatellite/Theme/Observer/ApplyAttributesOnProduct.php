<?php
namespace CanadaSatellite\Theme\Observer;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Option\Value\Collection as OptionValueCollection;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use MageWorx\OptionBase\Helper\Data as Helper;
use MageWorx\OptionBase\Model\AttributeSaver;
use MageWorx\OptionBase\Model\Entity\Product as ProductEntity;
use MageWorx\OptionBase\Model\Product\Attributes as ProductAttributes;
use MageWorx\OptionBase\Model\Product\Option\Attributes as OptionAttributes;
use MageWorx\OptionBase\Model\Product\Option\Value\Attributes as OptionValueAttributes;
use MageWorx\OptionBase\Model\ResourceModel\DataSaver;
use Psr\Log\LoggerInterface as Logger;

class ApplyAttributesOnProduct extends \MageWorx\OptionBase\Observer\ApplyAttributesOnProduct
{

    public function __construct(
        OptionValueCollection $optionValueCollection,
        ProductAttributes $productAttributes,
        OptionAttributes $optionAttributes,
        OptionValueAttributes $optionValueAttributes,
        Product $productModel,
        ProductEntity $productEntity,
        Helper $helper,
        ResourceConnection $resource,
        Logger $logger,
        MessageManager $messageManager,
        AttributeSaver $attributeSaver,
        DataSaver $dataSaver)
    {
        parent::__construct($optionValueCollection, $productAttributes, $optionAttributes, $optionValueAttributes, $productModel, $productEntity, $helper, $resource, $logger, $messageManager, $attributeSaver, $dataSaver);
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    protected function initOptions($observer)
    {
        $currentOptions = $observer->getProduct()->getOptions();
        if($observer->getProduct()->getFlagCliCommand() === 1){
            for($i = 0; $i < count($currentOptions); $i++){
                $temp = [];
                foreach ($currentOptions[$i]->getValues() as $key => $value){
                    $temp[] = $value->getData();
                }
                $currentOptions[$i]->setData('values', $temp);
            }
        }
        if ($observer->getProduct()->getIsAfterTemplateSave()) {
            $this->options = [];
            if (empty($currentOptions)) {
                return;
            }
            foreach ($currentOptions as $currentOption) {
                if (!empty($currentOption['is_delete'])) {
                    continue;
                }
                $this->options[] = $currentOption;
            }
        } else {
            $savedOptions = $this->productModel->load($observer->getProduct()->getId())->getOptions();

            $currentOptions = $this->helper->beatifyOptions($currentOptions);
            $savedOptions = $this->helper->beatifyOptions($savedOptions);
            $savedOptions = $this->_setValues($currentOptions,$savedOptions);
            $this->options = $this->mergeArrays($currentOptions, $savedOptions);
        }
    }

    protected function _setValues($currentOptions,$savedOptions){
        for($i = 0; $i < count($currentOptions);$i++){
            if(empty($savedOptions[$i]['values']) && (!empty($currentOptions[$i]['values']))){
                $savedOptions[$i]['values'] = $currentOptions[$i]['values'];
            }
        }
        return $savedOptions;
    }
}