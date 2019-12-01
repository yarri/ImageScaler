<?php
use Pupiq\ImageScaler, Pupiq\PngquantOptimizer;

class TcPngquantOptimizer extends TcBase {

	function test(){
		$infile = __DIR__."/images/red_200x200.png";
		$is = new ImageScaler($infile);
		$is->scaleTo(100,["output_format" => "png"]);
		$img_unoptimized = Files::GetTempFilename();
		$is->saveTo($img_unoptimized);

		$is = new ImageScaler($infile);
		$is->appendAfterSaveFilter(new PngquantOptimizer());
		$is->scaleTo(100,["output_format" => "png"]);
		$img_optimized = Files::GetTempFilename();
		$is->saveTo($img_optimized);

		$this->assertTrue(filesize($img_optimized)<filesize($img_unoptimized));
	}
}
