<?php

namespace App\Models;

use PDO;
use DateTime;
use DateInterval;
use DatePeriod;
use \App\Auth;
use \App\Config;

/**
 * Share model
 * 
 * PHP version 7.0
  */
class Share extends \Core\Model
{
    /**
    * Errors messages
    * 
    * @var array
     */
    public $errors = [];

    /**
     * Earliest start date from today
     * 
     * @var string
     */
    const MIN_START_DATE = '+2 days';

    /**
     * Earliest end date related to start date
     * 
     * @var string
     */
    const MIN_END_DATE = '+7 days';

    /**
     * Earliest cancel date from today
     * 
     * @var string
     */
    const MIN_CANCEL_DATE = '+3 days';

    /**
     * Class constructor
     * 
     * @param array $data Initial property values
     * @param integer $contract_id The contract_id
     * 
     * @return void
    */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Calculate credit item the share model with the current property values
     * 
     * @param Contract $contract The contract object which holds the 
     * credit-item-per-day property thats relevant for credit-item-calculation
     *  
     * @return float $creditItem The credit item for selected share period
    */
    public function calculateCreditItem($contract) {

        $this->validate();

        if (empty($this->errors)) {

            $start_date = new DateTime($this->start_date);
            $end_date = new DateTime($this->end_date);

            $interval = $end_date->diff($start_date);
            $amount_days = floatval($interval->format('%a'));

            $this->credit_item = $amount_days * $contract->credit_item_per_day;
            $this->amount_days = $amount_days;

            return true;

        } else {

            return false;
        }
    }

    /**
     * Add the share model with the current property values
     * 
     * @param string $id The parking ID
     * 
     * @return void
    */
    public function save() {

        $this->validate();

        if (! isset($this->agb_check)) {
            $this->errors[] = 'Bitte akzeptieren Sie die AGBs';
        }

        if (empty($this->errors)) {

            $this->formatDatesIntoISO8601String();
    
            $shares = Share::getAllByContractID($this->contract_id);
            $this->removeSurroundedShares($shares);
           
            $sql = 'INSERT INTO share ( start_date, 
                                        end_date,
                                        contract_id) 
                                VALUES (:start_date,
                                        :end_date,
                                        :contract_id)';
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':start_date', 
                            substr($this->start_date, 0, strpos($this->start_date, 'T')),
                            PDO::PARAM_STR);
            $stmt->bindValue(':end_date',
                            substr($this->end_date, 0, strpos($this->end_date, 'T')), 
                            PDO::PARAM_STR);
            $stmt->bindValue(':contract_id', $this->contract_id, PDO::PARAM_INT);
    
