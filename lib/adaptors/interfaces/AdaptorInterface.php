<?php

namespace n00bsys0p;

/**
 * Explorer Interface
 *
 * The name explorer may be slightly misleading; as
 * long as the class provides a means of returning the
 * current block height, current block reward value and
 * the current network block difficulty it doesn't matter
 * what it is.
 */
interface AdaptorInterface
{
    /**
     * Return the CURRENT block reward
     */
    public function getBlockReward();
    /**
     * Return the current max block height
     */
    public function getBlockHeight();
    /**
     * Return the current network block difficulty
     */
    public function getDifficulty();
    /**
     * Convert a block's nBits hex to a usable difficulty
     */
    public static function convertBlockBitsToDiff($nBits);
}
