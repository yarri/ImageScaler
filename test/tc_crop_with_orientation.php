<?php
use Pupiq\ImageScaler;

class TcCropWithOrientation extends TcBase {

	function test(){
		// cutting out a red square
		$is = new ImageScaler(__DIR__."/images/red_white_400x200.png");

		$is->scaleTo(200,200,[
			"orientation" => 1,
			"crop" => "top",
		]);

		$output_filename = Files::GetTempFilename();
		$is->saveTo($output_filename);

		$this->assertSameImages(__DIR__."/images/red_200x200.png",$output_filename);

		// cutting out a white square
		$is = new ImageScaler(__DIR__."/images/red_white_400x200.png");

		$is->scaleTo(200,200,[
			"orientation" => 1,
			"crop" => "bottom",
		]);

		$output_filename = Files::GetTempFilename();
		$is->saveTo($output_filename);

		$this->assertSameImages(__DIR__."/images/white_200x200.png",$output_filename);

		// cutting out a white square
		$is = new ImageScaler(__DIR__."/images/red_white_400x200.png");

		$is->scaleTo(200,200,[
			"orientation" => 2,
			"crop" => true,
		]);

		$output_filename = Files::GetTempFilename();
		$is->saveTo($output_filename);

		$this->assertSameImages(__DIR__."/images/red_white_400x200.cropped.png",$output_filename);
	}
}
