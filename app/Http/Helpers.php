<?php
//Delete existing file
if ( ! function_exists('deleteFile')) {
    function deleteFile($path, $name)
    {
        if (file_exists($path.'/'.$name)) {
            unlink($path.'/'.$name);
        }
    }
}

//Search string get and set an url
if ( ! function_exists('qString')) {
    function qString($query = null)
    {
        if (isset($_SERVER['QUERY_STRING'])) {
            return '?'.$_SERVER['QUERY_STRING'].$query;
        } else {
            if ($query) {
                return '?'.$query;
            }
        }
    }
}

//Date View
if ( ! function_exists('dateFormat')) {
    function dateFormat($date, $time = null)
    {
        if ($time) {
            return date('d/M/Y h:i A',(strtotime($date)));
        } else {
            return date('d/M/Y',strtotime($date));
        }
    }
}

//Time View
if ( ! function_exists('timeFormat')) {
    function timeFormat($date)
    {
        return date('h:i A',(strtotime($date)));
    }
}

//Two Digit Number Format Function
if ( ! function_exists('numberFormat')) {
    function numberFormat($amount=0, $coma=null)
    {
        if ($coma) {
            if ($amount==0)
                return '-';
            else
                return number_format($amount,2);
        } else {
            return number_format($amount,2,'.','');
        }
    }
}
