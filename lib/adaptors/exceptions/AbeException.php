<?php

namespace n00bsys0p;

require_once('AdaptorException.php');

class AbeException extends AdaptorException
{
    protected $message = 'Generic Abe Exception';
}
