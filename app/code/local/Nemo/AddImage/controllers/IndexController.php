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
        $this->_helper->saveImage('filename', $this->getRequest()->getParam('product_id'));
        $this->_redirectReferer();
    }
}
