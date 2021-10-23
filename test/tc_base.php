<?php
class TcBase extends TcSuperbase {

	function assertSameImages($image1_filename,$image2_filename){
		$image1 = new Imagick($image1_filename);
		$image2 = new Imagick($image2_filename);

		$result = $image1->compareImages($image2, Imagick::METRIC_MEANSQUAREERROR);
		$this->assertEquals(0.0,round($result[1]*1000.0),"$image2_filename is not same as $image1_filename");
	}

	function assertNotSameImages($image1_filename,$image2_filename){
		$image1 = new Imagick($image1_filename);
		$image2 = new Imagick($image2_filename);

		$result = $image1->compareImages($image2, Imagick::METRIC_MEANSQUAREERROR);
		$this->assertNotEquals(0.0,round($result[1]*1000.0));
	}

	function tearDown(){
		Files::RecursiveUnlinkDir(TEMP);
		Files::Mkdir(TEMP);
	}
}
