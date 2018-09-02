<?php

/**
 * @author maciej
 */

/**
 * CustomerImage Resource
 */
class Nemo_AddImage_Model_Resource_CustomerImage extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('nemo_addimage/customerImage', 'id');
    }
}
