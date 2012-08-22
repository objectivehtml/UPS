# UPS (United Parcel Service) PHP API

#### Version 0.1.0 (2012-08-22)

---

### Table Of Contents

1. [About the Project](#about-the-project)
2. [Features](#features)
3. [Setters & Getters](#setters--getters)
4. [Live Shipping Rates](#live-shipping-rates)
5. [Credits](#credits)
6. [License](#license)

---

### About the Project

So after searching around the Internet, it's apparent that there are no modern OOP UPS (United Parcel Service [www.ups.com](http://www.ups.com)) libraries. This library aims to provide a solid foundation for other developers to fork, contribute, and use for their own projects when interacting with the UPS API.

*While the live rates API is stable and is encouraged to be used, just know that this project is very early in development. While the goal is to support the entire API, it will take some time, especially since there are no quality examples to use. Please contribute with pull requests to speed things up!*

---

### Features

1. Consistent and extendible API
2. Live shipping rates between multiple countries
3. Live shipping rates for multiple packages (with unique weights and dimensions)
4. Framework Agnostic. Great for CodeIgniter and Laravel

---

### Setters & Getters

The setters and getters are dynamic, and included by default in the library using PHP magic method. Meaning, you can set or get any object property using a memorable and consistent syntax.

#### Setters

*Setters are used to override default property values.*

	$Ups = new Ups(array(
	    'access_key'     => 'your-access-key',
	    'username'       => 'your-username',
	    'password'       => 'your-password',
	    'account_number' => 'your-account-number',
	    'origin'         => 'your-zipcode'
	));

	$Ups->set_country_code('US');
	$Ups->set_shipping_type('03'); // Use the numberic code for UPS
	$Ups->set_shipping_type('UPS Ground'); // OR use the service name


#### Getters

*Getters are used to retrieve property values.*

	$Ups = new Ups(array(
	    'access_key'     => 'your-access-key',
	    'username'       => 'your-username',
	    'password'       => 'your-password',
	    'account_number' => 'your-account-number',
	    'origin'         => 'your-zipcode'
	));
	
	$country_code  = $Ups->get_country_code();
	$shipping_type = $Ups->get_shipping_type();

----

# Live Shipping Rates

	get_rates(mixed $destination, array $packages);

**$destination** : An array containing address components or a 5 digit zipcode string.

**$packages** : An array of arrays that contain indexes and values that give the packages weight and dimension.

### How to Use

#### Step 1. Initialize the Library

	// Define your origin
	$origin = 'your-zipcode';
	
	// OR use the long syntax for non-US locations
	$origin = array(
		'state'        => 'IN',
		'postal_code'  => '46060',
		'country_code' => 'US'
	);
	
	$auth_params = array(
		'access_key'     => 'your-access-key',
		'username'       => 'your-username',
		'password'       => 'your-password',
		'account_number' => 'your-account-number',
		'origin' 	 	 => $origin
	);
	
	// PHP
	$Ups = new Ups($auth_params);
	
	// CodeIgniter
	$this->CI->load->library('Ups', $auth_params);
	
	// ExpressionEngine
	$this->EE->load->library('Ups', $auth_params);
	
#### Step 2. Set your destination and package data.

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
	
	// Define a destination with a 5 digit zip (US Only)	
	$destination = '33010'; // Postal code for Miami, FL
	
	// OR use the long syntax for non-US locations
	$destination = array(
		'state'        => 'FL',
		'postal_code'  => '33010',
		'country_code' => 'US'
	);


#### Step 3. Get the live rates

	// PHP
	$rate = $Ups->get_rate($destination, $packages);
	
	// CodeIgniter
	$rate = $this->CI->ups->get_rate($destination, $packages);
	
	// ExpressionEngine	
	$rate = $this->EE->ups->get_rate($destination, $packages);

---

### Credits

The following URL contains the only library I could find that was of any use was for interacting with UPS. I started from this code, and removed all the hardcoded variables, added a proper setters/getters, and made the packaging system much more robust.[CodeIgniter UPS Rate Tool](https://github.com/EllisLab/CodeIgniter/wiki/UPS-Rate-Tool)

---

### License

GNU GENERAL PUBLIC LICENSE - Refer to license.txt for details.