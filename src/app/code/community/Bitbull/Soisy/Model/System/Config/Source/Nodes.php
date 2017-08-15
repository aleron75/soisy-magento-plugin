<?php

/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Martins Saukums <martins.saukums@bitbull.it>
 */
class Bitbull_Soisy_Model_System_Config_Source_Nodes
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => 'Minimum', 'value' => 'min'],
            ['label' => 'Average', 'value' => 'median'],
            ['label' => 'Maximum', 'value' => 'max'],
        ];
    }
}