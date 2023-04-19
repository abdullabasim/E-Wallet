<?php

namespace  App\ServicesData\FileCompress;

use Illuminate\Support\Facades\File;
use ZipArchive;
Class FileCompressZipService {

    public static function executeZipCompress($storagePath,$fileName,$havePassword = true)
    {

        $zipFilePath = $storagePath.$fileName.'.zip';




            $zip = new ZipArchive;
            if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {

                if($havePassword)
                   $zip->setPassword('5iWeTm7NyLf*1^u');

                $zip->addFile($storagePath.$fileName.'.xlsx',$fileName.'.xlsx');
                $zip->setEncryptionName($fileName.'.xlsx', ZipArchive::EM_AES_256);

                $zip->close();

                File::delete($storagePath.$fileName.'.xlsx');

                if(!File::exists($zipFilePath))
                    return null;

                return $zipFilePath;
            }
            else
                return null;



    }





}
