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
	}
}
