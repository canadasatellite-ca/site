<?php

namespace LitExtension\CartImport\Model;

class User extends \Magento\Framework\Model\AbstractModel
{

    public function _construct()
    {
        $this->_init('LitExtension\CartImport\Model\ResourceModel\User');
    }

    public function beforeSave(){
        $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61 = $this->getData('notice');
        if(is_array($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61)){
            $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61 = serialize($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61);
            $this->setData('notice',$nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61);
        }
    }

    public function getNotice(){
        $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61 = $this->getData('notice');
        $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61 = unserialize($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61);
        return $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61;
    }

    public function loadByUserId($nhtcace4a159ff9f2512dd42373760608767b62855d){
        $nht2037de437c80264ccbce8a8b61d0bf9f593d2322 = $this->getResourceCollection()
            ->addFieldToFilter('user_id', $nhtcace4a159ff9f2512dd42373760608767b62855d);
        foreach($nht2037de437c80264ccbce8a8b61d0bf9f593d2322 as $nht1615307cc4523f183e777df67f168c86908e8007){
            return $nht1615307cc4523f183e777df67f168c86908e8007;
        }
        return false;
    }
}