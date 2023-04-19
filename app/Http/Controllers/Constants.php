<?php

namespace App\Http\Controllers;

class Constants
{


    const HTTP_OPERATION_SUCCESS ="Operation accomplished successfully";
    const HTTP_INVALID = "Invalid input data";
    const HTTP_ERROR = "Something went wrong";
    const HTTP_UNAUTH = "unauthorized";
    const HTTP_USER_BLOCKED = "user blocked";
    const HTTP_NOT_FOUND = "not found";
    const HTTP_BAD_REQUEST = "bad request";
    const HTTP_OLD_PASSWORD_NOT_MATCH = "old password not match";
    const HTTP_NEW_PASSWORD_SAME_OLD = "new password same old";
    const HTTP_LOGGED_SUCCESS = "Success logged in";
    const HTTP_UPDATE_DATA = "Success updated  ";
    const HTTP_DELETE_DATA = "Success deleted  ";

    const  USER_TYPE = ['admin', 'company','client'];

    const USER_TYPES =  [
        'admin' => 'admin',
        'company' => 'company',
        'client' => 'client',

    ];

}
