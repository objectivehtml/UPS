<?php

require_once 'Ups_Base.php';
require_once 'Ups_Live_Rates.php';

class Ups extends Base_Ups {
	
	private $available_apis = array(
		'rates' => 'Ups_Live_Rates.php'
	);
	
	public function __construct($data, $load_apis = FALSE)
	{
		parent::__construct($data);
		
		$this->load();
	}
	
	public function load($apis = FALSE)
	{
		if(!$apis)
		{
			$apis = $this->available_apis;
		}
		
		foreach($apis as $obj => $file)
		{
			require_once($file);
			
			$class = str_replace('.php', '', $file);
			
			$params = array();
			
			foreach(get_object_vars($this) as $index => $value)
			{
				if(!in_array($index, array('available_apis')))
				{
					$params[$index] = $value;
				}
			}
			
			$this->$obj = new $class($params);
		}
	}
}