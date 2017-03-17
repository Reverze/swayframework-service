<?php

namespace Sway\Component\Service;

use Sway\Component\Dependency\DependencyInterface;
use Sway\Component\Init\Component;

class InitInterface extends Component
{
    public function init()
    {
        $containerInterface = new ContainerInterface();
        return $containerInterface;
    }
    
}


?>

