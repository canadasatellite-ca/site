<?php
namespace Interactivated\ActivationForm\Model\Source\Form;
 
class Status implements \Magento\Framework\Data\OptionSourceInterface
{

    /**#@+
     * Product Status values
     */
    const STATUS_PENDING = 1;

    const STATUS_COMPLETED = 2;

    public static function getOptionArray()
    {
        return [self::STATUS_PENDING => __('Pending'), self::STATUS_COMPLETED => __('Completed')];
    }
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        foreach ($this->getOptionArray() as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();

        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}