            return $stmt->execute();

        } else {

            return false;
        }
    }

    /**
    * Validate current property values, adding validation error messages to the  
    * erros array property. 
    * 
    * @return void
    */
    protected function validate()
    {

        $this->validateStartDate();

        $this->validateEndDate();

    }

    /**
     * Validate the start date
     * 
     * @return void
    */
    protected function validateStartDate() {

        if ($this->start_date != '') {

            if(! strtotime($this->start_date)) {

                $this->errors[] = 'Startdatum ungültig Eingabe';

            } else {

                if (! $this->dateIsValidCalenderDate($this->start_date)) {
                    $this->errors[] = 'Startdatum ungültig';
                } 

                if (! $this->earliestStartDateIsValid($this->start_date)) {
                    $this->errors[] = 'Das früheste Startdatum ist Übermorgen';
                }

                if ( $this->dateExists($this->start_date)) {
                    $this->errors[] = 'Startdatum existiert bereits';
                }
            }

        } else {

            $this->errors[] = 'Bitte Startdatum auswählen';
        }
    }

    /**
     * Validate the end date
     * 
     * @return void
    */
    protected function validateEndDate() {

        if ($this->end_date != '') {
        
            if(! strtotime($this->end_date)) {

                $this->errors[] = 'Enddatum ungültige Eingabe';

            } else {

                if (! $this->dateIsValidCalenderDate($this->end_date)) {
                    $this->errors[] = 'Enddatum ungültig';
                } 
            }            

        }  else {

            $this->errors[] = 'Bitte Enddatum auswählen';   
        }

        if(empty($this->errors))  {
    
            if (! $this->earliestEndDateIsValid($this->start_date, $this->end_date)){
                $this->errors[] = 'Das früheste Enddatum muss 7 Tage vom Startdatum entfernt liegen';
            }

            if ( $this->dateExists($this->end_date)) {
                $this->errors[] = 'Enddatum existiert bereits';
            }
        }  
    }

    /**
     * Check if date is a valid calender date
     * 
     * @param string $date The date
     * 
     * @return boolean True is date is valid, false otherwise
    */
    protected function dateIsValidCalenderDate($date) {

        $end_date = new DateTime($date); 

        $date_errors = DateTime::getLastErrors();

        if ($date_errors['warning_count'] > 0) {
            return false;
        }

        return true;
    }

   /**
     * Check if earliest start date is enough days in front of today
     * 
     * @param string $start_date The start date 
     * 
     * @return boolean True is earliest start date is valid, false otherwise
    */
    protected function earliestStartDateIsValid($start_date) {

        $start_date = new DateTime($start_date);
        $min_start_date = new DateTime('now');
        $min_start_date->setTime(0,0,0);
        $min_start_date->modify(Share::MIN_START_DATE);

        if ($start_date < $min_start_date) {
            return false;
        } 

        return true;
    }

    /**
     * Check if earliest end date is enough days in front of start date
     * 
     * @param string $start_date The start date
     * @param string $end_date The end date 
     * 
     * @return boolean True is earliest end date is valid, false otherwise
    */
    protected function earliestEndDateIsValid($start_date, $end_date) {

        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);

        $min_end_date = $start_date->modify(Share::MIN_END_DATE);

        if ($end_date <  $min_end_date) {
            return false;
        } 

        return true;
    }

    /**
     * Check if given share date exists already in database
     * 
     * @param string $share_date The share date to check
     * 
     * @return boolean True if date is found in existing sharePeriodDates, false 
     * otherwise
    */
    protected function dateExists($share_date)
    {
        $shares = Share::getAllByContractID($this->contract_id);
        $sharePeriods = $this->getDatesPeriodsFromDateTimes($shares);
        $sharePeriodDates = $this->getDatesFromDatePeriods($sharePeriods);

        $share_date = new DateTime($share_date);
        $needle = $share_date->format(DateTime::ISO8601);

        $haystack = $sharePeriodDates;

        if(in_array($needle, $haystack)) {

            return true;
        }

        return false;
    }

    /**
    * Find all contract related shares
    * 
    * @param string $id The contract ID
    * 
    * @return mixed Share object collection if found, false otherwise
    */
    public static function getAllByContractID($id) {

        $sql = 'SELECT * FROM share WHERE contract_id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetchAll();  
    }

    /**
    * Create DatePeriodObjects from DateTime Objects 
    * 
    * @param mixed $data Array of DateTime objects
    * 
    * @return mixed $periods An collection of DatePeriod objects
    */
    protected function getDatesPeriodsFromDateTimes($shareObjects) {

        $periods = [];

        foreach ($shareObjects as $share) {
            $periods[] = $this->createDatePeriod($share->start_date, $share->end_date);
        }

        return $periods;
    }

    /**
    * Create an DatePeriod object from given start and end dates 
    * With ISO8601 matching interval declaration P1D (Period1Day)
    * 
    * @param string $start The start date
    * @param string $end The end date
    * 
    * @return DatePeriod object
    */
    protected function createDatePeriod($start, $end) 
    {
        $start = new DateTime($start);
        $end = new DateTime($end);
        $end = $end->modify('+1 day');
        $interval = new DateInterval('P1D');
        $dateperiod = new DatePeriod($start, $interval, $end);

        return $dateperiod;
    }

    /**
    * Create Array with formatted string dates from multiple DatePeriod objects
    * 
    * @param mixed $data Array of DatePeriod objects
    * 
    * @return array $dates An collection Ymd-formatted string dates
    */
    protected function getDatesFromDatePeriods($sharePeriods) 
    {
        $dates = [];

        foreach ($sharePeriods as $sharePeriod) {

            foreach ($sharePeriod as $date)  {
                $dates[] = $date->format(DateTime::ISO8601);
            }
        }

        return $dates;
    }

    /**
     * Format current share dates into ISO8601 date strings
     * 
     * @return void
    */
    protected function formatDatesIntoISO8601String() {

        $this->start_date = new DateTime($this->start_date);
        $this->start_date = $this->start_date->format(DateTime::ISO8601);
        $this->end_date = new DateTime($this->end_date);
        $this->end_date = $this->end_date->format(DateTime::ISO8601);

    }

    /**
    * Remove all existing shares in db, which exist in between current start and
    * end date
    * 
    * @return boolean true if removing successfull, false otherwise
    *
    */
    protected function removeSurroundedShares($shares)
    {
        $shareIDs = $this->getIncludedShareIDs($shares);

       // $shares = $this->getIncludedShares($shares);

       // var_dump($shares); exit;

        if (!empty($shareIDs)) {
            $this->removeByIDs($shareIDs);
        }

    }

   /**
     * Extract all IDs from share object collection, which start_date is included 
     * by daterange of current share object
     * 
     * @param Shares $shares An share object collection
     * 
     * @return mixed $shareIDs An Array with all IDs which are included in daterange of current share object
    */
    private function getIncludedShareIDs($shares)
    {
        $includesdShareIDs = [];

        $existingShares = $shares;

        $currentShareDates = $this->getDatesFromSingleShare($this);
        $haystack = $this->sanitizeISO8601DateString($currentShareDates);

        foreach ($existingShares as $element) {
            
            $needle = $element->start_date;

            if (in_array($needle, $haystack)) {
                $includesdShareIDs[] = $element->share_id;
            }
        }

        return $includesdShareIDs;
    }

    /**
    * Remove shares by IDs
    * 
    * @param string $ids The share IDs
    * 
    * @return boolean true if removing successfull, false otherwise
     */
    protected function removeByIDs($ids) {

        $sql = 'DELETE FROM share WHERE share_id IN (';

        $values = [];

        foreach ($ids as $id) {
            $values[] = "{$id},";
        }

        $sql .= implode(" ", $values);

        // Removelast character from statement if it is ","
        $sql = rtrim($sql, ",");

        // Add closing bracket to complete sql statement
        $sql .= ")";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        return $stmt->execute(); 
    }

    /**
     * Cuts off time and timezone information from ISO8601 formatted date array
     * 
     * @param mixed $dates An array with string dates formatted in ISO8601
     * 
     * @return mixed $shareIDs An Array with all IDs which are included in daterange of current share object
    */
    private function sanitizeISO8601DateString($dates) {

        $sanitizedDates = [];

        foreach ($dates as $date) {
            $sanitizedDates[] = substr($date, 0, strpos($date, "T"));
        }

        return $sanitizedDates;
    }


    /**
    * Get all Dates from single share object
    * 
    * @param Shares $shares share object to get dates from
    * 
    * @return mixed $dates array with ISO8601 formatted dates
    */
    private function getDatesFromSingleShare($share)
    {

        $sharePeriod = $this->createDatePeriod($share->start_date, $share->end_date);

        $dates = [];

        foreach ($sharePeriod as $date) {
            $dates[] = $date->format(DateTime::ISO8601);
        }

        return $dates;
    }

    /**
    * Remove share if not active
    * 
    * @return boolean true if removing successfull, false otherwise
    *
    */
    public function remove() {

        if(Share::earliestCancelDateIsValid($this->start_date)) {

            $sql = 'DELETE FROM share WHERE share_id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);
    
            $stmt->bindParam(':id', $this->share_id, PDO::PARAM_INT);
    
            return $stmt->execute(); 

        } else {

            return false;

        }
    }

    /**
     * Check if cancel date is enough days in front of today
     * 
     * @param string $start_date The start date 
     * 
     * @return boolean True if cancel start date is valid, false otherwise
    */
    protected static function earliestCancelDateIsValid($start_date) {

        $start_date = new DateTime($start_date);
        $min_start_date = new DateTime('now');
        $min_start_date->setTime(0,0,0);
        $min_start_date->modify(Share::MIN_CANCEL_DATE);

        if ($start_date < $min_start_date) {
            return false;
        } 

        return true;
    }

    /**
    * Find share by id
    * 
    * @param string $id The share ID
    * 
    * @return Share Share object if found, false otherwise
    */
    public static function getByID($id) {

        $sql = 'SELECT * FROM share WHERE share_id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();  
    }

    /**
    * Check all given shares for active status
    * 
    * @param mixed $shares The share object collection
    * 
    * @return void
    */
    public static function checkActiveStatus($shares) {

        foreach ($shares as $share) {
            
            if (! Share::earliestCancelDateIsValid($share->start_date)) {

                Share::activate($share->share_id);
            }
        }
    }

     /**
     * Activate share account with the specified id
     * 
     * @param string $id The share id
     * 
     * @return void
     * */
    private static function activate($id)
    {

        $sql = 'UPDATE share
                SET is_active = 1
                WHERE share_id = :id';
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_STR);

        $stmt->execute();
    }
}
    
