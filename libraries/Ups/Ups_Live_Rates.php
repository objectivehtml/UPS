<?php

/**
 * UPS Live Rates
 * 
 * @package		UPS API
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Objective HTML
 * @link 		http://www.objectivehtml.com/
 * @version		0.2.0
 * @build		20120823
 */

class Ups_Live_Rates extends Base_Ups
{

    /**
	 * The live rates API endpoint.
	 * 
	 * @var string
	 */
	 	
	private $endpoint = "/Rate";
	
	
    /**
	 * Packaging Codes
	 *
	 * @var array
     */
    	
    protected static $shipping_types = array(
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
   
   
    /**
	 * Service Codes
	 *
	 * @var array
     */
     
    protected static $service_codes = array(
		'01'    => 'UPS Express',
		'02'    => 'UPS Expedited',
		'03'    => 'UPS Ground',
		'07'    => 'UPS Express',
		'08'    => 'UPS Expedited',
		'11'    => 'UPS Standard',
		'12'    => 'UPS Three-Day Select',
		'13'    => 'UPS Saver',
		'14'    => 'UPS Express Early A.M.',
		'54'    => 'UPS Worldwide Express Plus',
		'59'    => 'UPS Second Day Air A.M.',
		'65'    => 'UPS Saver',
		'82'    => 'UPS Today Standard',
		'83'    => 'UPS Today Dedicated Courrier',
		'84'    => 'UPS Today Intercity',
		'85'    => 'UPS Today Express',
		'86'    => 'UPS Today Express Saver',
		'308'   => 'UPS Freight LTL',
		'309'   => 'UPS Freight LTL Guaranteed',
		'310'   => 'UPS Freight LTL Urgent',
		'TDCB'  => 'Trade Direct Cross Border',
		'TDA'   => 'Trade Direct Air',
		'TDO'   => 'Trade Direct Ocean',
    );
    
    
    /**
     * Codes for unites of measurement
     *
	 * @var array
     */
     
    public static $weight_uom = array(
    	'LBS' => 'Pounds',
    	'KGS' => 'Kilograms',
    );
    
    
    /**
     * Pickup type codes
     *
	 * @var array
     */
     
    public static $pickup_codes = array(
    	'01' => 'Daily Pickup',
    	'03' => 'Customer Counter',
    	'06' => 'One Time Pickup',
    	'07' => 'On Call Air',
    	'11' => 'Suggested Retail Rates',
    	'19' => 'Letter Center',
    	'20' => 'Air Service Center',
    );
    
    
    /**
     * Pickup day codes
     *
	 * @var array
     */
     
    public static $pickup_day_codes = array(
        '01' => 'Same Day',
        '02' => 'Future Day'
    );
     
        
    /**
	 * Origin address or 5 digit postal code (US only)
	 *
	 * Note, City or Postal Code is required, along with the Country.
	 *
	 * $origin = array(
	 *		'company'      => '', 
	 *		'attn'         => '',
	 *		'address'      => '',
	 *			'street'       => '',
	 *			'street_2'     => '',
	 *			'street_3'     => '',
	 *			'city'         => '',
	 *			'state'        => '',
	 *			'postal_code'  => '',
	 *			'country_code' => ''
	 *		),
	 *		'phone'        => '',
	 *		'fax'          => '',
	 * );
	 *
	 * Or, use the alt syntax and pass a 5 digit postal code (US Only)
	 *
	 * $origin = '12345';
	 *
	 * @var mixed
	 */
    
    protected $origin;
    
    
    /**
	 * Destination address or 5 digit postal code (US only)
	 * 
	 * @var mixed
	 */
    
    protected $destination;
   
   
    /**
	 * Shipper address or 5 digit postal code (US only)
	 * 
	 * @var mixed
	 */
    
    protected $shipper = FALSE;
   
   
    /**
	 * The default country code.
	 * 
	 * @var string
	 */
	 
    protected $country_code = 'US'; 
    	
   
    /**
	 * The default shipping type code.
	 * 
	 * @var string
	 */
	 
    protected $shipping_type = '03';
    	
    	
    /**
	 * The default package type code.
	 * 
	 * @var string
	 */
	 
    protected $package_type = '02';
    	
    	
    /**
	 * The default pickup type code.
	 * 
	 * @var string
	 */
	 
    protected $pickup_type = '01';    	
    	
    /**
	 * Is the destination a residential location?
	 * 
	 * @var string
	 */
	 
    protected $residential = TRUE;
    	
    	
    /**
	 * The default service type code.
	 * 
	 * @var string
	 */
	 
    protected $service_type = 'Rate';
    	
    	
    /**
	 * The default service type code.
	 * 
	 * @var string
	 */
	 
    protected $ship_date = FALSE;
    	
    	
    /**
	 * The default format (used for the dates in the returned response).
	 * 
	 * @var string
	 */
	 
    protected $date_format = 'm/d/Y';
    	
    	
    public function __construct($data = array())
    {
    	parent::__construct($data);
    }
        
    protected function build_location($argLocation)
    {
    	$components = array(
    		'company',
    		'attn',
    		'phone',
    		'fax',
    		'address' => array(
    			'street',
    			'street_2',
    			'street_3',
    			'city',
    			'state',
    			'postal_code',
    			'country_code'
    		)
    	);


        //If just zip code string is passed
        if(is_string($argLocation))
        {
            if(!preg_match('/^\d{5}$/', $argLocation)){
                throw new Exception("Bad format for zip code in ".__FUNCTION__."() function in class ".__CLASS__." on line ".__LINE__);
            }

            return array('address' => array(
                'state' => $this->get_state($argLocation),
                'postal_code' => $argLocation,
                'country_code' => $this->country_code
            ));

        }


        $location = array();

        //If address passed in array format
        foreach($components as $index=>$addrComponentName){

            //Work with strings - company, attn, phone, fax
            if(is_string($components[$index])){
                if(isset($argLocation[$addrComponentName]) && !empty($argLocation[$addrComponentName])){
                    $location[$addrComponentName] = $argLocation[$addrComponentName];
                }
            //Work with address inner components - street, city, state, postal_code, country_code
            } elseif(is_array($components[$index])){

                $outerElementName = $index;
                foreach($components[$outerElementName] as $innerElement){
                    if(isset($argLocation[$innerElement]) && !empty($argLocation[$innerElement])){
                        $location[$outerElementName][$innerElement] = $argLocation[$innerElement];
                    }
                }

            }

        }
        return $location;
    }
    
    protected function key($str)
    {
    	$reserved_words = array(
    		'state'    => 'StateProvinceCode',
    		'street'   => 'AddressLine1',
    		'street_2' => 'AddressLine2',
    		'street_3' => 'AddressLine3',
    		'attn'	   => 'AttentionName',
    		'company'  => 'CompanyName',
    		'name'	   => 'CompanyName',
    		'phone'	   => 'PhoneNumber',
    		'fax'	   => 'FaxNumber',
    		'zip'	   => 'PostalCode',
    		'zipcode'  => 'PostalCode',
    		'postal'   => 'PostalCode'
    	);
    	
    	if(isset($reserved_words[$str]))
    	{
	    	$str = $reserved_words[$str];
    	}
    	
	    return str_replace(array('<', '>', '/>', ' '), '', ucwords(str_replace('_', ' ', $str)));
    }
    
    protected function tag($index, $value)
    {
    	$return = '<'.$this->key($index).'>'.$value.'</'.$this->key($index).'>';
    	
    	if(empty($value))
    	{
    		$return = '<'.$this->key($index).'/>';
    	}
    	
	    return $return;
    }
    
    protected function array_to_xml($array, $xml = '')
    {
	    foreach($array as $index => $value)
	    {
		    if(is_array($value))
		    {
			    $xml .= $this->tag($index, $this->array_to_xml($value));
		    }
		    else
		    {
			    $xml .= $this->tag($index, $value);
		    }
	    }
	    
	    return $xml;
    }
    
    protected function url()
    {
	   return parent::url($this->endpoint);
    }
    
    protected function curl($data)
    {
	    return parent::curl($this->url(), $data);
    }
    
    public function set_origin($var)
    {
	    $this->origin = $this->build_location($var);
    }
    
    public function set_destination($var)
    {
	    $this->destination = $this->build_location($var);
    }
    
    public function set_shipper($var)
    {
    	$var = $var ? $var : $this->origin;
    	
	    $this->shipper = $this->build_location($var);
	    $this->shipper = isset($this->shipper['address']) ? $this->shipper['address'] : $this->shipper;
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
    
    public function get_rate($packages = array())
    {
    	$this->set_shipper($this->shipper);
    	
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
                    <Address>" 
                    	. $this->array_to_xml($this->shipper) . "
                    </Address>
                    <ShipperNumber>{$this->account_number}</ShipperNumber>
                </Shipper>
                <ShipTo>
                	".$this->array_to_xml($this->destination)."
                </ShipTo>
                <ShipFrom>
                	".$this->array_to_xml($this->origin)."
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

        $result = $this->curl($data);
        
        $xml = new SimpleXMLElement(strstr($result, '<?'));

        $response = new Ups_Live_Rates_Response(array(
        	'origin'	  => $this->origin,
        	'destination' => $this->destination,
        	'shipper' 	  => $this->shipper,
        	'shipping_type' => $this->shipping_type,
        	'package_type'  => $this->package_type,
        	'pickup_type'   => $this->pickup_type,
        	'residential'   => $this->residential,
        	'service_type'  => $this->service_type,
        	'ship_date'		=> strtotime($this->ship_date),
        	'formatted_ship_date' => date($this->date_format, strtotime($this->ship_date))
        ));
        
        /*
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
        */
        
        if ($xml->Response->ResponseStatusCode == '1')
        {
        	$response->success();
        
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
            
            $response->warnings = $warnings;
            $response->rate     = (double) $rate;
        }
        else
        {
        	$error = $xml->Response->Error;
        	
        	$response->failed($error->ErrorCode);
                    
	        $response->errors = array((object) array(
	        	'severity' 		=> (string) $error->ErrorSeverity,
	        	'code' 			=> (string) $error->ErrorCode,
	        	'description' 	=> (string) $error->ErrorDescription
	        ));
        }
        
        return $response;
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

class Ups_Live_Rates_Response extends Base_UPS_Response {

	public function __construct($data = array())
	{
		parent::__construct($data);
	}
	
	/**
	 * Origin
	 * 
	 * @var array
	 */
	
	public $origin;
	
	
	/**
	 * Destination
	 * 
	 * @var array
	 */
	
	public $destination; 
	
	
	/**
	 * Shipper
	 * 
	 * @var array
	 */
	
	public $shipper; 
	
	
	/**
	 * Shipping Type Code
	 * 
	 * @var array
	 */
	
	public $shipping_type; 
	
	
	/**
	 * Package Type Code
	 * 
	 * @var array
	 */
	
	public $package_type; 
	
	
	/**
	 * Pickup Type Code
	 * 
	 * @var array
	 */
	
	public $pickup_type; 

	
	/**
	 * Service Type Code
	 * 
	 * @var array
	 */
	
	public $service_type; 
	
	
	/**
	 * Residential
	 * 
	 * @var array
	 */
	
	public $residential; 
	
	
	/**
	 * Ship Date
	 * 
	 * @var array
	 */
	
	public $ship_date;
	
	
	/**
	 * Formatted Ship date
	 * 
	 * @var array
	 */
	
	public $formatted_ship_date; 
}