<?php

namespace BroSolutions\VideoSlider\Plugin;

/**
 * Class WysiwygConfigDataProcessorPlugin
 * @package BroSolutions\VideoSlider\Plugin
 */
class WysiwygConfigDataProcessorPlugin
{
    /**
     * @param $subject
     * @param $result
     * @return array
     */
    public function afterProcess($subject, $result)
    {
        return [
            'add_variables' => false,
            'add_widgets' => true,
            'add_directives' => true,
            'use_container' => true,
            'container_class' => 'hor-scroll',
        ];
    }
}
