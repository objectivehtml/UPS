# UPS API

#### Version 0.1.0

#### About this Project

So after searching around the Internet, it's apparent that there are no modern OOP UPS (United Parcel Service [www.ups.com](http://www.ups.com)) libraries. This project aims to provide a solid foundation for other developers to fork, contribute, and use for their own projects.

#### License

GNU GENERAL PUBLIC LICENSE - Refer to license.txt for details.

#### Credits

[CodeIgniter UPS Rate Tool](https://github.com/EllisLab/CodeIgniter/wiki/UPS-Rate-Tool) 

This code was the only library I could find that was of any use was. I started from this code, and rewrote the majority of it. 


## UPS Live Rates

### Step 1. Initialize the Library

	// Define your origin
	$origin = 'your-zipcode';
	
	// OR use the long syntax for non-US locations
	$origin = array(
		'state'        => 'IN',
		'postal_code'  => '46060',
		'country_code' => 'US'
	);
	
	$this->EE->load->library('Ups', array(
		'access_key'     => 'your-access-key',
		'username'       => 'your-username',
		'password'       => 'your-password',
		'account_number' => 'your-account-number',
		'origin' 	 	 => $origin
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
	
	$destination = '33010'; // Postal code for Miami, FL
	
	// OR use the long syntax for non-US locations	
	$destination = array(
		'state'        => 'FL',
		'postal_code'  => '33010',
		'country_code' => 'US'
	);


### Step 3. Get the live rates

	$rate = $this->EE->ups->get_rate($destination, $packages);