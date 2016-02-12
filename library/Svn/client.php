<?php 
class Svn_client{
	
	public $m_path;
	public $m_strCommand = 'svn';
	
	public function __construct( $path = '' ) {
		if( '' != $path )
		$this->m_path = '"' . $path . '"';
	}
	
	public function svn( $strCommand ) {
		$this->m_strCommand .= ' '. $strCommand;
	}
	
	public function limit( $intLimit ) {
		$this->m_strCommand .= ' --limit '. $intLimit;
	}

	public function xml() {
		$this->m_strCommand .= ' --xml';
	}
	
	public function revision( $intRevId ) {
		$this->m_strCommand .= ' --verbose -r ' . $intRevId;
	}
	
	function winOutput() {
	    $strCommand = $this->m_strCommand . ' ' . $this->m_path;
		return shell_exec( $strCommand );
	}
	
	function unixOutput() {
        $strCommand = $this->m_strCommand . ' ' . $this->m_path;
        $objSvnSsh = new Svn_Ssh();
        return $objSvnSsh->execute( $strCommand );
	}
	
	function output() {
	    
	    if( true == Common::isWindows() ) {
	        $strXmlOutput = $this->winOutput();
	    } else {
	        $strXmlOutput = $this->unixOutput();
	    }

		$strXmlOutput = str_replace( array( '\n', '\r', '\t' ), '', $strXmlOutput );
		$strXml = @simplexml_load_string( $strXmlOutput );
		if( $strXml ) {
			$strJson = json_encode( $strXml );
		} else {
			$strJson = json_encode( array( 'msg' => 'No data found' ) );
		} 
		return $strJson;
	}
}