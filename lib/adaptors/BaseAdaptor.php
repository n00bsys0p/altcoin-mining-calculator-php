<?php

namespace n00bsys0p;

require_once(APP_DIR . '/lib/adaptors/interfaces/AdaptorInterface.php');

abstract class BaseAdaptor implements AdaptorInterface
{
    abstract public function getBlockReward();
    abstract public function getBlockHeight();
    abstract public function getDifficulty();
}
