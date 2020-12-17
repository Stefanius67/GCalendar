<?php
require_once 'autoloader.php';

use SKien\GCalendar\GCalAddEventLink;

$oLink = new GCalAddEventLink();

$oLink->setSubject('Zoom meeting at christmas day');
$oLink->setStart('2020-12-24 13:00');
$oLink->setDuration(new \DateInterval('PT1H')); // 1 hour
$oLink->setTimezone('America/New_York');
$oLink->setDetails(
    'We just want to meet us via zoom cause we are not allowed to ' . PHP_EOL . 
    'meet us at the christmas market :-(' . PHP_EOL . PHP_EOL .
    'And this is just a check whether the usual problems caused by' . PHP_EOL .
    'German umlauts are correctly intercepted: ä, ö, ü, Ä, Ö, Ü, ß ...'
);
$oLink->setLocation('Homeoffice');
$oLink->addGuest('guest1@example.com');
$oLink->addGuest('guest2@example.com');

echo '<h1>Example to add event to google calendar</h1>' . PHP_EOL;
echo '<a target="_blank" href="' . $oLink->getHREF() . '">Add to google calendar</a>' . PHP_EOL;
