<?php

namespace Sway\Component\Service\Exception;


class ServiceException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null) 
    {
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * Gets exception object on case when required parameter is not defined
     * @param string $parameterName Parameter's name
     * @return \Sway\Component\Service\Exception\ServiceException
     */
    public static function requiredParameterException(string $parameterName) : ServiceException
    {
        $serviceException = new ServiceException(
                sprintf("Parameter '%s' is required!", $parameterName)
        );
        return $serviceException;
    }
    
    /**
     * Gets exception object on case when service was not found
     * @param string $serviceName
     * @return \Sway\Component\Service\Exception\ServiceException
     */
    public static function notFoundException(string $serviceName) : ServiceException
    {
        $serviceException = new ServiceException(
                sprintf("Service '%s' not found", $serviceName)
        );
        return $serviceException;
    }
}


?>