<?php

namespace Sway\Component\Service;

use Sway\Component\Dependency\DependencyInterface;
use Sway\Component\Init\Component;

class Init extends Component
{
    /**
     * Array which contains all defined services
     * @var array
     */
    private $services = array();
    
    protected function dependencyController() 
    {
        if ($this->getDependency('framework')->hasCfg('framework/service')){
            $this->services = $this->getDependency('framework')->getCfg('framework/service');
        }
    }
    
    /**
     * Initializes service container
     * @return \Sway\Component\Service\Container
     */
    public function init()
    {
        $serviceContainer = Container::createFromList($this->services);
        return $serviceContainer;
    }
}


?>

