<?php

/**
 * @author maciej
 */

/**
 * Methods to display customer added image on product page
 */
class Nemo_AddImage_Block_CustomerImage extends Mage_Catalog_Block_Product_View
{
    /**
     * Customer image collection
     * 
     * @var Nemo_AddImage_Model_Resource_Collection_CustomerImage
     */
    private $customerImageCollection;

    /**
     * Initialize Customer images collection
     * 
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        parent::__construct($args);
        $this->customerImageCollection 
            = Mage::getModel('nemo_addimage/customerImage')
            ->getCollection()
            ->addFilter('product_id', $this->getProduct()->getId())
            ->addFilter('is_active', 1);
    }

    /**
     * Check if there are images for current product
     * 
     * @return boolean
     */
    public function hasImages() 
    {
        return $this->customerImageCollection->getSize()? true: false;
    }

    /**
     * Get array of image data for current product
     * 
     * @return array
     */
    public function getImages() 
    {
        if ($this->hasImages()) {
            $images = [];
            foreach ($this->customerImageCollection as $image) {
                $images[$image->getId()]['path']
                    = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) .
                    'customer_images' . $image->getData('filename');
                $images[$image->getId()]['title'] = $image->getTitle();
            }
            return $images;
        }
    }
}
