<?php
namespace Pupiq\ImageScaler;

abstract class AfterScaleFilter {

	abstract function process($imagick,$options);
}
