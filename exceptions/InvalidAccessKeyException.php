<?php
namespace Pi\Livestream\Exceptions;

use Exception;

class InvalidAccessKeyException extends Exception
{
    public function render()
    {
        return response()->json(
            [
                "message" => "Invalid Access Key",
                "success" => false,
             ],
            401
        );
    }
}
