<?php
use Pupiq\ImageScaler, Pupiq\GrayscaleFilter;

class TcGrayscaleFilter extends TcBase {

	function test(){
		$infile = __DIR__."/images/dungeon_master.png";

		$is = new ImageScaler($infile);
		$is->appendAfterScaleFilter(new GrayscaleFilter());
		$is->scaleTo(575,359,["output_format" => "png"]);
		$outfile = Files::GetTempFilename();
		$is->saveTo($outfile);

		$this->assertSameImages(__DIR__."/images/dungeon_master_bw.png",$outfile);
	}
}
