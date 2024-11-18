<?php

/* Originally from Icinga Web 2 Reporting Module (c) Icinga GmbH | GPLv2+ */
/* icingaweb2-module-scaffoldbuilder 2023 | GPLv2+ */

namespace Icinga\Module\Enrollment\Controllers;


use Icinga\Application\Config;
use Icinga\Web\Controller;
use Icinga\Module\Enrollment\Forms\ModuleconfigForm;

class ModuleconfigController extends Controller
{


    public function indexAction()
    {
        $this->assertPermission("config/enrollment");
        $form = (new ModuleconfigForm())
            ->setIniConfig(Config::module('enrollment', "config"));

        $form->handleRequest();

        $this->view->tabs = $this->Module()->getConfigTabs()->activate('config/moduleconfig');
        $this->view->form = $form;
    }


    public function createTabs()
    {
        $tabs = $this->getTabs();

        $tabs->add('enrollment/config', [
            'label' => $this->translate('Configure Enrollment'),
            'url' => 'enrollment/config'
        ]);

        return $tabs;

    }

}