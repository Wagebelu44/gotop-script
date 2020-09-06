<?php

if (! function_exists('str_limit')) {
    /**
     * Limit the number of characters in a string.
     *
     * @param  string  $value
     * @param  int     $limit
     * @param  string  $end
     * @return string
     */
    function str_limit($value, $limit = 100, $end = '...')
    {
        return Str::limit($value, $limit, $end);
    }
}

/**
 * Get notification setting by key.
 *
 * @param  string  $title
 * @param  int     $type
 * @return \App\SettingNOtification
 */
if (! function_exists('notification')) {
    function notification($title, $type)
    {
        return \App\Models\SettingNotification::where(['title' => $title, 'type' => $type])->first();
    }
}

/**
 * Get general setting by key.
 *
 * @param  string  $keyword
 * @return \App\CmsSettingGeneral
 */
if (! function_exists('generalSetting')) {
    function generalSetting($keyword)
    {
        return \App\CmsSettingGeneral::where(['keyword' => $keyword])->first();
    }
}

/**
 * Get staffEmails setting by key.
 *
 * @param  string  $action
 * @return \Illuminate\Support\Collection
 */
if (! function_exists('staffEmails')) {
    function staffEmails($action)
    {
        return \App\CmsStaffEmail::where($action, '1')->pluck('email');
    }
}
