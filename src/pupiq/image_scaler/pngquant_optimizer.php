<?php
namespace Pupiq\ImageScaler;

class PngquantOptimizer extends AfterSaveFilter {

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
		if($options["output_format"]!=="png"){
			return;
		}
		$filename_optimized = \Files::GetTempFilename("pngquant_optimizer_");
		// the --skip-if-larger switch did not work well on img.dumlatek.cz (nothing was saved and error 98 was returned)
		$cmd = escapeshellarg($this->pngquant_binary)                                                                                                                                                                                                  
			. " --quality " . escapeshellarg($this->quality_range)                                                                                                                                                                                  
			. " --force " . escapeshellarg($filename)                                                                                                                                                                                                 
			. " --output " . escapeshellarg($filename_optimized);

		exec($cmd,$output,$ret_val);
		if($ret_val){
			trigger_error("PNG optimization command execution ($cmd) ended with error $ret_val");
		}
		if(file_exists("$filename_optimized") && filesize("$filename_optimized")){
			\Files::MoveFile("$filename_optimized","$filename");
		}
	}
}
