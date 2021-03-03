<?php
class TcWatermark extends TcBase {

	function test(){
		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/dungeon_master.png");
		$scaler->appendAfterScaleFilter(
			new Pupiq\ImageScaler\WatermarkFilter(__DIR__ . "/images/watermark.png")
		);
		$scaler->scaleTo(575,359);
		$outfile = Files::GetTempFilename();
		$scaler->saveTo($outfile);
		//
		$this->assertSameImages(__DIR__ . "/images/dungeon_master_watermark_center.png",$outfile);
		unlink($outfile);

		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/dungeon_master.png");
		$scaler->appendAfterScaleFilter(
			new Pupiq\ImageScaler\WatermarkFilter(
				__DIR__ . "/images/watermark.png",
				array("opacity" => 50, "position" => "center")
			)
		);
		$scaler->scaleTo(575,359);
		$outfile = Files::GetTempFilename();
		$scaler->saveTo($outfile);
		//
		$this->assertSameImages(__DIR__ . "/images/dungeon_master_watermark_center.png",$outfile);
		unlink($outfile);

		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/dungeon_master.png");
		$scaler->appendAfterScaleFilter(
			new Pupiq\ImageScaler\WatermarkFilter(
				__DIR__ . "/images/watermark.png",
				array("opacity" => 100, "position" => "left-top")
			)
		);
		$scaler->scaleTo(575,359);
		$outfile = Files::GetTempFilename();
		$scaler->saveTo($outfile);
		//
		$this->assertSameImages(__DIR__ . "/images/dungeon_master_watermark_left_top.png",$outfile);
		unlink($outfile);

		$scaler = new Pupiq\ImageScaler(__DIR__ . "/images/dungeon_master.png");
		$scaler->appendAfterScaleFilter(
			new Pupiq\ImageScaler\WatermarkFilter(
				__DIR__ . "/images/watermark.png",
				array("opacity" => 100, "position" => "right-bottom")
			)
		);
		$scaler->scaleTo(287,179);
		$outfile = Files::GetTempFilename();
		$scaler->saveTo($outfile);
		//
		$this->assertSameImages(__DIR__ . "/images/dungeon_master_small_watermark_right_botton.png",$outfile);
		unlink($outfile);
	}
}
