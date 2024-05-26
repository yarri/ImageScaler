<?php
class TcGif extends TcBase {

	function test(){
		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/dungeon_master.gif");

		$this->assertEquals("image/gif",$scaler->getMimeType());
		$this->assertEquals(0,$scaler->getOrientation());
		$this->assertEquals(575,$scaler->getImageWidth());
		$this->assertEquals(359,$scaler->getImageHeight());

		$scaler->scaleTo(100,100);
		$outfile = Files::GetTempFilename();
		$scaler->saveTo($outfile);

		$scaler_out = new Pupiq\ImageScaler($outfile);
		$this->assertEquals("image/gif",$scaler_out->getMimeType());
		$this->assertEquals(0,$scaler_out->getOrientation());
		$this->assertEquals(100,$scaler_out->getImageWidth());
		$this->assertEquals(100,$scaler_out->getImageHeight());

		unlink($outfile);
	}

	function test_animation(){
		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/animation.gif");

		$this->assertEquals("image/gif",$scaler->getMimeType());
		$this->assertEquals(0,$scaler->getOrientation());
		$this->assertEquals(625,$scaler->getImageWidth());
		$this->assertEquals(625,$scaler->getImageHeight());

		$scaler->scaleTo(100,100);
		$outfile = Files::GetTempFilename();
		$scaler->saveTo($outfile);

		$scaler_out = new Pupiq\ImageScaler($outfile);
		$this->assertEquals("image/gif",$scaler_out->getMimeType());
		$this->assertEquals(0,$scaler_out->getOrientation());
		$this->assertEquals(100,$scaler_out->getImageWidth());
		$this->assertEquals(100,$scaler_out->getImageHeight());

		unlink($outfile);
	}

	function test__isAnimatedGif(){
		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/animation.gif");
		$this->assertEquals(true,$scaler->_isAnimationGif());

		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/dungeon_master.gif");
		$this->assertEquals(false,$scaler->_isAnimationGif());

		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/dungeon_master.png");
		$this->assertEquals(false,$scaler->_isAnimationGif());
	}

	function test__extractGifFrames(){
		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/animation.gif");
		$frames = $scaler->_extractGifFrames();
		$this->assertEquals(3,sizeof($frames));

		$this->assertEquals(60,$frames[0]->getDuration());
		$this->assertEquals(60,$frames[1]->getDuration());
		$this->assertEquals(60,$frames[2]->getDuration());
	}
}
