<?php
ini_set('max_execution_time', -1 ); 
class CompareController extends Zend_Controller_Action
{
	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
 		$boolTrunk = false;
 		$boolStage = false;
 		$boolProd = false;
 		$strHtmlContent = '';

 		$this->m_arrmixTrunkData = $this->getCache( 'trunk' );
		if( true == isset( $this->m_arrmixTrunkData ) ) {
 			$boolTrunk = true;
 		}
 		$this->m_arrmixStageData = $this->getCache( 'stage' );
		if( true == isset( $this->m_arrmixStageData ) ) {
 			$boolStage = true;
 		}
 		$this->m_arrmixProdData = $this->getCache( 'prod' );
		if( true == isset( $this->m_arrmixProdData ) ) {
 			$boolProd = true;
 		}

 		if( $boolTrunk && $boolStage && $boolProd ) {
 			
			$arrstrMergedPath = array_merge( array_keys( $this->m_arrmixTrunkData ), array_keys( $this->m_arrmixStageData ) );
			$arrstrMergedPath = array_merge( array_keys( $this->m_arrmixProdData ), $arrstrMergedPath );
			
			$this->m_arrstrComparionPath = $this->brachComparisonTags( $arrstrMergedPath );
			
			$arrstrMergedPath = array_merge( Common::giveOneDimensionArray( $this->m_arrmixTrunkData, 'title' ), Common::giveOneDimensionArray( $this->m_arrmixStageData, 'title' ) );
			$arrstrMergedPath = array_merge( Common::giveOneDimensionArray( $this->m_arrmixProdData, 'title' ), $arrstrMergedPath );

			$this->m_arrstrIsDir = array_merge( Common::giveOneDimensionArray( $this->m_arrmixTrunkData, 'is_dir' ), Common::giveOneDimensionArray( $this->m_arrmixStageData, 'is_dir' ) );
			$this->m_arrstrIsDir = array_merge( Common::giveOneDimensionArray( $this->m_arrmixProdData, 'is_dir' ), $this->m_arrstrIsDir );
			
			$arrmixTree = $this->buildTree( $arrstrMergedPath );
			$strHtmlContent = $this->buildList( $arrmixTree );
 		}

 		$this->view->boolTrunk = $boolTrunk;
 		$this->view->boolStage = $boolStage;
 		$this->view->boolProd = $boolProd;
 		$this->view->isWindows = Common::isWindows();
 		
