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

	function test_automatic_orientation_detecting(){
		$is = new ImageScaler(__DIR__ . "/images/pigeon.jpg");
		$this->assertEquals(0,$is->getOrientation());

		$is = new ImageScaler(__DIR__ . "/images/valachia.jpg");
		$this->assertEquals(3,$is->getOrientation());
	}

	function test_prepareScalingData(){
		$is = new ImageScaler(__DIR__."/images/dungeon_master.png");
		
		list($width,$heigh,$options) = $is->prepareScalingData(100);
		$this->assertEquals(100,$width);
		$this->assertEquals(62,$heigh);

		list($width,$heigh,$options) = $is->prepareScalingData(100,array("orientation" => 1));
		$this->assertEquals(100,$width);
		$this->assertEquals(160,$heigh);

		list($width,$heigh,$options) = $is->prepareScalingData(null,100);
		$this->assertEquals(160,$width);
		$this->assertEquals(100,$heigh);

		// enlargement
		list($width,$heigh,$options) = $is->prepareScalingData(1000);
		$this->assertEquals(1000,$width);
		$this->assertEquals(624,$heigh);

		list($width,$heigh,$options) = $is->prepareScalingData(101,102);
		$this->assertEquals(101,$width);
		$this->assertEquals(102,$heigh);

		// retaining the original dimensions
		list($width,$heigh,$options) = $is->prepareScalingData(null,null);
		$this->assertEquals(575,$width);
		$this->assertEquals(359,$heigh);
		//
		list($width,$heigh,$options) = $is->prepareScalingData();
		$this->assertEquals(575,$width);
		$this->assertEquals(359,$heigh);
		//
		list($width,$heigh,$options) = $is->prepareScalingData(array("orientation" => 1));
		$this->assertEquals(359,$width);
		$this->assertEquals(575,$heigh);

		list($width,$heigh,$options) = $is->prepareScalingData(100,100,array("keep_aspect" => true));
		$this->assertEquals(100,$width);
		$this->assertEquals(62,$heigh);
		$this->assertEquals(0,$options["x"]);
		$this->assertEquals(0,$options["y"]);
		$this->assertEquals(575,$options["width"]);
		$this->assertEquals(359,$options["height"]);

		list($width,$heigh,$options) = $is->prepareScalingData(100,50,array("keep_aspect" => true));
		$this->assertEquals(80,$width);
		$this->assertEquals(50,$heigh);
		$this->assertEquals(0,$options["x"]);
		$this->assertEquals(0,$options["y"]);
		$this->assertEquals(575,$options["width"]);
		$this->assertEquals(359,$options["height"]);

		list($width,$heigh,$options) = $is->prepareScalingData(100,100,array("crop" => true));
		$this->assertEquals(100,$width);
		$this->assertEquals(100,$heigh);
		$this->assertEquals(107,$options["x"]);
		$this->assertEquals(0,$options["y"]);
		$this->assertEquals(359,$options["width"]);
		$this->assertEquals(359,$options["height"]);

		// The default background_color...

		// ... is transparent for png images
		$is = new ImageScaler(__DIR__."/images/dungeon_master.png");
		list($width,$heigh,$options) = $is->prepareScalingData(100);
		$this->assertEquals("transparent",$options["background_color"]);

		// ... is #ffffff for jpeg images
		$is = new ImageScaler(__DIR__."/images/pigeon.jpg");
		list($width,$heigh,$options) = $is->prepareScalingData(100);
		$this->assertEquals("#ffffff",$options["background_color"]);

		// ... is transparent for webp images
		$is = new ImageScaler(__DIR__."/images/penguin.webp");
		list($width,$heigh,$options) = $is->prepareScalingData(100);
		$this->assertEquals("transparent",$options["background_color"]);
	}

	function test_not_image(){
		$exeption_thrown = false;
		try {
			$is = new ImageScaler(__FILE__);
		} catch(Exception $e) {
			$exeption_thrown = true;
		}
		$this->assertEquals(true,$exeption_thrown);
	}
}
