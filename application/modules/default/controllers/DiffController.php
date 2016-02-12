<?php
ini_set('max_execution_time', -1 ); 
class DiffController extends Zend_Controller_Action
{
	
    public function init()
	{
		/* Initialize action controller here */
	}
    
	public function indexAction()
	{
	    if( false == isset( $_REQUEST['path'] ) || false == isset( $_REQUEST['path_type_left'] ) || false == isset( $_REQUEST['path_type_right'] ) ) {
	        echo 0;
	        exit;
	    }
	    $this->_helper->layout->disableLayout();
	    
	    $this->view->path = $_REQUEST['path'];
	    $this->view->relativeLeftPath = Common::getRelativepath( $_REQUEST['path_type_left'] ); 
	    $this->view->relativeRightPath = Common::getRelativepath( $_REQUEST['path_type_right'] );
	}
	
	public function getContentAction() {
	    if( false == isset( $_REQUEST['path'] ) ) {
	        echo 0;
	        exit;
	    }
	    $this->_helper->layout->disableLayout();
	    
	    $strPath = $_REQUEST['path'];
	    echo file_get_contents( $strPath, true );
	    exit;
	
	}
	
	public function winMergeAction() {
	    if( false == isset( $_REQUEST['path'] ) ) {
	        echo 0;
	        exit;
	    }
	    
	    $path = $_REQUEST['path'];
	    
	    $strCommand = '"C:\Program Files (x86)\WinMerge\WinMergeU.exe" /minimize';
	    $strCommand .= ' "'. Common::getRelativepath( $_REQUEST['path_type_left'] ) . $path .  '" ';
	    $strCommand .= ' "'. Common::getRelativepath( $_REQUEST['path_type_right'] ) . $path .  '" ';

	    exec($strCommand);
	    exit;
	}
}