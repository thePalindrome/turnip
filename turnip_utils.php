<?php
# Copyright © 2010-2011, Ian McEwen and Eliza Gebow
# All rights reserved.
#
# See the file LICENSE for further conditions

// TURNIPDIR is where we keep ALLLLLL the files
define('TURNIPDIR', dirname(__FILE__));

include(TURNIPDIR . '/turnip_config.php');

/***********************************
 * Return a config value from the  *
 * category and property it's      *
 * stored under.                   *
 *                                 *
 * Optional third argument is a    *
 * default to return if a property *
 * is not found.                   *
 ***********************************
 */
function common_config($main,$sub,$default = false)
{
    global $config;
    return (array_key_exists($main,$config) && 
            array_key_exists($sub,$config[$main])) ? $config[$main][$sub] : $default;
}

/*******************************
 * Returns an SQL time string  *
 * from a PHP time object      *
 *******************************
 */
function common_sql_date($datetime)
{
    return strftime('%Y-%m-%d',$datetime);
}

/*******************************
 * Returns a PHP time object   *
 * from the format used in SQL *
 *******************************
 */
function common_php_date($timestr)
{
    return strtotime($timestr);
}

/******************************
 * Returns the ID number of   *
 * the current comic.         *
 * (that is, the one with the *
 * highest ID whose release   *
 * date has passed)           *
 ******************************
 */
function common_currentid()
{
    $link = mysql_connect(common_config('database','host'),
        common_config('database','user'),
        common_config('database','password'));

    if (!$link)
    {
        header('HTTP/1.0 500 Internal Server Error');
        exit;
    }

    if (!mysql_select_db(common_config('database','name')))
    {
        header('HTTP/1.0 500 Internal Server Error');
        exit;
    }
    $query = "SELECT id FROM comic WHERE date <= '" . common_sql_date(time()) . "' ORDER BY id DESC LIMIT 1;";
    $result = mysql_query($query);

    if(!$result)
    {
        header('HTTP/1.0 500 Internal Server Error');
        exit;
    }

    if (mysql_num_rows($result) == 0)
    {
        header('HTTP/1.0 500 Internal Server Error');
        exit;
    }

    $line = mysql_fetch_assoc($result);
    mysql_free_result($result);
    return $line['id'];
}

?>
