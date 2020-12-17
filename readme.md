# Create 'Add to my google calendar' Link to your Homepage

 ![Latest Stable Version](https://img.shields.io/badge/release-v1.0.0-brightgreen.svg)
 ![License](https://img.shields.io/packagist/l/gomoob/php-pushwoosh.svg) 
 [![Donate](https://img.shields.io/static/v1?label=donate&message=PayPal&color=orange)](https://www.paypal.me/SKientzler/5.00EUR)
 ![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)
 [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Stefanius67/GCalendar/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/Stefanius67/GCalendar/?branch=main)
 
----------
## Overview

This class can generate the HREF value to provide a `Add to my google calendar` link to your
page.

Following Data for the event to create is supported:
- Subject
	- `setSubject()`
- Start Date/Time
	- `setStart()`
- End Date/Time or Duration(if not set, default duration of 30 min is used)
	-> `setEnd()` or `setDuration()`
- Timezone (if not set, system settings used)
	- `setTimezone()`
- All day Event
	- `setTimezone()`
- Detailed Description (optional)
	- `setDetails()`
- Location
	- `setLocation()`
- Additional Guest(s)
	- `addGuest()`

## Usage
1. Create an instance of the GCalAddEventLink class
2. Set the required information for the event to be generated
3. Pass the generated HREF to an <a> element on your page

See AddEventExample.php

## Logging
This package can use any PSR-3 compliant logger. The logger is initialized with a NullLogger-object 
by default. The logger of your choice have to be passed to the constructor of the GCalAddEventLink class. 

If you are not working with a PSR-3 compatible logger so far, this is a good opportunity 
to deal with this recommendation and may work with it in the future.  

There are several more or less extensive PSR-3 packages available on the Internet.  

You can also take a look at the 
 [**'XLogger'**](https://www.phpclasses.org/package/11743-PHP-Log-events-to-browser-console-text-and-XML-files.html)
package and the associated blog
 [**'PSR-3 logging in a PHP application'**](https://www.phpclasses.org/blog/package/11743/post/1-PSR3-logging-in-a-PHP-application.html)
as an introduction to this topic.


## History
##### 2020-12-15	Version 1.00
  * initial Version
