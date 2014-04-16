<?php

namespace n00bsys0p;

require_once(APP_DIR . '/lib/adaptors/RpcAdaptor.php');

/**
 * RPC client implementation for Hirocoin
 *
 * As shown, you just have to implement your coin's
 * subsidy function to change this to work for any
 * coin, and specify this adaptor in your adaptor.yml
 * configuration file.
 */
class HirocoinRpcAdaptor extends RpcAdaptor
{
    /**
     * Ported directly from Hirocoin source code, ignoring txfees
     */
    public function getBlockValue($nHeight)
    {
        $nSubsidy = 400; // Original block reward
        $nSubsidy >>= ($nHeight / 840000);

        return $nSubsidy;
    }
}
