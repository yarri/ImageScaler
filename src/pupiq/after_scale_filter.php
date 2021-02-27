<?php
namespace Pupiq;

abstract class AfterScaleFilter {

	abstract function process($imagick,$options);
}
