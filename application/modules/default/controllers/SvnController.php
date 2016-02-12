<?php
ini_set('max_execution_time', -1 ); 
class SvnController extends Zend_Controller_Action
{
	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
		// action body
	}
	
	public function updateAction()
	{
		$this->_helper->layout->disableLayout();
		if( true == Common::isWindows() ) {
			exec('C:\WINDOWS\system32\cmd.exe /c START svn update ' . Common::getRelativepath( '' ) );
		} else {
			$objSvnClient = new Svn_client( Common::getRelativepath( '' ) );
			$objSvnClient->svn('update');
			echo $objSvnClient->output();
		}
		exit;
	}
	  
	public function getLogAction() {
	
		if( false == isset( $_REQUEST['path'] ) ) {
			echo 0;
			exit;
		}
		$this->_helper->layout->disableLayout();
		
		$strPath = $_REQUEST['path'];
		$strPathType = $_REQUEST['path_type'];
		
		$objSvnClient = new Svn_client( Common::getRelativepath( $strPathType ) . $strPath );
		$objSvnClient->svn('log');
		$objSvnClient->xml();
		$objSvnClient->limit( 20 );
		echo $objSvnClient->output();
		exit;
	}
	
	public function revisionLogAction() {

		if( false == isset( $_REQUEST['rev_id'] ) ) {
			echo 0;
			exit;
		}
		$this->_helper->layout->disableLayout();
		$intRevId = $_REQUEST['rev_id'];
		$objSvnClient = new Svn_client( Common::getRelativepath( '' ) );
		$objSvnClient->svn('log');
		$objSvnClient->xml();
		$objSvnClient->revision( $intRevId );
		$strOutput = $objSvnClient->output();
		$arrmixOutput = json_decode( $strOutput, true );
		if( true == isset( $arrmixOutput['logentry']['paths']['path'] ) ) {
			$strHtml = '<h5>Author: ' . $arrmixOutput['logentry']['author'] . '</h5>';
			$strHtml .= '<h5>Files</h5>';
			$strHtml .= '<div class="revision-div">';
			$strHtml .= '<ul class="revision-files">';
			if( true == is_array( $arrmixOutput['logentry']['paths']['path'] ) ) {
			    
				foreach( $arrmixOutput['logentry']['paths']['path'] as $strPath ) {
					$strHtml .=  '<li><p>' . $strPath . '</p></li>';
				}
			} else {
				$strHtml .= '<li><p>' . $arrmixOutput['logentry']['paths']['path'] . '</p></li>';
			}
			$strHtml .= '</ul>';
			$strHtml .= '</div>';
			
		} else {
			$strHtml = '<p>Error</p>';
		}
		echo $strHtml;
		exit;
	}
	
	public function getDirectoryAction() {

		 if( false == isset( $_REQUEST['path'] ) && false == isset( $_REQUEST['path_type'] ) ) {
			echo 0;
			exit;
		}

		$strPath = $_REQUEST['path'];
		$strPathType = $_REQUEST['path_type'];
		
		$objSvnClient = new Svn_client( Common::getRelativepath( $strPathType ) . $strPath );
		$objSvnClient->svn( 'ls' );
		$objSvnClient->xml();
		echo $objSvnClient->output();
		exit;
	}
}