<?php
class TcWatermark extends TcBase {

	function test(){
		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/dungeon_master.png");
		$scaler->appendAfterScaleFilter(
			new Pupiq\WatermarkFilter(__DIR__ . "/images/watermark.png")
		);
		$scaler->scaleTo(575,359,["output_format" => "png"]);
		$outfile = Files::GetTempFilename();
		$scaler->saveTo($outfile);
		//
		$this->assertSameImages(__DIR__ . "/images/dungeon_master_watermark_center.png",$outfile);
		unlink($outfile);

		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/dungeon_master.png");
		$scaler->appendAfterScaleFilter(
			new Pupiq\WatermarkFilter(
				__DIR__ . "/images/watermark.png",
				array("opacity" => 50, "position" => "center")
			)
		);
		$scaler->scaleTo(575,359,["output_format" => "png"]);
		$outfile = Files::GetTempFilename();
		$scaler->saveTo($outfile);
		//
		$this->assertSameImages(__DIR__ . "/images/dungeon_master_watermark_center.png",$outfile);
		unlink($outfile);

		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/dungeon_master.png");
		$scaler->appendAfterScaleFilter(
			new Pupiq\WatermarkFilter(
				__DIR__ . "/images/watermark.png",
				array("opacity" => 100, "position" => "left-top")
			)
		);
		$scaler->scaleTo(575,359,["output_format" => "png"]);
		$outfile = Files::GetTempFilename();
		$scaler->saveTo($outfile);
		//
		$this->assertSameImages(__DIR__ . "/images/dungeon_master_watermark_top_left.png",$outfile);
		unlink($outfile);
	}
}
