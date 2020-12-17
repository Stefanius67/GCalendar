<?php
declare(strict_types=1);

namespace SKien\GCalendar;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class to generate HTML link to allow the user to add event to his google calendar.
 * 
 * #### History
 * - *2020-12-15*   initial version
 * 
 * @package SKien/GCalendar
 * @version 1.0.0
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
*/
class GCalAddEventLink
{
    public const EVENTEDIT_URL = 'https://calendar.google.com/calendar/r/eventedit';
    
    public const AVAILABLE = 'AVAILABLE';
    public const BUSY = 'BUSY';
    public const BLOCKING = 'BLOCKING';
    
    /** @var string subject of the event   */
    protected string $strSubject = '';
    /** @var string detailed text   */
    protected string $strDetails = '';
    /** @var \DateTime start date and time   */
    protected ?\DateTime $dtStart = null;
    /** @var \DateTime end date and time   */
    protected ?\DateTime $dtEnd = null;
    /** @var string location    */
    protected string $strLocation = '';
    /** @var string timezone    */
    protected string $strTimezone = '';
    /** @var bool all day event    */
    protected bool $bAllday = false;
    /** @var array list of guests    */
    protected array $aGuest = [];
    /** @var string transparency (one of self::AVAILABLE, self::BUSY or self::BLOCKING */
    protected string $strTransparency = '';
    /** @var LoggerInterface    PSR-3 logger */
    protected LoggerInterface $logger;
    
    /**
     * Constructor
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        if ($logger === null ) {
            // to ensure logger always containing valid PSR-3 logger instance
            $this->logger = new NullLogger();
        } else {
            $this->logger = $logger;
        }
    }
    
    /**
     * Reset object
     */
    public function reset() : void
    {
        $this->strSubject = '';
        $this->strDetails = '';
        $this->dtStart = null;
        $this->dtEnd = null;
        $this->strLocation = '';
        $this->strTimezone = '';
        $this->bAllday = false;
        $this->aGuest = [];
        $this->strTransparency = '';
    }
    
    /**
     * Returns resulting HREF to add the event to the google calendar
     * @return string
     */
    public function getHREF() : string
    {
        if (!$this->validate()) {
            return '';            
        }
        $strHREF  = self::EVENTEDIT_URL;
        $strHREF .= $this->getEncodedParam('?text', $this->strSubject);
        $strHREF .= $this->getDatesParam();
        $strHREF .= $this->getEncodedParam('&ctz', $this->strTimezone);
        $strHREF .= $this->getEncodedParam('&details', $this->strDetails);
        $strHREF .= $this->getEncodedParam('&location', $this->strLocation);
        $strHREF .= $this->getEncodedParam('&crm', $this->strTransparency);
        $strHREF .= $this->getGuestParam();
        
        return $strHREF;
    }
    
    /**
     * @param string $strSubject
     */
    public function setSubject(string $strSubject) : void
    {
        $this->strSubject = $strSubject;
    }

    /**
     * @param string $strDetails
     */
    public function setDetails(string $strDetails) : void
    {
        $this->strDetails = $strDetails;
    }

    /**
     * @param \DateTime|string|int $start
     * @param string $strFormat
     */
    public function setStart($start, string $strFormat = '') : void
    {
        $this->dtStart = $this->getDateTime($start, $strFormat);
    }

    /**
     * @param \DateTime|string|int $end
     * @param string $strFormat
     */
    public function setEnd($end, string $strFormat = '') : void
    {
        $this->dtEnd = $this->getDateTime($end, $strFormat);
    }
    
    /**
     * Set duration of the event.
     * Start must be specifeid before - otherwise this call is ignored.
     * @param string $strIntervall valid intervall
     * @link https://www.php.net/manual/en/dateinterval.construct
     */
    public function setDuration(string $strIntervall) : void
    {
        if ($this->dtStart !== null) {
            $this->dtEnd = clone $this->dtStart;
            $di = new \DateInterval($strIntervall);
            $this->dtEnd->add($di);
        } else {
            $this->logger->warning('Call of GCalendar::setDuration() without call of GCalendar::setStart before');
        }
    }

    /**
     * @param string $strLocation
     */
    public function setLocation(string $strLocation) : void
    {
        $this->strLocation = $strLocation;
    }
    
