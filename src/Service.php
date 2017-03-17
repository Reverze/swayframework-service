<?php

namespace Sway\Component\Service;

use Sway\Component\Dependency\DependencyInterface;

class Service extends DependencyInterface
{
    /**
     * Service's name
     * @var string
     */
    private $serviceName = null;
    
    /**
     * Service's class path
     * @var string
     */
    private $serviceClass = null;
    
    /**
     * Service's arguments
     * @var array
     */
    private $serviceArguments = array();
    
    /**
     * Object of final class
     * @var object
     */
    private $serviceInstance = null;
    
    /**
     * Determines if arguments are prepared or not
     * @var boolean
     */
    private $isArgumentsPrepared = false;
    
    /**
     *
     * @var \Sway\Component\Service\ContainerInterface
     */
    private $containerInterface = null;
    
    /**
     * Service instance if was registered manually
     * @var object
     */
    private $prestance = null;
    
    /**
     * Service's tags
     * @var array
     */
    private $serviceTags = array();
    
    public function __construct(string $serviceName, string $serviceClass, array $serviceArguments, array $serviceTags = array()) 
    {
        /* If server's name has not been defined before */
        if (empty($this->serviceName)){
            $this->serviceName = (string) $serviceName;
        }
        
        /* If server's class path has not been defined before */
        if (empty($this->serviceClass)){
            $this->serviceClass = (string) $serviceClass;
            
            /**
             * Class path need prefix '\'
             */
            if ($this->serviceClass[0] !== '\\'){
                $this->serviceClass = '\\' . $this->serviceClass;
            }
        }
        
        if (empty($this->serviceArguments)){
            $this->serviceArguments = (array) $serviceArguments;
        }
        
        $this->serviceTags = $serviceTags;
    }
    
    protected function dependencyController() 
    {
        $this->containerInterface = $this->getDependency('service');
    }
    
    /**
     * Gets service's name
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }
    
    /**
     * Gets service's class path
     * @return string
     */
    public function getServiceClass()
    {
        return $this->serviceClass;
    }
    
    /**
     * Gets service's arguments
     * @return array
     */
    public function getServiceArguments()
    {
        return $this->serviceArguments;
    }
    
    /**
     * Checks if arguments are prepared
     * @return boolean
     */
    private function isArgumentsPrepared()
    {
        return $this->isArgumentsPrepared;
    }
    
    /**
     * Sets service prestance
     * @param type $instance
     */
    public function prestance($instance)
    {
        $this->prestance = $instance;
    }
    
    private function prepareArguments()
    {
        /**
         * Array with prepared arguments
         */
        $preparedArguments = array();
        /**
         * Podczas przygotowania argumentow interesuje nas:
         * - odwolania do innych uslug eg. @serviceName, @userService.getName
         */
        foreach ($this->serviceArguments as $argument){
            if (is_string($argument)){
                
                /**
                 * Recall to another existing service
                 */
                if ($argument[0] === '@'){
                    $argument = str_replace("@", "", $argument);
                    
                    /**
                     * Variable explodedRecall is call tree
                     */
                    $explodedRecall = explode(".", $argument);
                    
                    
                    
                    /**
                     * As first context in call process we set service's class instance
                     */
                    $currentContext = $this->containerInterface->get($explodedRecall[0]);
                    
                    
                    for ($call = 1; $call < sizeof($explodedRecall); $call++){
                        $propertyName = $explodedRecall[$call];
                        $currentContext = $currentContext->$propertyName;
                    }
                    
                    array_push($preparedArguments, $currentContext);
                    
                }
                else if (preg_match('/^%([a-zA-Z0-9\_\-\.]+)%$/', $argument, $regexMatches)){
                    
                    
                    $parameterName = $regexMatches[1] ?? null;
                    if (!empty($parameterName)){
                        array_push($preparedArguments,
                            $this->containerInterface->getParameterContainerInterface()->get($parameterName)
                        );
                    }
                    else{
                        array_push($preparedArguments, $argument);
                    }
                }
                else{
                    array_push($preparedArguments, $argument);
                }
                
            }
        }
        
        $this->isArgumentsPrepared = true;
        
        $this->serviceArguments = $preparedArguments;
    }
    
    /**
     * Gets instance of final class
     * @return object
     */
    public function getInstance()
    {
        if (!empty($this->prestance)){
            return $this->prestance;
        }
        
        $classReflector = new \ReflectionClass($this->serviceClass);

        if (empty($this->serviceArguments) && !sizeof($this->serviceArguments)) {
            $this->serviceInstance = $classReflector->newInstance();
        } 
        else {
            if (!$this->isArgumentsPrepared()){
                $this->prepareArguments();
            }
            $this->serviceInstance = $classReflector->newInstanceArgs($this->serviceArguments);
        }

        return $this->serviceInstance;
    }
    
    
}


?>
