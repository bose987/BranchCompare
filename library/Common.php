<?php
class Common {
	
	public static function giveOneDimensionArray( $arrmixData, $strKey ) {
		$arrmixRekeyedData = array();
		foreach( $arrmixData as $strArrayKey => $arrmixInternalData ) {
			$arrmixRekeyedData[$strArrayKey] = $arrmixInternalData[$strKey];
		}
		return $arrmixRekeyedData;
	}
	
	public static function getRelativepath( $strKey = '' ) {
	    $strKey = strtoupper( $strKey );
		if( true == Common::isWindows() ) {
			$arrstrRelativePath = array(
				'' => 'C:/givingForce/',
				'TRUNK' => 'C:/givingForce/trunk',
				'STAGE' => 'C:/givingForce/branches/GF-STAGE',
				'PROD' => 'C:/givingForce/branches/GF-PROD'
			);
		} else {
			$arrstrRelativePath = array(
				'' =>	RELATIVE_PATH_GIVINGFORCE,
			 	'TRUNK' => RELATIVE_PATH_GIVINGFORCE . 'trunk',
				'STAGE' => RELATIVE_PATH_GIVINGFORCE . 'branches/GF-STAGE',
				'PROD' => RELATIVE_PATH_GIVINGFORCE . 'branches/GF-PROD'
			);
		}
		return $arrstrRelativePath[$strKey];
	}
	
	public static function isWindows() {
	
	    if( strtoupper(substr(PHP_OS, 0, 3) ) === 'WIN' ) {
	        return true;    
	    } else {
	        return false;
	    }
	}
} 