    /**
     * @param string $strTimezone
     */
    public function setTimezone(string $strTimezone) : void
    {
        if (!in_array($strTimezone, \DateTimeZone::listIdentifiers())) {
            $this->logger->warning('Call of GCalendar::setTimezone() with invalid timezone', ['timezone' => $strTimezone]);
        } else {
            $this->strTimezone = $strTimezone;
        }
    }

    /**
     * @param boolean $bAllday
     */
    public function setAllday(bool $bAllday = true) : void
    {
        $this->bAllday = $bAllday;
    }

    /**
     * @param string $strGuest
     */
    public function addGuest(string $strGuest) : void
    {
        $this->aGuest[] = $strGuest;
    }

    /**
     * @param string $strTransparency
     */
    public function setTransparency(string $strTransparency) : void
    {
        $aValidTrp = [self::AVAILABLE, self::BUSY, self::BLOCKING];
        if (in_array($strTransparency, $aValidTrp)) {
            $this->strTransparency = $strTransparency;
        } else {
            $this->logger->warning('Invalid Transparency at GCalendar::setTransparency()!', ['strTransparency' => $strTransparency]);
        }
    }
    
    /**
     * Check, if object contains valid data
     * @return bool
     */
    private function validate() : bool
    {
        if ($this->dtStart === null) {
            $this->logger->warning('GCalendar: No start date/time set!');
        } else if ($this->dtEnd === null) {
            $this->dtEnd = clone $this->dtStart;
            if (!$this->bAllday && $this->dtEnd !== null) {
                $this->dtEnd->add(new \DateInterval('PT30M'));
            }
        }
        if ($this->strTimezone == '' && $this->dtStart !== null) {
            $this->strTimezone = $this->dtStart->getTimezone()->getName();
        }
        if (strlen($this->strSubject) === 0) {
            $this->logger->warning('GCalendar: No subject set!');
        }
        return strlen($this->strSubject) > 0 && $this->dtStart !== null && $this->dtEnd !== null;
    }
    
    /**
     * @param \DateTime|string|int $datetime
     * @param string $strFormat
     * @return \DateTime
     */
    private function getDateTime($datetime, string $strFormat = '') : ?\DateTime
    {
        $dt = null;
        if (is_object($datetime) && get_class($datetime) == 'DateTime') {
            // DateTime -object
            $dt = $datetime;
        } else if (is_numeric($datetime)) {
            // unix timestamp
            $dt = new \DateTime();
            $dt->setTimestamp(intval($datetime));
        } else {
            // formated string
            if ($strFormat != '') {
                // parse according given format
                $dt = \DateTime::createFromFormat($strFormat, (string) $datetime);
                if ($dt === false) {
                    $dt = null;
                    $this->logger->warning('Invalid Date parameter GCalendar::setStart()/setEnd()', ['datetime' => $datetime]);
                }
            } else {
                // assuming any English textual datetime
                $timestamp = strtotime((string) $datetime);
                if ($timestamp !== false) {
                    $dt = new \DateTime();
                    $dt->setTimestamp($timestamp);
                } else {
                    $this->logger->warning('Invalid Date parameter GCalendar::setStart()/setEnd()', ['datetime' => $datetime]);
                }
            }
        }
        return $dt;
    }

    /**
     *  
     * @param string $strParam
     * @param string $strValue
     * @return string
     */
    private function getEncodedParam(string $strParam, string $strValue) : string
    {
        if (strlen($strValue) == 0) {
            return '';
        }
        return $strParam . '=' . urlencode($strValue);
    }
    
    /**
     * 
     * @return string
     * @link https://www.php.net/manual/en/datetime.format.php
     */
    private function getDatesParam() : string
    {
        $strDates = '';
        if ($this->dtStart !== null && $this->dtEnd !== null) {
            $strFormat = 'Ymd\THis';
            if ($this->bAllday) {
                $this->dtEnd->add(new \DateInterval('P1D'));
                $strFormat = 'Ymd';
            }
            $strDates = '&dates=' . $this->dtStart->format($strFormat) . '/' . $this->dtEnd->format($strFormat);
        }
        return $strDates;
    }
    
    /**
     * @return string
     */
    private function getGuestParam() : string
    {
        if (count($this->aGuest) == 0) {
            return '';
        }
        $strSep = '';
        $strParam = '&add=';
        foreach ($this->aGuest as $strGuest) {
            $strParam .= $strSep . $strGuest;
            $strSep = ',';
        }
        return $strParam;
    }
}
