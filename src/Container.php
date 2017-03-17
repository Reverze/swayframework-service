<?php

namespace Sway\Component\Service;

use Sway\Component\Service\Exception;
use Sway\Component\Parameter;

use Sway\Component\Dependency\DependencyInterface;

class Container extends DependencyInterface
{
    /**
     * This array contains service objects
     * @var array
     */
    private $services = array();
    
    /**
     * Objects of service's class
     * @var array
     */
    private $objects = array();
    
    /**
     * Parameter's container interface
     * @var \Sway\Component\Parameter\Container
     */
    private $parameterContainerInterface = null;
    
    /**
     * Service's to unbox
     * @var array
     */
    private $servicesToUnbox = null;
    
    public function __construct()
    {
        
    }
    
    protected function dependencyController() 
    {
        $this->parameterContainerInterface = $this->getDependency('parameter');
        $this->unboxServiceList(array());
    }
    
    /**
     * Sets parameter's container interface
     * @param \Sway\Component\Parameter\ContainerInterface $parameterContainerInterface
     */
    public function setParameterContainerInterface(Parameter\ContainerInterface $parameterContainerInterface = null)
    {
        if (!empty($parameterContainerInterface)){
            $this->parameterContainerInterface = $parameterContainerInterface;
        }
    }
    
    public function unboxServiceList(array $serviceList)
    {
        if (sizeof($serviceList)){
            $this->servicesToUnbox = $serviceList;
            return;
        }
        
        
        if (is_array($this->servicesToUnbox)){
            foreach ($this->servicesToUnbox as $serviceName => $serviceParameters){
                /**
                 * Parameter 'class' is required parameter.
                 * Parameter 'class' determine path to class
                 */
                if (!isset($serviceParameters['class'])){
                    throw Exception\ServiceException::requiredParameterException('class');
                }
                /**
                 * Arguments is optional parameter
                 * Service's name is always lower
                 */
                $service = new Service(strtolower($serviceName), $serviceParameters['class'], $serviceParameters['arguments'] ?? array());


                /**
                 * Pushs service into container
                 */
                $this->addService($service);
            }

        }
    }
    
    /**
     * Registers object as service
     * @param string $serviceName
     * @param object $serviceObject
     */
    public function registerService(string $serviceName, $serviceObject)
    {
        $service = new Service(strtolower($serviceName), get_class($serviceObject), array());
        $service->prestance($serviceObject);
        $this->objects[strtolower($serviceName)] = $serviceObject;
        $this->addService($service);
    }
    
    /**
     * Adds service into container
     * @param \Sway\Component\Service\Service $service
     * @return bool
     */
    protected function addService(Service $service) : bool
    {
        return (bool) array_push($this->services, $service);
    }

    

    /**
     * Creates a service container using service list
     * @param array $serviceList
     * @return \Sway\Component\Service\Container
     */
    public static function createFromList(array $serviceList) : Container
    {
        $serviceContainer = new Container();
        $serviceContainer->unboxServiceList($serviceList);
        return $serviceContainer;
    }
    
    /**
     * Checks if service is defined
     * @param string $serviceName
     * @return bool
     */
    public function has(string $serviceName) : bool
    {
        try {
            $this->get($serviceName);
            return true;         
        } 
        catch (Exception\ServiceException $ex) {
            return false;
        }
    }
    
    /**
     * Get instance of service's class
     * @param string $serviceName
     * @return object
     */
    public function get(string $serviceName)
    {
        $serviceName = strtolower($serviceName);
        
        if (!isset($this->objects[$serviceName])){
            foreach ($this->services as $service){
                
                if ($service->getServiceName() === strtolower($serviceName)){
                    $this->getDependency('injector')->inject($service);
                    $this->objects[$serviceName] = $service->getInstance();
                    break;
                }
            }  
            
            /**
             * If objects is not created it means that service not exists
             */
            if (!isset($this->objects[$serviceName])){
                throw Exception\ServiceException::notFoundException($serviceName);
            }
        }
        
        return $this->objects[$serviceName];
    }
    
    /**
     * Gets parameter's container interface
     * @return \Sway\Component\Parameter\ContainerInterface
     */
    public function getParameterContainerInterface()
    {
        return $this->parameterContainerInterface;
    }
    
}


?>