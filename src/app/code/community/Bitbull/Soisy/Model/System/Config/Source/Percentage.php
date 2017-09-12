<?php

/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Martins Saukums <martins.saukums@bitbull.it>
 */
class Bitbull_Soisy_Model_System_Config_Source_Percentage
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => 'Off', 'value' => '100'],
            ['label' => '5%',  'value' => '5'],
            ['label' => '10%', 'value' => '10'],
            ['label' => '15%', 'value' => '15'],
            ['label' => '25%', 'value' => '25'],
            ['label' => '30%', 'value' => '30'],
            ['label' => '45%', 'value' => '45'],
            ['label' => '50%', 'value' => '50'],
        ];
    }
}