<?php

class Nemo_AddImage_Block_Adminhtml_Catalog_Product_Tab extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    private $_helper;
    /**
     * Set the template for the block
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_helper = Mage::helper('nemo_addimage');
        $this->setTemplate('catalog/product/addimage_tab.phtml');
    }

    /**
     * Retrieve the label used for the tab relating to this block
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Customer AddImage Module');
    }

    /**
     * Retrieve the title used by this tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Broese customer added images');
    }

    /**
     * Determines whether to display the tab
     * Add logic here to decide whether you want the tab to display
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Stops the tab being hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    private function getImages() {
        $customerImageCollection = Mage::getModel('nemo_addimage/customerImage')
                ->getCollection()
                ->addFilter('product_id', $this->getProduct()->getId());
        return $customerImageCollection;
    }

    public function getImagesArray() {
        //check if file exists, if not log error.display error
        $images = [];
        foreach ($this->getImages() as $image) {
            $images[$image->getId()]['id'] = $image->getId();
            $images[$image->getId()]['path'] = 
                    Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) .
                    'customer_images' . $image->getData('filename'); //change it to use helper constants
            $images[$image->getId()]['thumbnail'] = 
                    Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) .
                    'customer_images' . DS . 'thumbnails' . $image->getData('filename'); //change it to use helper constants
            $images[$image->getId()]['title'] = $image->getData('title');
            $images[$image->getId()]['is_active'] = $image->getData('is_active') ? 'selected':'';
        }
        return $images;
    }
}
