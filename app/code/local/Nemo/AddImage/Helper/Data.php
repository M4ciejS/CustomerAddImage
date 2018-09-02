<?php

/**
 * @author maciej
 */
class Nemo_AddImage_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Log file name
     */
    const LOG_FILE = 'nemo_addImage';

    /**
     * Customer images folder name
     */
    const MEDIA_FOLDER = 'customer_images';

    /**
     * Thumnails folder name
     */
    const THUMBNAILS_FOLDER = 'thumbnails';

    /**
     * Validate and save customer uploaded image
     * 
     * @param array $file
     * @param int $productId
     * @throws Exception
     */
    public function saveImage($file, $productId)
    {
        try
        {
            if((int) $productId === 0) {
                throw new Exception(__('Wrong product id'));
            }
            $uploader = new Mage_Core_Model_File_Uploader($file);
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
            $title = pathinfo($uploadResult['file'], PATHINFO_BASENAME);
            $this->saveCustomerImageData($productId, $title, $filename);
            Mage::getSingleton('core/session')->addSuccess(__('Image added, waiting for acceptance. Thank you.'));
        } catch (Exception $e)
        {
            Mage::log($e->getMessage(), null, self::LOG_FILE);
            Mage::getSingleton('core/session')->addError(__('Error adding image:') . $e->getMessage());
        }
    }

    /**
     * Save image information to database
     * 
     * @param intiger    $productId
     * @param string     $title
     * @param string     $filename
     */
    public function saveCustomerImageData($productId, $title, $filename)
    {
        try
        {
            $customerImage = Mage::getModel('nemo_addimage/customerImage');
            $customerImage->addData(
                    [
                        'product_id' => $productId,
                        'title' => $title,
                        'filename' => $filename,
                        'created_at' => time()
                    ]
            )->save();
        } catch (Exception $e)
        {
            Mage::log($e->getMessage(), null, self::LOG_FILE);
            Mage::getSingleton('core/session')->addError(__('Error adding image:') . $e->getMessage());
        }
    }

    /**
     * Create thumbnail for image and save it to thumbnails directory
     *
     * @param string $filename Image path to be resized
     * @param bool $keepRation Keep aspect ratio or not
     * @return bool|string Resized filepath or false if errors were occurred
     */
    public function resizeFile($filename, $keepRation = true)
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

    /**
     * 
     * @param Nemo_AddImage_Model_CustomerImage $customerImage
     */
    public function deleteCustomerImageFiles($filename) {
        $ioFile = new Varien_Io_File();
        $pathToImage = Mage::getBaseDir('media') . DS .
                self::MEDIA_FOLDER . $filename;
        $pathToThumbnail = Mage::getBaseDir('media') . DS .
                self::MEDIA_FOLDER . DS .
                self::THUMBNAILS_FOLDER . $filename;
        if($ioFile->fileExists($pathToImage) && $ioFile->fileExists($pathToThumbnail)) {
            $ioFile->rm($pathToImage);
            $ioFile->rm($pathToThumbnail);
            return true;
        }
        return false;
    }
}
