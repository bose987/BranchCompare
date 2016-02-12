<?php
ini_set('max_execution_time', -1 );
class PdfApiController extends Zend_Controller_Action
{
	public function indexAction()
	{
		try {
			$arrmixUrlInfo = parse_url( $_REQUEST['url'] );
			$client = new PdfCrowd("abhishekb", "224e0bb48a3569047a6858cca800be8c");
			$pdf = $client->convertURI( $_REQUEST['url'] );
			$file = RELATIVE_PATH . 'session-logs/' . time() .'_' . $arrmixUrlInfo['host'] . '.pdf';
			file_put_contents($file, $pdf);
			echo 'converted';
		} catch( PdfcrowdException $why ) {
			echo "Pdfcrowd Error: " . $why;
		}
		exit;
	}
}
