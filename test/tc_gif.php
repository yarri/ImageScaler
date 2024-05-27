<?php
class TcGif extends TcBase {

	function test_transparency(){
		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/transparent_200x200.gif");
		$scaler->scaleTo(100,100);
		$outfile_gif = Files::GetTempFilename();
		$scaler->saveTo($outfile_gif);

		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/transparent_200x200.gif");
		$scaler->scaleTo(100,100,["background_color" => "#ffffff"]);
		$outfile_gif_white = Files::GetTempFilename();
		$scaler->saveTo($outfile_gif_white);

		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/transparent_200x200.png");
		$scaler->scaleTo(100,100);
		$outfile_png = Files::GetTempFilename();
		$scaler->saveTo($outfile_png);

		$this->assertSameImages($outfile_gif,$outfile_png);
		$this->assertNotSameImages($outfile_gif,$outfile_gif_white);

		unlink($outfile_gif);
		unlink($outfile_gif_white);
		unlink($outfile_png);
	}

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

		$scaler->setOrientation(1);
		$scaler->appendAfterScaleFilter(new Pupiq\ImageScaler\GrayscaleFilter());

		$scaler->scaleTo(100,100);
		$outfile = Files::GetTempFilename();
		$scaler->saveTo($outfile);

		$scaler_out = new Pupiq\ImageScaler($outfile);
		$this->assertEquals("image/gif",$scaler_out->getMimeType());
		$this->assertEquals(0,$scaler_out->getOrientation());
		$this->assertEquals(100,$scaler_out->getImageWidth());
		$this->assertEquals(100,$scaler_out->getImageHeight());
		$this->assertTrue($scaler_out->_isAnimated());

		unlink($outfile);
	}

	function test__isAnimated(){
		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/animation.gif");
		$this->assertEquals(true,$scaler->_isAnimated());

		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/dungeon_master.gif");
		$this->assertEquals(false,$scaler->_isAnimated());

		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/dungeon_master.png");
		$this->assertEquals(false,$scaler->_isAnimated());
	}

	function test__extractFrames(){
		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/animation.gif");
		$frames = $scaler->_extractFrames();
		$this->assertEquals(3,sizeof($frames));

		$this->assertEquals(0.6,$frames[0]->getDuration());
		$this->assertEquals(0.6,$frames[1]->getDuration());
		$this->assertEquals(0.6,$frames[2]->getDuration());
	}
}
