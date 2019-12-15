<?php
use Pupiq\ImageScaler;

class TcTransparency extends TcBase {

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
	}
}
