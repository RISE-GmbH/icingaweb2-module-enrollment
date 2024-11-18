<?php


namespace Icinga\Module\Enrollment\Clicommands;

use Icinga\Cli\Command;
use Icinga\Data\ResourceFactory;


class SetCommand extends Command
{

    /**
     * USAGE:
     *
     *   icingacli oidc backend
     */
    public function resourceAction()
    {
        $options = ResourceFactory::getResourceConfigs('db')->keys();
        $name = $this->params->getRequired('name');
        if(in_array($name,$options)){
            $lastConfig =   $this->Config('config')->getSection('backend')->toArray();
            $lastConfig['resource']=$name;
            $this->Config('config')->setSection('backend',$lastConfig)->saveIni();
            echo "Resource set successfully\n";
            exit(0);
        }else{
            echo "Only these Resources are allowed:\n\n";
            foreach ($options as $name){
                echo $name."\n";

            }
            exit(1);
        }

    }
}
