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
            ['label' => '10%', 'value' => '10'],
            ['label' => '20%', 'value' => '20'],
            ['label' => '30%', 'value' => '30'],
            ['label' => '40%', 'value' => '40'],
            ['label' => '50%', 'value' => '50'],
            ['label' => '60%', 'value' => '60'],
            ['label' => '70%', 'value' => '70'],
            ['label' => '80%', 'value' => '80'],
            ['label' => '90%', 'value' => '90'],
        ];
    }
}
