<?php

namespace App\Models;
use PDO;


/**
 * Credit itm model
 *
 * PHP version 7.0
 */
class CreditItem extends \Core\Model
{
    /**
     * Create credit items from given shares
     * 
     * @param Share $shares The share objects to create credit item from
     * 
     * @return void
     */ 
    public static function createFromShares($shares) 
    {

        // seperate all shares, where not credit item is related to.
        // create credit item for each share, where share.share.id != credit_item.share_id

        $sql = 'INSERT INTO credit_item (share_id) VALUES ';

        $values = [];

        foreach ($shares as $share) {
            $values[] = "({$share->share_id}),";
        }

        $sql .= implode(" ", $values);
        $sql = rtrim($sql, ",");
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        return $stmt->execute(); 
    }

    
    /**
    * Get all credit Items
    * 
    * @return mixed Share object collection if found, false otherwise
    */
    public static function getAll() {

        $sql = 'SELECT * FROM credit_item';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetchAll();  
    }

    /**
    * Get all credit Items Which are not exported yet
    * 
    * @return mixed CreditItemExport object collection if found, false otherwise
    */
    public static function getAllForExport() {

        $sql = 'SELECT 
                        contract_id,
                        credit_item, 
                        credit_item.credit_item_id,
                        credit_item.created_at
                FROM share    
                JOIN credit_item 
                ON credit_item.share_id = share.share_id 
                WHERE credit_item.csv_report_id IS NULL;';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        return $stmt->fetchAll();  
    }

    /**
     * Update credit item csv_report_id field with given id
     *
     * @param integer $id The csv_report_id to update
     * 
     * @return void
     */
    public function updateWithCSVReportID($id) 
    {
       
        $sql = 'UPDATE credit_item 
                SET csv_report_id =' . $id . 
                ' WHERE credit_item_id = ' . $this->credit_item_id ;

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        return $stmt->execute(); 

    }

   
}


