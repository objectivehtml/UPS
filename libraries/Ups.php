<?php

/**
 * UPS Rat
 * 
 * @package		
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/
 * @version		
 * @build		
 */

class Ups
{
	/* -----------------------------------------
		UPS Rate API Url
	----------------------------------------- */
	
	private $rate_url = "https://www.ups.com/ups.app/xml/Rate";
	
	/* -----------------------------------------
		Account Details
	----------------------------------------- */
	
    private	$access_key,		// Your UPS Online Tools Access Key
	    	$username, 			// Your UPS Account Username
	    	$password, 			// Your UPS Account Password
	    	$account_number;	// Your UPS Account Number
    
    /* -----------------------------------------
    	Properties
    ----------------------------------------- */
    
    protected $origin		  = FALSE,		// Your shipping origin
    		  $country_code	  = 'US',		// The default country code
    		  $shipping_type  = '03',		// The default shipping code
    		  $package_type	  = '02',		// The default package type code
    		  $pickup_type    = '01',		// The default pickup code
    		  $residential 	  = TRUE,		// Is destination a residential location?
    		  $service_type   = 'Rate',		// Default service type, 'Rate' or 'Shop'
    		  $ship_date	  = FALSE,		// Default shipping date (defaults to now)
    		  $date_format 	  = 'm/d/Y';	// Default shipping date format
    		  
    /* -----------------------------------------
    	Default Shipping Types	
    ----------------------------------------- */
    
    protected $shipping_types = array(
        '01' => 'UPS Next Day Air',
        '02' => 'UPS Second Day Air',
        '03' => 'UPS Ground',
        '07' => 'UPS Worldwide Express',
        '08' => 'UPS Worldwide Expedited',
        '11' => 'UPS Standard',
        '12' => 'UPS Three-Day Select',
        '13' => 'Next Day Air Saver',
        '14' => 'UPS Next Day Air Early AM',
        '54' => 'UPS Worldwide Express Plus',
        '59' => 'UPS Second Day Air AM',
        '65' => 'UPS Saver'
    );
    
    public function __construct($data = array())
    {
	    foreach($data as $key => $value)
	    {
		    if(property_exists($this, $key))
		    {
			    $this->$key = $value;
		    }
	    }
	    
	    $this->origin = $this->build_location($this->origin);
	    
	    $this->set_default_date('ship_date');
    }
	    
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
    
    protected function build_location($var)
    {
	    if(!is_array($var) && !is_object($var) && preg_match('/\d{5}/', $var))
	    {
		    $var = array(
	    		'state'        => $this->get_state($var),
	    		'postal_code'  => $var,
	    		'country_code' => $this->country_code
	    	);
	    }
	    
	    if(!isset($var['country_code']))
	    {
		    $var['country_code'] = $this->country_code;
	    }
	    
	    return $var;
    }
    
    protected function set_default_date($index)
    {
    	$var = $this->$index;
    	
        if(!$var)
        {
	        $var = time();
        }
        
        if (is_numeric($var))
        {
            $var = date('Y-m-d', $var);
        }
        else
        {
            $var = date('Y-m-d', strtotime($var));
        }
        
        $this->set($index, $var);
    }
    
