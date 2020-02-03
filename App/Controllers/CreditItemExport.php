<?php

namespace App\Controllers;

use \Core\View;
use \App\AuthMethod;
use \App\FlashMessage;
use \App\Models\Share;
use \App\Models\Contract;
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
        $shares = Share::getAll();
        Share::checkActiveStatus($shares);
        $activeShares = Share::getActiveShares();

        if ($activeShares) {

            $sharesToCreateCreditItemFrom = Share::getAllWithoutCreditItem($activeShares);
        
            if ($sharesToCreateCreditItemFrom) {
                CreditItem::createFromShares($sharesToCreateCreditItemFrom);
            }
        }

        $credititems = CreditItem::getAll();

        View::renderTemplate('credititemexport/all.html', [
            'credit_items' => $credititems,
            'shares' => $activeShares
        ]);
    }

    /**
     * Start csv export file download
     *
     * @return void
     */
    public function downloadAction() {
   
        $exportData = $this->getExportData();
     
        if ($exportData) {
            
            $csvReport = new CSVReport($exportData);
            $csvReport->save();

            $id = $csvReport->csv_report_id;
            $creditItems = CreditItem::getAllForExport();

            foreach ($creditItems as $creditItem)  {
                
                $creditItem->updateWithCSVReportID($id);

                $relatedContract = Contract::findByID($creditItem->contract_id);
                $relatedContract->addCreditItemSum($creditItem->credit_item);
            }

            $headline = $this->createHeadlineForCSVFile($exportData);
            $this->exportCSV($headline, $exportData);
   
        } else {

            FlashMessage::add('Keine neuen Gutschriften zum Export verfügbar', FlashMessage::INFO);

            $this->redirect('/CreditItemExport/show');
        }
    }

    /**
     * get all data for creditItem export
     *
     * @return mixed $exportData object collection 
     */
    private function getExportData() {

        $creditItems = CreditItem::getAll();
        $activeShares = Share::getActiveShares();

        $exportData = CreditItem::getAllForExport();
        
        return $exportData;
    }

    /**
     * Create headerline for downloadable csv file
     *
     * @param  mixed $exportData object collection
     *
     * @return array $headline The Headline for downloadable csv file
     */
    private function createHeadlineForCSVFile($exportData) {

        $exportDataArray = get_object_vars($exportData[0]);
        $exportDataProperties = array_keys($exportDataArray);

        // CSV headline from object properties
        $headline = [
            [$exportDataProperties[0], $exportDataProperties[1], $exportDataProperties[2]]
        ];

        return $headline;
    }
    
    /**
     * Create downloadable csv export file from exportdata
     *
     * @param  mixed $exportData
     *
     * @return void
     */
    private function exportCSV($headline, $exportData) {

        foreach ($exportData as $data)  {

            $data = (array) $data;
            $headline[] = $data;
        }

        $file = new SplTempFileObject();

        foreach ($headline as $row) {
            $file->fputcsv($row);
        }

        $file->rewind();
        
        header("Content-Type: text/csv");
        header('Content-Disposition: attachment; filename="temp.csv"');
    
        $file->fpassthru();
    }
}