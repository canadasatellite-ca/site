<?php

namespace MW\Onestepcheckout\Model\Customer;

class Customer extends \Magento\Customer\Model\Customer
{
	/**
     * Validate customer attribute values.
     * For existing customer password + confirmation will be validated only when password is set
     * (i.e. its change is requested)
     *
     * @return bool|string[]
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function validate()
    {
        $errors = [];
        if (!\Zend_Validate::is(trim($this->getFirstname()), 'NotEmpty')) {
            $errors[] = __('Please enter a first name.');
        }

        if (!\Zend_Validate::is(trim($this->getLastname()), 'NotEmpty')) {
            $errors[] = __('Please enter a last name.');
        }

        if (!\Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = __('Please correct this email address: "%1".', $this->getEmail());
        }

        $transport = new \Magento\Framework\DataObject(
            ['errors' => $errors]
        );
        $this->_eventManager->dispatch(
        	'customer_validate', 
        	['customer' => $this, 'transport' => $transport]
        );
        $errors = $transport->getErrors();

        if (empty($errors)) {
            return true;
        }
        
        return $errors;
    }
}
