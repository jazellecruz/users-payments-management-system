<?php 

class CustomException extends Exception {
    private $errorDesc;
    private $statusCode;
    private $userMessage;
    private $exceptionType;
    private $errorObj;

    public function __construct(
        $error,
        $errorDesc = "An error occurred", 
        $exceptionType = null,
        $code = 500, 
        $userMessage = "Internal Server Error. Could not process your request at this time.",
    ) {
        parent::__construct($error);
        $this->errorDesc = $errorDesc;
        $this->exceptionType = $exceptionType;
        $this->statusCode = $code;
        $this->userMessage = $userMessage;
    }

    public function getErrorDesc() {
        return $this->errorDesc;
    }

    public function getStatusCode() {
        return $this->statusCode;
    }

    public function getUserMessage() {
        return $this->userMessage;
    }

    public function getExceptionType() {
        return $this->exceptionType;
    }

    public function setErrorObj($error) {
        $this->errorObj = $error;
    }
    
    public function getErrorObj() {
        return $this->errorObj;
    }
}

?>