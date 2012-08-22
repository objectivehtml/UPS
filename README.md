# UPS Live Rates

## Basic Usage

### Step 1. Initialize the Library

	$this->EE->load->library('Ups', array(
		'access_key'     => 'your-access-key',
		'username'       => 'your-username',
		'password'       => 'your-password',
		'account_number' => 'your-account-number',
		'origin' 	 	 => 'your-zipcode'
	));
	
### Step 2. Set your destination and package data.

	// Height, width, depth are optional. Weight is required.
	
	$packages = array(
		array(
			'height' => 12,
			'width'  => 12,
			'depth'  => 1,
			'weight' => 2
		),
		array(
			'height' => 6,
			'width'  => 6,
			'depth'  => 2,
			'weight' => 1
		)
	);
	
	$destination = 33010; // Postal code for Miami, FL
	
	/*
		OR use the long syntax for non-US locations
		
		$destination = array(
			'state'        => 'FL',
			'postal_code'  => 33010,
			'country_code' => 'US""
		);
	*/

### Step 3. Get the live rates

	$rate = $this->EE->ups->get_rate($destination, $packages);