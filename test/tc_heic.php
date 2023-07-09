<?php
class TcHeic extends TcBase {

	function test(){
		if(!in_array("HEIC",Imagick::queryFormats())){
			fwrite(STDERR, "!!! There is no HEIC support in this Imagick installation");
			$this->assertTrue(true);
			return;
		}

		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/penguin.heic");

		$this->assertEquals("image/heic",$scaler->getMimeType());
		$this->assertEquals(0,$scaler->getOrientation());
		$this->assertEquals(386,$scaler->getImageWidth());
		$this->assertEquals(395,$scaler->getImageHeight());

		$scaler->scaleTo(100,100);
		$outfile = Files::GetTempFilename();
		$scaler->saveTo($outfile);

		$scaler_out = new Pupiq\ImageScaler($outfile);
		$this->assertEquals("image/heic",$scaler_out->getMimeType());
		$this->assertEquals(0,$scaler_out->getOrientation());
		$this->assertEquals(100,$scaler_out->getImageWidth());
		$this->assertEquals(100,$scaler_out->getImageHeight());

		unlink($outfile);
	}
}
