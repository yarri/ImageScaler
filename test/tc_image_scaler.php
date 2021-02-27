<?php
use Pupiq\ImageScaler;

class TcImageScaler extends TcBase {

	function test(){
		$is = new ImageScaler(__DIR__."/images/dungeon_master.png");
		$this->assertEquals("image/png",$is->getMimeType());
		$this->assertEquals(__DIR__."/images/dungeon_master.png",$is->getFileName());
		$this->assertEquals(575,$is->getImageWidth());
		$this->assertEquals(359,$is->getImageHeight());
		$is->setOrientation(1);
		$this->assertEquals(359,$is->getImageWidth());
		$this->assertEquals(575,$is->getImageHeight());
		$is->setOrientation(2);
		$this->assertEquals(575,$is->getImageWidth());
		$this->assertEquals(359,$is->getImageHeight());

		$is = new ImageScaler(__DIR__."/images/pigeon.jpg");
		$this->assertEquals("image/jpeg",$is->getMimeType());
		$this->assertEquals(153,$is->getImageWidth());
		$this->assertEquals(331,$is->getImageHeight());

		$output_filename = Files::GetTempFilename();

		$is->scaleTo(100,100);
		$is->saveTo($output_filename);
		//
		$is2 = new ImageScaler($output_filename);
		$this->assertEquals(100,$is2->getImageWidth());
		$this->assertEquals(100,$is2->getImageHeight());

		$is->scaleTo(100,100,array("keep_aspect" => true));
		$is->saveTo($output_filename);
		//
		$is2 = new ImageScaler($output_filename);
		$this->assertEquals(46,$is2->getImageWidth());
		$this->assertEquals(100,$is2->getImageHeight());

		$is->scaleTo(100,100,array("keep_aspect" => true, "orientation" => 1));
		$is->saveTo($output_filename);
		//
		$is2 = new ImageScaler($output_filename);
		$this->assertEquals(100,$is2->getImageWidth());
		$this->assertEquals(46,$is2->getImageHeight());

		unlink($output_filename);

		// Method is scaleTo() is not gonna be called
		$is = new ImageScaler(__DIR__."/images/dungeon_master.png");
		$output_filename = Files::GetTempFilename();
		$is->saveTo($output_filename);
		$this->assertEquals("image/png",Files::DetermineFileType($output_filename));
		unlink($output_filename);
		//
		$is = new ImageScaler(__DIR__."/images/pigeon.jpg");
		$output_filename = Files::GetTempFilename();
		$is->saveTo($output_filename);
		$this->assertEquals("image/jpeg",Files::DetermineFileType($output_filename));
		unlink($output_filename);
	}
}
