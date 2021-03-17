<?php

namespace CanadaSatellite\Theme\Block\Adminhtml\Desk\Edit\Left;

class Form extends \Cart2Quote\Desk\Block\Adminhtml\Edit\Left\Form
{

    /**
     * Adds the customer information to the form.
     *
     * @return $this
     * @throws \Exception
     */
    protected function _addCustomer()
    {
        $this->_searchCriteria->setFilterGroups([])->setSortOrders(
            [
                $this->_sortOrderBuilder
                    ->setField('firstname')
                    ->setDirection(\Magento\Framework\Api\SortOrder::SORT_ASC)->create(),
                $this->_sortOrderBuilder
                    ->setField('lastname')
                    ->setDirection(\Magento\Framework\Api\SortOrder::SORT_ASC)->create()
            ]
        );

        $this->_getFieldSet()->addField(
            'customer_email',
            'text',
            [
                'label' => __("Customer"),
                'required' => true,
                'name' => 'customer_email',
                'value' => $this->_getTicket()->getCustomerEmail(),
            ]
        );

        return $this;
    }

}