    public function get_rate($destination, $packages = array())
    {
    	$destination = $this->build_location($destination);
    	    	
        $residential_xml = '';
        $package_xml     = '';        
       
        if ($this->residential)
        {
            $residential_xml = "<ResidentialAddressIndicator/>";
        }
        
        foreach($packages as $package)
        {
        	$package = (object) $package;
        	
        	$type    = isset($package->type)   ? $package->type   : $this->package_type;
        	$length  = isset($package->length) ? $package->length : (isset($package->depth) ? $package->depth : 1);
        	$height	 = isset($package->height) ? $package->height : 0;
        	$width	 = isset($package->width)  ? $package->width  : 0;
        	
        	$dimension_uom = strtoupper(isset($package->dimension_uom) ? $package->dimension_uom : 'IN');
        	$weight_uom = strtoupper(isset($package->weight_uom) ? $package->weight_uom : 'LBS');
        	
    		$package_xml .= "
                <Package>
                    <PackagingType>
                    	<Code>$type</Code>
                    </PackagingType>
                    <Dimensions>
                        <UnitOfMeasurement><Code>$dimension_uom</Code></UnitOfMeasurement>
                        <Height>{$height}</Height>  
                        <Width>{$width}</Width>                    
                        <Length>{$length}</Length>
                    </Dimensions>
                    <PackageWeight>
                        <UnitOfMeasurement><Code>$weight_uom</Code></UnitOfMeasurement>
                        <Weight>{$package->weight}</Weight>
                    </PackageWeight>
                </Package>
            ";	        
        }
        
        $data ="
       	<?xml version=\"1.0\" encoding=\"UTF-8\"?>
        <AccessRequest xml:lang=\"en-US\">
            <AccessLicenseNumber>" . $this->access_key . "</AccessLicenseNumber>
            <UserId>" . $this->username . "</UserId>
            <Password>" . $this->password . "</Password>
        </AccessRequest>
        
        <?xml version=\"1.0\" encoding=\"UTF-8\"?>
        <RatingServiceSelectionRequest xml:lang=\"en-US\">
            <Request>
                <TransactionReference>
                    <CustomerContext>Rate Request From " . $_SERVER['HTTP_HOST'] . "</CustomerContext>
                    <XpciVersion>1.0001</XpciVersion>
                </TransactionReference>
                <RequestAction>Rate</RequestAction>
                <RequestOption>{$this->service_type}</RequestOption>
            </Request>
            <PickupType> 
            	<Code>{$this->pickup_type}</Code> 
            </PickupType>
            <Shipment>
                <Shipper>
                    <Address>
                        <PostalCode>{$this->origin['postal_code']}</PostalCode>
                        <CountryCode>{$this->country_code}</CountryCode>
                    </Address>
                    <ShipperNumber>{$this->account_number}</ShipperNumber>
                </Shipper>
                <ShipTo>
                    <Address>
                    	<PostalCode>{$destination['postal_code']}</PostalCode>
                    	<StateProvinceCode>{$destination['state']}</StateProvinceCode>
                    	<CountryCode>{$this->country_code}</CountryCode>
                    	$residential_xml
                    </Address>
                </ShipTo>
                <ShipFrom>
                    <Address>
                    	<PostalCode>{$this->origin['postal_code']}</PostalCode>
                    	<StateProvinceCode>{$this->origin['state']}</StateProvinceCode>
                    	<CountryCode>{$this->origin['country_code']}</CountryCode>
                    </Address>
                </ShipFrom>
                <Service>
                    <Code>{$this->get_shipping_type()}</Code>
                </Service>
                <ShipmentServiceOptions>
                    <OnCallAir>
                        <Schedule>
                            <PickupDay>{$this->ship_date}</PickupDay>
                        </Schedule>
                    </OnCallAir>
                </ShipmentServiceOptions>
                $package_xml
                <RateInformation>
                    <NegotiatedRatesIndicator/>
                </RateInformation>
            </Shipment>
        </RatingServiceSelectionRequest>";
        
        $ch = curl_init($this->rate_url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
       
        $result = curl_exec($ch);
        
        $xml = new SimpleXMLElement(strstr($result, '<?'));

        $return = array(
        	'origin'	  => (object) $this->origin,
        	'destination' => (object) $destination,
        	'success' => TRUE,
        	'error'   => FALSE,
        	'shipping_type' => $this->shipping_type,
        	'package_type'  => $this->package_type,
        	'pickup_type'   => $this->pickup_type,
        	'residential'   => $this->residential,
        	'service_type'  => $this->service_type,
        	'ship_date'		=> strtotime($this->ship_date),
        	'formatted_ship_date' => date($this->date_format, strtotime($this->ship_date))
        );
        
        if ($xml->Response->ResponseStatusCode == '1')
        {
            $data   = array();
            $warnings = array();
            
            foreach($xml->RatedShipment as $index => $service)
            {
                $data[count($data)] = "{$service->TotalCharges->MonetaryValue}";
                
                if(isset($service->RatedShipmentWarning))
                {
                	foreach($service->RatedShipmentWarning as $warning)
                	{
	                	$warnings[] = (string) $warning;
                	}
                }
            }            
            
            asort($data);
            
            foreach($data as $key => $value)
            {
                $date = '';
                   
                $service = $xml->RatedShipment[$key]->children();
                
                if (!empty($service->GuaranteedDaysToDelivery))
                {
                    $date = date($this->date_format, strtotime($this->ship_date) + ($service->GuaranteedDaysToDelivery * 86400));
                }
                
                $rate = number_format((double)($service->TotalCharges->MonetaryValue), 2);
            }
            
            $return['warnings']		  = $warnings;
            $return["service"]  	  = $this->shipping_types[(string)$service->Service->Code];
            $return["rate"]           = (double) $rate;
            $return["delivery_date"]  = strtotime($date);
            $return["formatted_delivery_date"] = $date;
        }
        else
        {
        	$error = $xml->Response->Error;
        	
            $return['success'] = FALSE;
	        $return['error']   = (object) array(
	        	'severity' 		=> (string) $error->ErrorSeverity,
	        	'code' 			=> (string) $error->ErrorCode,
	        	'description' 	=> (string) $error->ErrorDescription
	        );
        }
        
        $return['xml'] = $xml;
        
        return $return;
    }
    
    public function set_shipping_type($type)
    {
    	$type = (string) $type;
    	
    	if(!preg_match("/\d*/u", $type))
    	{
	    	foreach($this->shipping_types as $code => $shipping_type)
	    	{
		    	if($shipping_type == $type)
		    	{
			    	$this->set('shipping_type', $code);
		    	}
	    	}
    	}
    	else
    	{
    		$this->set('shipping_type', isset($this->shipping_types[$type]) ? $type : $this->shipping_type);
    	}    	
    }
       
    public function get($prop)
    {
	    if(isset($this->$prop))
	    {
		    return $this->$prop;
	    }
	    
	    return NULL;
    }
    
    public function set($prop, $value)
    {
	    if(isset($this->$prop))
	    {
		    $this->$prop = $value;
	    }
    }
    
    protected function get_state($zip)
    {
        $all_states = array('AK9950099929', 'AL3500036999', 'AR7160072999', 'AR7550275505', 'AZ8500086599', 'CA9000096199', 'CO8000081699', 'CT0600006999', 'DC2000020099', 'DC2020020599', 'DE1970019999', 'FL3200033999', 'FL3410034999', 'GA3000031999', 'HI9670096798', 'HI9680096899', 'IA5000052999', 'ID8320083899', 'IL6000062999', 'IN4600047999', 'KS6600067999', 'KY4000042799', 'KY4527545275', 'LA7000071499', 'LA7174971749', 'MA0100002799', 'MD2033120331', 'MD2060021999', 'ME0380103801', 'ME0380403804', 'ME0390004999', 'MI4800049999', 'MN5500056799', 'MO6300065899', 'MS3860039799', 'MT5900059999', 'NC2700028999', 'ND5800058899', 'NE6800069399', 'NH0300003803', 'NH0380903899', 'NJ0700008999', 'NM8700088499', 'NV8900089899', 'NY0040000599', 'NY0639006390', 'NY0900014999', 'OH4300045999', 'OK7300073199', 'OK7340074999', 'OR9700097999', 'PA1500019699', 'RI0280002999', 'RI0637906379', 'SC2900029999', 'SD5700057799', 'TN3700038599', 'TN7239572395', 'TX7330073399', 'TX7394973949', 'TX7500079999', 'TX8850188599', 'UT8400084799', 'VA2010520199', 'VA2030120301', 'VA2037020370', 'VA2200024699', 'VT0500005999', 'WA9800099499', 'WI4993649936', 'WI5300054999', 'WV2470026899', 'WY8200083199');
        
        foreach ($all_states as $zip_range)
        {
            
            if (($zip >= substr($zip_range, 2, 5)) && ($zip <= substr($zip_range, 7, 5)))
            {
                return substr($zip_range, 0, 2);
            }
        }
        
        return;
    }
        
}