<?php

namespace Server\Core;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

/**
 * Constant class
 */
class EnumConst
{
    const VERSION = '2.1';
    /*
    * ERRORS
    */
    const ERROR_SQL_ARRAY = 'Error SQL params';

    /*
    * DATA BASE CONNECT PARAMS
    */
    const DB_HOST = 'localhost';
    const DB_NAME = 'appi_usa';
    const DB_USER = 'appi_usa';
    const DB_PASS = 'usa';

    /*
    * DATA BASE COLUMN NAME
    */
    const ID = 'id';
}