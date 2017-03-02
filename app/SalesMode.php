<?php

/**
 * Bill short summary.
 *
 * Invoice description.
 *
 * @version 1.0
 * @author kris
 */
namespace App;

class SalesMode {
    const NORMAL = 1;
    const RESTO = 2;

    public static function getMode(){
        return isset($_COOKIE['sales_mode']) ? $_COOKIE['sales_mode'] :"";
    }
}
