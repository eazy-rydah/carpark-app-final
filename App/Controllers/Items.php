<?php

namespace App\Controllers;

use \Core\View;

/**
 * Items controller (example to demonstrate access-restriction)
 * 
 * PHP version 7.0
 */ 
class Items extends Authenticated
{

    /**
     * Items index
     * 
     * @return void
     */ 
    public function indexAction()
    {
        View::renderTemplate('Items/index.html');
    }

    /**
     * Add a new item
     * 
     * @return void
     */
    public function newAction()
    {
        echo "new action";
    }

}
