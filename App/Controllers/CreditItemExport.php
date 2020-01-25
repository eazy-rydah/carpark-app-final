<?php

namespace App\Controllers;

use \Core\View;
use \App\AuthMethod;
use \App\FlashMessage;
use \App\Models\Share;
use \App\Models\CreditItem;
use SplTempFileObject;

/**
 * Contract controller
 * 
 * PHP version 7.0
 */ 
class CreditItemExport extends EmployeeCustomerServiceAuth
{
    /**
     * Show the contract request page
     * 
     * @return void
     */
    public function showAction()
    {
        // get all shares
        $shares = Share::getAll();
        // activate all active share
        Share::checkActiveStatus($shares);
        // scan all shares for active status
        $activeShares = Share::getActiveShares();


        // create credit items from active shares
        // CreditItem::createFromShares($activeShares);

        // get all credit items

        $credititems = CreditItem::getAll();

        View::renderTemplate('credititemexport/all.html', [

            'credit_items' => $credititems,
            'shares' => $activeShares
        ]);
    }

    public function downloadAction() {
   
        $array = [
            ['Row1', 'Row2', 'Row3'],
            ['Col1', 'Col2', 'Col3'],
            ['Col1', 'Col2', 'Col3'],

        ];

        $file = new SplTempFileObject();

        foreach ($array as $row) {
            $file->fputcsv($row);
        }

        $file->rewind();
     
        header("Content-Type: text/csv");
        header('Content-Disposition: attachment; filename="temp.csv"');
 
    $file->fpassthru();

 /*    $handle = fopen('php://output', 'w');
    ob_clean(); // clean slate

    // [given some database query object $result]...

    while ($row = db_fetch_array($result)) {
        // parse the data...
        
        fputcsv($handle, $row);   // direct to buffered output
    }

    ob_flush(); // dump buffer
    fclose($handle);
    die();		
    // client should see download prompt and page remains where it was */
    }


}