<?php

/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Martins Saukums <martins.saukums@bitbull.it>
 */
class Bitbull_Soisy_Block_Adminhtml_Config_InstalmentsTable extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * @var
     */
    protected $_itemRenderer;

    /**
     * Prepare to render
     */
    public function _prepareToRender()
    {
        $this->addColumn('from_price', array(
            'label' => $this->helper('soisy')->__('From Price'),
            'style' => 'width:100px',

        ));
        $this->addColumn('instalments', array(
            'label' => $this->helper('soisy')->__('Instalments'),
            'style' => 'width:100px',
            'renderer' => $this->_getInstalments(),
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = $this->helper('soisy')->__('Add');
    }

    /**
     * @return Bitbull_Soisy_Block_Adminhtml_Form_Field_Instalments|Mage_Core_Block_Abstract
     */
    protected function  _getInstalments()
    {
        if (!$this->_itemRenderer) {
            $this->_itemRenderer = $this->getLayout()->createBlock(
                'soisy/adminhtml_form_field_instalments', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_itemRenderer;
    }

    /**
     * @param Varien_Object $row
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getInstalments()
                ->calcOptionHash($row->getData('instalments')),
            'selected="selected"'
        );
    }
}