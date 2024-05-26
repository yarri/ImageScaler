<?php
namespace Pupiq\ImageScaler;

class GifFrame {

	protected $duration;
	protected $image;
	protected $temp_filename;

	function __construct(\GdImage $image,int $duration){
		$this->duration = $duration;
	}

	function getDuration(){
		return $this->duration;
	}

	function getTempFilename(){
		if(!$this->temp_filename){
			$temp_filename = Files::GetTempFilename();
			imagegif($this->image,$temp_filename);
			$this->temp_filename;
		}
		return $this->temp_filename;
	}

	function __destruct(){
		if($this->temp_filename){
			Files::Unlink($this->temp_filename);
		}
	}
}
