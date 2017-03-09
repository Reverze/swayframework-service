<?php

namespace Sway\Component\Service;

use Sway\Component\Dependency\DependencyInterface;

class ContainerInterface extends DependencyInterface
{
    /**
     * Service's container
     * @var \Sway\Component\Service\Container
     */
    private $container = null;
    
    
    public function __construct()
    {
        
    }
    
    protected function dependencyController()
    {
        $this->container = $this->getDependency('serviceContainer');
    }
    
    /**
     * Get instance of service's class
     * @param string $serviceName
     * @return object
     */
    public function get(string $serviceName)
    {
        return $this->container->get($serviceName);
    }
    
    /**
     * Gets parameter's container interface
     * @return \Sway\Component\Parameter\ContainerInterface
     */
    public function getParameterContainerInterface()
    {
        return $this->container->getParameterContainerInterface();
    }
    
    
}


?>
