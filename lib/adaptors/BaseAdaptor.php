<?php

namespace n00bsys0p;

require_once(APP_DIR . '/lib/adaptors/interfaces/AdaptorInterface.php');

abstract class BaseAdaptor implements AdaptorInterface
{
   /**
     * Convert a block's bits hex string to an actual diff.
     *
     * @param string $nBits The ['bits'] parameter for a block.
     * @return float
     */
    public static function convertBlockBitsToDiff($nBits)
    {
        $nBits = hexdec($nBits);

        $dDiff = (double) hexdec('0x0000ffff') / (double) ($nBits & hexdec('0x00ffffff'));

        $nShift = ($nBits >> 24) & hexdec('0xff');
        while($nShift < 29)
        {
            $dDiff *= 256.0;
            $nShift++;
        }
        while($nShift > 29)
        {
            $dDiff /= 256.0;
            $nShift--;
        }

        return $dDiff;
    }

    abstract public function getBlockReward();
    abstract public function getBlockHeight();
    abstract public function getDifficulty();
}
