<?php

/**
 * Base UPS Class
 * 
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/
 * @version		0.2.0
 * @build		20120823
 */

require_once 'Ups_Base_Response.php';

abstract class Base_Ups
{
    /**
	 * Base API URL
	 * 
	 * @var string
	 */
	 
	private $base_url = "https://www.ups.com/ups.app/xml";
	
	
    /**
	 * USP Account Access Key
	 * 
	 * @var string
	 */
    
    protected $access_key;
   
   
    /**
	 * USP Account Username
	 * 
	 * @var string
	 */
    
    protected $username;
   
   
    /**
	 * USP Account Password
	 * 
	 * @var string
	 */
    
    protected $password;
   
   
    /**
	 * UPS Account Number
	 * 
	 * @var string
	 */
    
    protected $account_number;
   
   
    /**
     * Contructor
     *
     * @access	public
     * @param	array 	Pass object properties as array keys to set default values
     * @return	void
     */
   	    	
    public function __construct($data = array())
    {
	    foreach($data as $key => $value)
	    {
		    if(property_exists($this, $key))
		    {
			    $this->$key = $value;
		    }
	    }
	    
	    return;
    }
    
    
    /**
     * Dynamic create setter/getter methods
     *
     * @access	public
     * @param	string 	method name to call
     * @param	array 	arguments in the form of an array
     * @return	mixed
     */
	    
	public function __call($method, $args)
	{
		foreach(array('/^get_/' => 'get_' , '/^set_/' => 'set_') as $regex => $replace)
		{
	    	if(preg_match($regex, $method))
	    	{
	    		$property = str_replace($replace, '', $method);
	    		$method = rtrim($replace, '_');
		    }
	    }
	    
	    $args = array_merge(array($property), $args);	    	
	    	
	    return call_user_func_array(array($this, $method), $args);
	}
	        
        
    /**
     * CURL Helper
     *
     * @access	protected
     * @param	string	The API endpoint in which to send request
     * @param	string	The data to pass in the request
     * @return	object;
     */
     
    protected function curl($endpoint, $data)
    {
        $ch = curl_init($this->url($endpoint));
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
       
       return curl_exec($ch);
    }
     
	
	/**
	 * Get the value of a defined property
	 *
	 * @access	public
	 * @param	string 	propery name
	 * @return	mixed
	 */
       
    public function get($prop)
    {
	    if(isset($this->$prop))
	    {
		    return $this->$prop;
	    }
	    
	    return NULL;
    }
    
    
	/**
	 * Set the value of a defined property
	 *
	 * @access	public
	 * @param	string 	propery name
	 * @param	string 	propery value
	 * @return	mixed
	 */
       
    public function set($prop, $value)
    {
	    if(isset($this->$prop))
	    {
		    $this->$prop = $value;
	    }
    } 
      
     
	/**
	 * Helper function to build API URL's from endpoints.
	 *
	 * @access	protected
	 * @param	string	The API endpoint  
	 * @return	string
	 */
	
	protected function url($endpoint)
	{
		return rtrim($this->base_url, '/') . '/' . ltrim($endpoint, '/');
	}     
}