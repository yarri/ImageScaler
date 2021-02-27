<?php
namespace Pupiq;

abstract class AfterSaveFilter {

	abstract function process($filename,$options);
}
