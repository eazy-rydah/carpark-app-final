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
        $sql = 'INSERT INTO credit_item (share_id) VALUES ';

        $values = [];

        foreach ($shares as $share) {
            $values[] = "({$share->share_id}),";
        }

        $sql .= implode(" ", $values);

        // Removelast character from statement if it is ","
        $sql = rtrim($sql, ",");

        // Add closing bracket to complete sql statement
        //$sql .= "";

        //var_dump($sql); exit;

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
}


