<?php
/* @var $installer Bitbull_Soisy_Model_Resource_Setup */


$itRegionCollection = Mage::getModel('directory/region')->getCollection()
                        ->addFieldToFilter('country_id', 'IT');

if (!$itRegionCollection->getSize()) {
    $installer = $this;
    $installer->startSetup();

    $provinces = $this->getProvinces();
    $connection = $this->getConnection();
    foreach ($provinces as $code => $name) {
        $sql = "INSERT INTO " . $this->getTable('directory_country_region') . " (`region_id`,`country_id`,`code`,`default_name`) VALUES (NULL,?,?,?)";
        $connection->query($sql, array('IT', $code, $name));

        $region_id = $connection->lastInsertId();

        $sql = "INSERT INTO " . $this->getTable('directory_country_region_name') . " (`locale`,`region_id`,`name`) VALUES (?,?,?)";
        $connection->query($sql, array('it_IT', $region_id, $name));
    }

    $this->endSetup();
}