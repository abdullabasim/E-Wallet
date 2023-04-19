<?php
namespace App\ServicesData\Logs;

class LogsService
{

    public static function saveLogs($data =[])
    {
        activity()
            ->causedBy(auth()->user())
            ->withProperties($data)
            ->log('Jizdan');
    }


}
