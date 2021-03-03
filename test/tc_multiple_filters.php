<?php
class TcMultipleFilters extends TcBase {

	function test(){
		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/dungeon_master.png");
		$scaler->appendAfterScaleFilter(
			new Pupiq\ImageScaler\GrayscaleFilter()
		);
		$scaler->appendAfterScaleFilter(
			new Pupiq\ImageScaler\WatermarkFilter(__DIR__ . "/images/watermark_colored.png")
		);
		$scaler->scaleTo(575,359,["output_format" => "png"]);
		$outfile = Files::GetTempFilename();
		$scaler->saveTo($outfile);
		//
		$this->assertSameImages(__DIR__ . "/images/dungeon_master_bw_watermark_colored.png",$outfile);
		unlink($outfile);
	}
}
