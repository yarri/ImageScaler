<?php
use Pupiq\ImageScaler;

class TcImageScaler extends TcBase {

	function test_transparency(){
		
		// -- transparent_socks_400x400.png

		$is = new ImageScaler(__DIR__."/images/transparent_socks_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png"]);
		$img_1 = Files::WriteToTemp($is->getContent());

		$is = new ImageScaler(__DIR__."/images/transparent_socks_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png","background_color" => "transparent"]);
		$img_2 = Files::WriteToTemp($is->getContent());

		$is = new ImageScaler(__DIR__."/images/transparent_socks_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png","background_color" => "#99ff99"]);
		$img_3 = Files::WriteToTemp($is->getContent());

		$is = new ImageScaler(__DIR__."/images/transparent_socks_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png","background_color" => "#ffffff"]);
		$img_4 = Files::WriteToTemp($is->getContent());

		$this->assertSameImages($img_1,$img_2);
		$this->assertNotSameImages($img_1,$img_3);
		$this->assertNotSameImages($img_3,$img_4);

		// -- white_socks_400x400.png

		$is = new ImageScaler(__DIR__."/images/white_socks_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png"]);
		$img_1 = Files::WriteToTemp($is->getContent());

		$is = new ImageScaler(__DIR__."/images/white_socks_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png","background_color" => "transparent"]);
		$img_2 = Files::WriteToTemp($is->getContent());

		$is = new ImageScaler(__DIR__."/images/white_socks_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png","background_color" => "#99ff99"]);
		$img_3 = Files::WriteToTemp($is->getContent());

		$is = new ImageScaler(__DIR__."/images/white_socks_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png","background_color" => "#ffffff"]);
		$img_4 = Files::WriteToTemp($is->getContent());

		$this->assertSameImages($img_1,$img_2);
		$this->assertSameImages($img_1,$img_3);
		$this->assertSameImages($img_3,$img_4);

		// -- transparent_400x400.png

		$is = new ImageScaler(__DIR__."/images/transparent_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png"]);
		$img_1 = Files::WriteToTemp($is->getContent());

		$is = new ImageScaler(__DIR__."/images/transparent_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png","background_color" => "transparent"]);
		$img_2 = Files::WriteToTemp($is->getContent());

		$is = new ImageScaler(__DIR__."/images/transparent_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png","background_color" => "#ff0000"]);
		$img_3 = Files::WriteToTemp($is->getContent());

		$is = new ImageScaler(__DIR__."/images/transparent_400x400.png");
		$is->scaleTo(200,200,["output_format" => "png","background_color" => "#ffffff"]);
		$img_4 = Files::WriteToTemp($is->getContent());

		$this->assertSameImages($img_1,$img_2);
		$this->assertNotSameImages($img_1,$img_3);
		$this->assertNotSameImages($img_3,$img_4);

		$this->assertSameImages($img_1,__DIR__."/images/transparent_200x200.png");
		$this->assertSameImages($img_3,__DIR__."/images/red_200x200.png");
		$this->assertSameImages($img_4,__DIR__."/images/white_200x200.png");

		Files::RecursiveUnlinkDir(TEMP);
		Files::Mkdir(TEMP);
	}

	function assertSameImages($image1_filename,$image2_filename){
		$image1 = new Imagick($image1_filename);
		$image2 = new Imagick($image2_filename);

		$result = $image1->compareImages($image2, Imagick::METRIC_MEANSQUAREERROR);
		$this->assertEquals(0.0,$result[1]*1000.0);
	}

	function assertNotSameImages($image1_filename,$image2_filename){
		$image1 = new Imagick($image1_filename);
		$image2 = new Imagick($image2_filename);

		$result = $image1->compareImages($image2, Imagick::METRIC_MEANSQUAREERROR);
		$this->assertNotEquals(0.0,$result[1]*1000.0);
	}
}
