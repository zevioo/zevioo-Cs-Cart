<?php

/**
 * ZeviooAPI
 *Aln
 */

namespace ZeviooAPI;

class ZeviooSale extends ZeviooObject
{
   
    public function save ()
    {
        return $this->zevioo->saveSale($this);
    }
}
