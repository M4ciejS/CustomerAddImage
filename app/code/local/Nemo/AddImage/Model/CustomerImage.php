<?php

/**
 * @author maciej
 */

/**
 * CustomerImage Model
 */
class Nemo_AddImage_Model_CustomerImage extends Mage_Core_Model_Abstract
{
    /**
     * @todo Move constants to module config
     */

    /**
     * Customer images folder name
     */
    const MEDIA_FOLDER = 'customer_images';

    /**
     * Thumnails folder name
     */
    const THUMBNAILS_FOLDER = 'thumbnails';

    protected function _construct()
    {
        $this->_init('nemo_addimage/customerImage');
    }

    /** 
     * save image file if set
     * @param string $imageFile name of uploaded file
     */
    public function save($imageFile = null)
    {
        if ($imageFile) {
            $result = $this->saveImageFile($imageFile);
            $this->setData('title', pathinfo($result['file'], PATHINFO_FILENAME));
        }
        parent::save();
    }

    /**
     * Remove files along with the model
     */
    public function delete()
    {
        $this->deleteCustomerImageFiles();
        parent::delete();
    }
    /**
     * Save customer image file
     * 
     * @param file $imageFile
     */
    public function saveImageFile($imageFile)
    {
        $uploader = new Mage_Core_Model_File_Uploader($imageFile);
        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png'])
                ->setAllowCreateFolders(true)
                ->setAllowRenameFiles(true)
                ->setFilesDispersion(true)
                ->addValidateCallback(
                        Mage_Core_Model_File_Validator_Image::NAME, Mage::getModel('core/file_validator_image'), 'validate'
        );
        $path = Mage::getBaseDir('media') . DS . self::MEDIA_FOLDER;
        if(!$uploadResult = $uploader->save($path)){
            throw new Exception('Could not save image file');
        }
        $filename = $uploader->getUploadedFileName();
        if (!$this->resizeFile($filename , true)) {
            throw new Exception('Could not creaet thumbnails');
        }
        return $uploadResult;
    }

    /**
     * Delete files associated with this customer image
     */
    public function deleteCustomerImageFiles() {
        $ioFile = new Varien_Io_File();
        $pathToImage = Mage::getBaseDir('media') . DS .
                self::MEDIA_FOLDER . $this->getData('filename');
        $pathToThumbnail = Mage::getBaseDir('media') . DS .
                self::MEDIA_FOLDER . DS .
                self::THUMBNAILS_FOLDER . $this->getData('filename');
        if($ioFile->fileExists($pathToImage) && $ioFile->fileExists($pathToThumbnail)) {
            $ioFile->rm($pathToImage);
            $ioFile->rm($pathToThumbnail);
            return true;
        }
        return false;
    }

    /**
     * Create customer image thumbnail
     * 
     * @param file $filename
     * @param boolean $keepRation
     * @return boolean|string
     */
    private function resizeFile($filename, $keepRation = true)
    {
        $path = Mage::getBaseDir('media') . DS . self::MEDIA_FOLDER . $filename;
        if (!is_file($path)) {
            return false;
        }

        $targetDir = Mage::getBaseDir('media') . DS .
                self::MEDIA_FOLDER . DS . 
                self::THUMBNAILS_FOLDER .
                pathinfo($filename, PATHINFO_DIRNAME);
        $io = new Varien_Io_File();
        if (!$io->isWriteable($targetDir)) {
            $io->mkdir($targetDir);
        }
        if (!$io->isWriteable($targetDir)) {
            return false;
        }
        $image = Varien_Image_Adapter::factory('GD2');
        $image->open($path);
        $width = 200;//$this->getConfigData('resize_width');
        $height = 100;//$this->getConfigData('resize_height');
        $image->keepAspectRatio($keepRation);
        $image->resize($width, $height);
        $dest = $targetDir . DS . pathinfo($path, PATHINFO_BASENAME);
        $image->save($dest);
        if (is_file($dest)) {
            return $dest;
        }
        return false;
    }
}
