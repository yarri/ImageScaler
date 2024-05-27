<?php
namespace Pupiq\ImageScaler;

class GifFrame {

	protected $duration;
	protected $image; // GdImage
	protected $imagick; // Imagick
	protected $temp_filename;

	/**
	 * @param GdImage $image
	 * @param float $duration in seconds
	 */
	function __construct($image,float $duration){
		$this->image = $image; // GdImage
		$this->duration = $duration;
	}

	function __destruct(){
		if($this->temp_filename){
			\Files::Unlink($this->temp_filename);
		}
	}

	function getDuration(){
		return $this->duration;
	}

	function getTempFilename(){
		if(!$this->temp_filename){
			$temp_filename = \Files::GetTempFilename("gif_frame_");
			imagegif($this->image,$temp_filename);
			$this->temp_filename = $temp_filename;
		}
		return $this->temp_filename;
	}

	function setImagick($imagick){
		$this->imagick = $imagick;
	}

	function getImagick(){
		return $this->imagick;
	}
}
