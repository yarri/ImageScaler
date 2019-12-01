<?php
namespace Pupiq;

class PngquantOptimizer {

	protected $pngquant_binary;
	protected $quality_range;

	function __construct($options = []){
		$options += [
			"pngquant_binary" => "pngquant",
			"quality_range" => "70-90",
		];

		$this->pngquant_binary = $options["pngquant_binary"];
		$this->quality_range = $options["quality_range"];
	}

	function process($filename,$options){
		if($options["output_format"]=="png"){
			$filename_optimized = \Files::GetTempFilename("pngquant_optimizer_");
			// prepinac --skip-if-larger nefungoval dobre na img.dumlatek.cz (nic se neulozilo a vratila se chyba 98)
			$cmd = "$this->pngquant_binary --quality $this->quality_range --force $filename --output $filename_optimized";
			exec($cmd,$output,$ret_val);
			if($ret_val){
				trigger_error("PNG optimization command execution ($cmd) ended with error $ret_val");
			}
			if(file_exists("$filename_optimized") && filesize("$filename_optimized")){
				\Files::MoveFile("$filename_optimized","$filename");
			}
		}
	}
}
