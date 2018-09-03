<?php

/**
 * @author maciej
 */

/**
 * CustomerImage Collection
 */
class Nemo_AddImage_Model_Resource_CustomerImage_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    public function _construct()
    {
        $this->_init('nemo_addimage/customerImage');
    }

}
