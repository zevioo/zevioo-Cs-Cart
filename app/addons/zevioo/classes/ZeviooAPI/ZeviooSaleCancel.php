<?php

/**
 * ZeviooAPI 
 * Aln
 * 
 */

namespace ZeviooAPI;

class ZeviooSaleCancel extends ZeviooObject
{
    
    public function save ()
    {
        return $this->zevioo->cancelSale($this);
    }
    
}
