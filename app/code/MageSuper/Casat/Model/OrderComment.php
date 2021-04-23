<?php
namespace MageSuper\Casat\Model;
class OrderComment extends \Magento\Framework\Model\AbstractModel implements \MageSuper\Casat\Api\Data\OrderCommentInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'magesuper_casat_ordercomment';

    protected function _construct()
    {
        $this->_init('MageSuper\Casat\Model\ResourceModel\OrderComment');
    }

    function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
