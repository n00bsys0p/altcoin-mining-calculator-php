<?php

namespace n00bsys0p;

interface ExplorerInterface
{
    public function getBlockValue($nHeight);
    public function getBlockHeight();
}
