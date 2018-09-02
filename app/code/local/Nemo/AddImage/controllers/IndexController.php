<?php

/**
 * @author maciej
 */

/**
 * CustomerImage Controller
 */
class Nemo_AddImage_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     *
     * @var type Nemo_AddImage_Helper_Data
     */
    private $_helper;

    /**
     * Constructor
     * 
     * @param \Zend_Controller_Request_Abstract   $request
     * @param \Zend_Controller_Response_Abstract  $response
     * @param array                               $invokeArgs
     */
    public function __construct(\Zend_Controller_Request_Abstract $request, \Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);
        $this->_helper = Mage::helper('nemo_addimage');
    }

    /**
     * Save uploaded file and data
     * 
     * @throws Exception
     */
    public function saveUploadFileAction()
    {
        //$this->_helper->saveImage('filename', $this->getRequest()->getParam('product_id'));
        $productId = $this->getRequest()->getParam('product_id');
        try {
            if((int) $productId === 0) {
                throw new Exception(__('Wrong product id'));
            }
            $customerImage = Mage::getModel('nemo_addimage/customerImage');
            $customerImage->setData('product_id',$productId);
            $customerImage->setData('filename','filename');
            $customerImage->setData('created_at',time());//@todo change to Mage created time
            $customerImage->save('filename');
            Mage::getSingleton('core/session')->addSuccess(__('Image added, thank you. Your image is awaiting moderation.'));
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, "shorty_port");;
            Mage::getSingleton('core/session')->addError(__('Error adding image:') . $e->getMessage());
        }
        $this->_redirectReferer();
    }
}