		$this->view->htmlContent = $strHtmlContent;
	}
	
	public function brachComparisonTags( $arrstrMergedPath ) {
		$arrstrComparionPath = array();
		
		foreach( $arrstrMergedPath as $strMergedPath ) {
			$strHtmlTag = '';
			
			$strTrunkHash = '';
			$strStageHash = '';
			$strProdHash = '';

			if( true == isset( $this->m_arrmixTrunkData[$strMergedPath]['hash'] ) ) {
				$strTrunkHash = $this->m_arrmixTrunkData[$strMergedPath ]['hash'];
			}

			if( true == isset( $this->m_arrmixStageData[ $strMergedPath ]['hash'] ) ) {
				$strStageHash = $this->m_arrmixStageData[$strMergedPath]['hash'];
			}
			
			if( true == isset( $this->m_arrmixProdData[$strMergedPath]['hash'] ) ) {
				$strProdHash = $this->m_arrmixProdData[$strMergedPath]['hash'];
			}

			if( true == isset( $this->m_arrmixTrunkData[$strMergedPath] ) ) {
				if( $strTrunkHash == $strStageHash || $strTrunkHash == $strProdHash ) { 
					$strHtmlTag .= '<i class="trunk-t"></i>';
				} else if( $strStageHash == '' && $strProdHash == '' ) {
					$strHtmlTag .= '<i class="trunk-t"></i>';
				} else {
					$strHtmlTag .= '<i class="trunk-wa"></i>';
				}
			} else {
				$strHtmlTag .= '<i class="trunk-f"></i>';
			}
			
			if( true == isset( $this->m_arrmixStageData[ $strMergedPath ] ) ) {
				
				if( $strStageHash == $strTrunkHash || $strStageHash == $strProdHash ) { 
					$strHtmlTag .= '<i class="stage-t"></i>';
				} else if( $strTrunkHash == '' && $strProdHash == '' ) {
					$strHtmlTag .= '<i class="stage-t"></i>';
				} else {
					$strHtmlTag .= '<i class="stage-wa"></i>';
				}
			} else {
				$strHtmlTag .= '<i class="stage-f"></i>';
			}
			
			if( true == isset( $this->m_arrmixProdData[ $strMergedPath ] ) ) {
				
				if( $strProdHash == $strTrunkHash || $strProdHash == $strStageHash ) { 
					$strHtmlTag .= '<i class="prod-t"></i>';
				} else if( $strTrunkHash == '' && $strStageHash == '' ) {
					$strHtmlTag .= '<i class="prod-t"></i>';
				} else {
					$strHtmlTag .= '<i class="prod-wa"></i>';
				}
			} else {
				$strHtmlTag .= '<i class="prod-f"></i>';
			}
			$arrstrComparionPath[$strMergedPath] =  $strHtmlTag ;
		} 
		return $arrstrComparionPath;
	}
	
	public function trunkUpdateAction()
	{
		$this->getCache( 'trunk' );
		$this->listFolderFiles( Common::getRelativepath( 'TRUNK' ), Common::getRelativepath( 'TRUNK' ) );
		$this->logCache('trunk');
		echo 'Trunk Synced';
		exit;
	}
		
	public function stageUpdateAction()
	{
		$this->getCache( 'stage' );
		$this->listFolderFiles( Common::getRelativepath( 'STAGE' ),Common::getRelativepath( 'STAGE' ) );
		$this->logCache('stage');
		echo 'Stage Synced';
		exit;
	}
	
	public function prodUpdateAction()
	{
		$this->getCache( 'prod' );
		$this->listFolderFiles( Common::getRelativepath( 'PROD' ),Common::getRelativepath( 'PROD' ) );
		$this->logCache('prod');
		echo 'Prod Synced';
		exit;
	}
	
	public function logCache( $strPathType ) {
		$strLogFile = $strPathType . '-cache.log';
		file_put_contents( RELATIVE_PATH . 'session-logs/'. $strLogFile , json_encode( $this->m_strFileOrFolder ) );
	}
	
	public function getCache( $strPathType ) {
		$strLogFile = $strPathType . '-cache.log';
		if( false == file_exists( RELATIVE_PATH . 'session-logs/'. $strLogFile ) ){

		    return;
		} 
		$strJsonFileContent = file_get_contents( RELATIVE_PATH . 'session-logs/'. $strLogFile );
		$this->m_arrstrCacheData = json_decode( $strJsonFileContent, true );
		return $this->m_arrstrCacheData;
	}

	public function listFolderFiles( $strDirectoryPath, $strRelativePath ) {
		$arrstrFoldersOrFiles = scandir( $strDirectoryPath );
		foreach( $arrstrFoldersOrFiles as $strFolderOrFile ) {
			if( $strFolderOrFile != '.' && $strFolderOrFile != '..' && $strFolderOrFile != '.svn' && '.gitignore' != $strFolderOrFile ) {
				
				$intDir = 0;
				$strFileHash = '';
				$strLastModified = '';
				
				if( true == is_dir( $strDirectoryPath .'/'. $strFolderOrFile ) ) {
					$intDir = 1;
				} else {
					$strLastModified = filemtime( $strDirectoryPath .'/'. $strFolderOrFile );
					if( true == isset( $this->m_arrstrCacheData[str_replace( $strRelativePath . '/', '', $strDirectoryPath .'/'. $strFolderOrFile )]['last_modified'] )
					&& $strLastModified == $this->m_arrstrCacheData[str_replace( $strRelativePath . '/', '', $strDirectoryPath .'/'. $strFolderOrFile )]['last_modified'] ){
						$strFileHash = $this->m_arrstrCacheData[str_replace( $strRelativePath . '/', '', $strDirectoryPath .'/'. $strFolderOrFile )]['hash'];
					} else {
						$strFileHash = hash_file( 'crc32b', $strDirectoryPath .'/'. $strFolderOrFile );
					}
				}

				$this->m_strFileOrFolder[str_replace( $strRelativePath . '/', '', $strDirectoryPath .'/'. $strFolderOrFile )] = array(
					'title' => $strFolderOrFile,
					'is_dir' => $intDir,
					'hash'	=> $strFileHash,
					'last_modified' => $strLastModified
				);
				
				if( true == is_dir( $strDirectoryPath .'/'. $strFolderOrFile ) ) {
					$this->listFolderFiles( $strDirectoryPath.'/'.$strFolderOrFile, $strRelativePath );	
				}
	   		}
		}
	}
	
	function buildTree( $arrStrPathList ) {
		$arrstrPathTree = array();
		foreach ( $arrStrPathList as $strPath => $strTitle ) {
			$arrstrList = explode('/', trim( $strPath, '/' ) );
			$arrstrLastDir = &$arrstrPathTree;
			foreach( $arrstrList as $strDir) {
				$arrstrLastDir =& $arrstrLastDir[$strDir];
			}
			$arrstrLastDir['__title'] = $strTitle;
		}
		return $arrstrPathTree;
	}

	function buildList( $arrmixTree, $prefix = '' ) {
		
		$strUlTags = '';
		foreach( $arrmixTree as $strPath => $arrstrTitle ) {
			$strLiTags = '';
			if ( is_array( $arrstrTitle ) ) {
				if( array_key_exists( '__title', $arrstrTitle ) ) {
					$strLiTags .= ' <span><a href="javascript:;" data-path="/' . $prefix . $strPath . '" ';
					
					if( true == isset( $this->m_arrstrIsDir[$prefix . $strPath] ) && 1 != $this->m_arrstrIsDir[$prefix . $strPath] ) {
						$strLiTags .= ' data-file="1"';
					}
					
					$strLiTags .= '>'. $arrstrTitle['__title'] . $this->m_arrstrComparionPath[$prefix . $strPath] . '</a></span>'; 
					
				} else {
					$strLiTags .= $prefix . $strPath . '/';
				}
				$strLiTags .= $this->buildList( $arrstrTitle, $prefix . $strPath . '/' );
				
				$strCloseFolder = '';
				if( true == isset( $this->m_arrstrIsDir[$prefix . $strPath] ) && 1 == $this->m_arrstrIsDir[$prefix . $strPath] ) {
					$strCloseFolder = 'data-options="state:\'closed\'"';
				}
				
				$strUlTags .= strlen( $strLiTags ) ? '<li ' . $strCloseFolder . '>' . $strLiTags. '</li>' : '';
			}
		}
		return strlen( $strUlTags ) ? '<ul>' . $strUlTags . '</ul>' : '';
	}
}