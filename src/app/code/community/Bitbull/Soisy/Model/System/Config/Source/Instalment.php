<?php

/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Martins Saukums <martins.saukums@bitbull.it>
 */
class Bitbull_Soisy_Model_System_Config_Source_Instalment
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = array();

        for ($i = 3; $i <= 60; $i++) {
            $result[] = array(
                'label' => $i,
                'value' => $i,
            );
        }

        return $result;
    }
}