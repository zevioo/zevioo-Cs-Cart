<?php

/**
 * ZeviooAPI
 *
 
 Aln
 
 */

namespace ZeviooAPI;

spl_autoload_register(function ($class) {
    list($namespace, $classname) = explode('\\', $class);
    if ($namespace == 'ZeviooAPI') {
        include rtrim(__DIR__, '/').'/'.$classname . '.php';
    }
});

class ZeviooAPI
{
  
    private $last_result_raw;
    private $last_result;

    private $requestr;

    private $debug = false;
   
         
	/*
     * @param string $requestClass 
     */
    public function __construct($url, $requestClass = '\ZeviooAPI\ZeviooRequest')
    {
        $this->requestr = new $requestClass($url);

    }
    /**
     * turn on debuging for this class and requester class
     * @param  boolean $status
     */
    public function debug($status = true)
    {
        $this->requestr->setOpt('debug', $status);
        $this->debug = true;
    }
    public function __destruct()
    {

    }
   
  
    public function request($path)
    {
        return $this->_request($path);
    }
   
    
    public function saveSale($sale)
    {
        $result = $this->_request('/custpurchase', $sale->toArray());

        return $result;
    }
    
	 
	public function cancelSale($cancelSale)
    {
        $result = $this->_request('/cnlpurchase', $cancelSale->toArray());

        return  $result;  
    }
	 
	 
    private function _request($path, $data = null)
    {
        list($header, $body) = $this->requestr->post($path, json_encode($data));

        $body =  json_decode($body);
		
        if ($this->debug) {
            $this->last_result_raw = array_merge($header, $body);
            $this->last_result = $result;
        }
		
        return array($header, $body);
    }

    
}
