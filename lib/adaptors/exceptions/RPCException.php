<?php

namespace n00bsys0p;

require_once('AdaptorException.php');

class RPCException extends AdaptorException
{
    protected $message = 'Generic JSON RPC Exception';
}
