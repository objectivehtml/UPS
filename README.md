# UPS (United Parcel Service) PHP API

#### Version 0.1.0 (2015-05-23)


### Install

I recommend to use composer for installing. Here is a sample of composer.json file to include UPS library:

    {
        "name": "StudyProject",
        "repositories": [
            {
                "type": "git",
                "url": "https://github.com/KoulSlou/UPS.git"
            }
        ],
        "require": {
            "KoulSlou/UPS":"dev-master"
        }
    }


### How To Use

First you need to initialize Ups object with your account credentials:


    $auth_params = array(
        'access_key'     => '1234567890',
        'username'       => 'JohnSmith',
        'password'       => '*******',
        'account_number' => '123456'
    );
    
    $Ups = new Ups($auth_params);
    
Then you need to specify origin and destination. You can provide only zipcode string for US address, for other countries
provide array with address information.
    
    
    $destination = array(
        'state'        => 'FL',
        'postal_code'  => '33010',
        'country_code' => 'US',
        'company' => 'test',
        'fax' => '123'
    );
    
    
    $origin = '32548'
    
Now define information about packages your going to send:
    
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
    
To get rates call get_rate() function:
    
    $rates = $Ups->rates->get_rate($packages);
    
You will receive object of Ups_Live_Rates. In rates property you will have array of available shipping methods (array of
Ups_Rate objects)
    
    

### Credits

Here is a link to the repository that I used: https://github.com/objectivehtml/UPS

---

### License

GNU GENERAL PUBLIC LICENSE - Refer to license.txt for details.