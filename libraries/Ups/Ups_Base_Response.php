<?php

/**
 * UPS Base Response
 * 
 * @package		UPS API
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/
 * @version		0.2.0
 * @build		20120823
 */

abstract class Base_UPS_Response {
	
	/**
	 * UPS Success Constant
	 * 
	 * @var string
	 */
	 
	const UPS_SUCCESS = TRUE;
	
	
	/**
	 * UPS Failed Constant
	 * 
	 * @var string
	 */
	 
	const UPS_FAILED  = FALSE;
	
	
	/**
	 * Errors
	 * 
	 * @var array
	 */
	 
	public $errors = array();

	
	/**
	 * Warning
	 * 
	 * @var array
	 */
	 
	public $warnings = array();
	
	
	/**
	 * Timestamp
	 * 
	 * @var array
	 */
	 
	public $timestamp;
	
	
	/**
	 * Response Code
	 * 
	 * @var string
	 */
	 
	public $code;
	
	
	/**
	 * Response Status
	 * 
	 * @var string
	 */
	 
	public $status;
	
	
	/**
	 * Construct
	 *
	 * @access	public
	 * @return	obj
	 */
	 
	public function __construct($data = array())
	{
		$this->timestamp = time();
		
		foreach($data as $key => $value)
	    {
		    if(property_exists($this, $key))
		    {
			    $this->$key = $value;
		    }
	    }
	}
	
	
	/**
	 * Set the response as success
	 *
	 * @access	public
	 * @param	mixed 	Response code
	 * @return	NULL
	 */
	
	public function success($code = NULL)
	{
		$this->set_response(self::UPS_SUCCESS, $code);
	}


	/**
	 * Set the response as failed
	 *
	 * @access	public
	 * @param	mixed 	Response code
	 * @return	NULL
	 */
	
	public function failed($code = NULL)
	{
		$this->set_response(self::UPS_FAILED, $code);
	}
	
	
	/**
	 * Set the response
	 *
	 * @access	public
	 * @param	mixed 	Response code
	 * @return	NULL
	 */
	 
	public function set_response($response, $code = NULL)
	{	
		if(!is_null($code))
		{
			$this->code = $code;	
		}
		
		$this->status = $response;
	}	
}