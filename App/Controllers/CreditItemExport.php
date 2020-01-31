<?php

namespace App\Controllers;

use \Core\View;
use \App\AuthMethod;
use \App\FlashMessage;
use \App\Models\Share;
use \App\Models\CreditItem;
use \App\Models\CSVReport;
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
   
        // Get all credit items
     
        // put em into array or maybe export them directly
        // if export was sucessfull -> put csv report IDs into credit items to mark them as exported

        $creditItems = CreditItem::getAll();
        $creditItemArray = get_object_vars($creditItems[0]);
        $creditItemProperties = array_keys($creditItemArray);

        $activeShares = Share::getActiveShares();
        $activeSharesArray = get_object_vars($activeShares[0]);
        $activeSharesProperties = array_keys($activeSharesArray);

        // CSV headline from object properties
        $array = [

            [$activeSharesProperties[6], $activeSharesProperties[4], $creditItemProperties[2]]

        ];

   
        $exportData = CreditItem::getAllForExport();

        if ($exportData) {
            
            // get exportDatacount 
            // 
           
            // create new csvreport
            $csvReport = new CSVReport($exportData);

            // save new csv report in database
            $csvReport->save();

            // get new csv report id
            $id = $csvReport->csv_report_id;

            //update all exported credit items with csv report id
           
            // TOOODOOOO
    
        

   
        }

       // var_dump($csvReport->csv_report_id);
      
        // insert the id into exportedCreditItems

        // convert exportdata intro array
        foreach ($exportData as $data)  {

            $data = (array) $data;
            $array[] = $data;
        }


        // Create downloadable csv-file
        $file = new SplTempFileObject();

        foreach ($array as $row) {
            $file->fputcsv($row);
        }

        $file->rewind();
     
        header("Content-Type: text/csv");
        header('Content-Disposition: attachment; filename="temp.csv"');
 
        $file->fpassthru();
    }
}