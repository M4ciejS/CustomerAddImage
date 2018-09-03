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

    /**
     * Thumnail image width
     */
    const THUMBNAIL_WIDTH = 200;

    /**
     * Thumnail image height
     */
    const THUMBNAIL_HEIGHT = 100;

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
            $this->setData('filename', $result['file']);
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
        if (!$uploadResult = $uploader->save($path)) {
            throw new Exception('Could not save image file');
        }
        $filename = $uploader->getUploadedFileName();
        if (!$this->resizeFile($filename, true)) {
            throw new Exception('Could not create thumbnails');
        }
        return $uploadResult;
    }

    /**
     * Delete files associated with this customer image
     */
    public function deleteCustomerImageFiles()
    {
        $ioFile = new Varien_Io_File();
        $pathToImage = $this->getImageFilePath();
        $pathToThumbnail = $this->getThumbnailFilePath();
        if ($ioFile->fileExists($pathToImage) && $ioFile->fileExists($pathToThumbnail)) {
            $ioFile->rm($pathToImage);
            $ioFile->rm($pathToThumbnail);
            return true;
        }
        return false;
    }

    /**
     * Create customer image thumbnail
     * 
     * @param file $fileName
     * @param boolean $keepRation
     * @return boolean|string
     */
    private function resizeFile($fileName, $keepRation = true)
    {
        $filePath = $this->getImageFilePath($fileName);
        if (!is_file($filePath)) {
            return false;
        }

        $targetDir = $this->getThumbnailDirPath($fileName);
        $io = new Varien_Io_File();
        if (!$io->isWriteable($targetDir)) {
            $io->mkdir($targetDir);
        }
        if (!$io->isWriteable($targetDir)) {
            return false;
        }
        $image = Varien_Image_Adapter::factory('GD2');
        $image->open($filePath);
        $image->keepAspectRatio($keepRation);
        $image->resize(self::THUMBNAIL_WIDTH, self::THUMBNAIL_HEIGHT);
        $dest = $targetDir . DS . pathinfo($filePath, PATHINFO_BASENAME);
        $image->save($dest);
        if (is_file($dest)) {
            return $dest;
        }
        return false;
    }

    /**
     * Get customer image directory path
     * 
     * @param string $filename
     * @return string
     */
    public function getImageDirPath($filename)
    {
        return Mage::getBaseDir('media') . DS .
                self::MEDIA_FOLDER . DS .
                self::THUMBNAILS_FOLDER .
                pathinfo($filename, PATHINFO_DIRNAME);
    }

    /**
     * Get customer image thumbnail directory path
     * 
     * @param string $filename
     * @return type
     */
    public function getThumbnailDirPath($filename)
    {
        return Mage::getBaseDir('media') . DS .
                self::MEDIA_FOLDER . DS .
                self::THUMBNAILS_FOLDER .
                pathinfo($filename, PATHINFO_DIRNAME);
    }

    /**
     * Get customer image file path
     * 
     * @param string $fileName
     * @return string
     */
    public function getImageFilePath($fileName = null)
    {
        $fileName ? $fileName : $this->getData('filename');
        return Mage::getBaseDir('media') . DS .
                self::MEDIA_FOLDER . $fileName;
    }

    /**
     * Get customer image thumbnail file path
     * 
     * @return string
     */
    public function getThumbnailFilePath()
    {
        return Mage::getBaseDir('media') . DS .
                self::MEDIA_FOLDER . DS .
                self::THUMBNAILS_FOLDER . $this->getData('filename');
    }

    /**
     * Get image url
     * 
     * @return string url
     */
    public function getImageUrl()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) .
                self::MEDIA_FOLDER . $this->getData('filename');
    }

    /**
     * Get image thumbnail url
     * 
     * @return string url
     */
    public function getThumbnailUrl()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) .
                self::MEDIA_FOLDER . DS .
                self::THUMBNAILS_FOLDER . $this->getData('filename');
    }

}
