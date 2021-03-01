<?php
class TcWebp extends TcBase {

	function test(){
		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/penguin.webp");

		$this->assertEquals("image/webp",$scaler->getMimeType());
		$this->assertEquals(0,$scaler->getOrientation());
		$this->assertEquals(386,$scaler->getImageWidth());
		$this->assertEquals(395,$scaler->getImageHeight());

		$scaler->scaleTo(100,100);
		$outfile = Files::GetTempFilename();
		$scaler->saveTo($outfile);

		$scaler_out = new Pupiq\ImageScaler($outfile);
		$this->assertEquals("image/webp",$scaler->getMimeType());
		$this->assertEquals(0,$scaler->getOrientation());
		$this->assertEquals(100,$scaler->getImageWidth());
		$this->assertEquals(100,$scaler->getImageHeight());

		unlink($outfile);
	}
}
