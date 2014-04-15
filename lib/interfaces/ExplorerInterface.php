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
interface ExplorerInterface
{
    /**
     * Return the block value for any given block height
     */
    public function getBlockValue($nHeight);
    /**
     * Return the current max block height
     */
    public function getBlockHeight();
    /**
     * Return the current network block difficulty
     */
    public function getDifficulty();
}
