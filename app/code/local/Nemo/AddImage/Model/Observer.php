<?php

class Nemo_AddImage_Model_Observer
{

    /**
     * Flag to stop observer executing more than once
     *
     * @var static bool
     */
    static protected $_singletonFlag = false;

    /**
     * Save customer added image tab data on product save
     *
     * @param Varien_Event_Observer $observer
     */
    public function saveProductTabData(Varien_Event_Observer $observer)
    {

        if (!self::$_singletonFlag) {
            self::$_singletonFlag = true;

            $imageOptions = $this->_getRequest()->getPost('customer_image');
            $customerImageCollection = Mage::getModel('nemo_addimage/customerImage')->getCollection();
            $customerImageCollection->addFieldToFilter('id', array_keys($imageOptions));
            try
            {
                foreach ($customerImageCollection as $image) {
                    $index = (int)$image->getId();
                    if ($imageOptions[$index]['delete'] == 1) {
                        $image->delete();
                        continue;
                    }
                    if ($imageOptions[$index]['active'] == 0) {
                        $image->setData('is_active', 0)->save();
                    } else {
                        $image->setData('is_active', 1)->save();
                    }
                }
            } catch (Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
    }

    /**
     * Retrieve the product model
     *
     * @return Mage_Catalog_Model_Product $product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }

    /**
     * Shortcut to getRequest
     *
     */
    protected function _getRequest()
    {
        return Mage::app()->getRequest();
    }
    
    protected function modifyCustomerImagesData($args) {
        
    }
}
