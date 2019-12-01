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

	function test_transparency(){
		
		// -- transparent_socks_400x400.png

		$is = new ImageScaler(__DIR__."/images/transparent_socks_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png"]);
		$img_1 = Files::GetTempFilename();
		$is->saveTo($img_1);

		$is = new ImageScaler(__DIR__."/images/transparent_socks_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png","background_color" => "transparent"]);
		$img_2 = Files::GetTempFilename();
		$is->saveTo($img_2);

		$is = new ImageScaler(__DIR__."/images/transparent_socks_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png","background_color" => "#99ff99"]);
		$img_3 = Files::GetTempFilename();
		$is->saveTo($img_3);

		$is = new ImageScaler(__DIR__."/images/transparent_socks_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png","background_color" => "#ffffff"]);
		$img_4 = Files::GetTempFilename();
		$is->saveTo($img_4);

		$this->assertSameImages($img_1,$img_2);
		$this->assertNotSameImages($img_1,$img_3);
		$this->assertNotSameImages($img_3,$img_4);

		// -- white_socks_400x400.png

		$is = new ImageScaler(__DIR__."/images/white_socks_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png"]);
		$img_1 = Files::GetTempFilename();
		$is->saveTo($img_1);

		$is = new ImageScaler(__DIR__."/images/white_socks_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png","background_color" => "transparent"]);
		$img_2 = Files::GetTempFilename();
		$is->saveTo($img_2);

		$is = new ImageScaler(__DIR__."/images/white_socks_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png","background_color" => "#99ff99"]);
		$img_3 = Files::GetTempFilename();
		$is->saveTo($img_3);

		$is = new ImageScaler(__DIR__."/images/white_socks_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png","background_color" => "#ffffff"]);
		$img_4 = Files::GetTempFilename();
		$is->saveTo($img_4);

		$this->assertSameImages($img_1,$img_2);
		$this->assertSameImages($img_1,$img_3);
		$this->assertSameImages($img_3,$img_4);

		// -- transparent_400x400.png

		$is = new ImageScaler(__DIR__."/images/transparent_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png"]);
		$img_1 = Files::GetTempFilename();
		$is->saveTo($img_1);

		$is = new ImageScaler(__DIR__."/images/transparent_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png","background_color" => "transparent"]);
		$img_2 = Files::GetTempFilename();
		$is->saveTo($img_2);

		$is = new ImageScaler(__DIR__."/images/transparent_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png","background_color" => "#ff0000"]);
		$img_3 = Files::GetTempFilename();
		$is->saveTo($img_3);

		$is = new ImageScaler(__DIR__."/images/transparent_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png","background_color" => "#ffffff"]);
		$img_4 = Files::GetTempFilename();
		$is->saveTo($img_4);

		$this->assertSameImages($img_1,$img_2);
		$this->assertNotSameImages($img_1,$img_3);
		$this->assertNotSameImages($img_3,$img_4);

		$this->assertSameImages($img_1,__DIR__."/images/transparent_200x200.png");
		$this->assertSameImages($img_3,__DIR__."/images/red_200x200.png");
		$this->assertSameImages($img_4,__DIR__."/images/white_200x200.png");

		unset($is);
	}

}
