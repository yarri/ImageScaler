<?php
namespace Pupiq\ImageScaler;

abstract class AfterSaveFilter {

	abstract function process($filename,$options);
}
