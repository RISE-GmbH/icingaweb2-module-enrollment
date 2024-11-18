<?php

/* originally from Icinga Web 2 X.509 Module | (c) 2018 Icinga GmbH | GPLv2 */
/* generated by icingaweb2-module-scaffoldbuilder | GPLv2+ */

namespace Icinga\Module\Enrollment\Controllers;


use DateTime;
use DateTimeZone;
use Icinga\Application\Icinga;

use Icinga\Module\Enrollment\BackendHelper;
use Icinga\Module\Enrollment\Controller;
use Icinga\Module\Enrollment\Forms\MessageForm;
use Icinga\Module\Enrollment\Forms\RegisterUserForm;

use Icinga\Module\Enrollment\Common\Database;

use Icinga\Module\Enrollment\Model\Userenrollment;
use Icinga\Web\Url;
use ipl\Html\Html;
use ipl\Stdlib\Filter;


class RegisterController extends Controller
{
    use BackendHelper;
    /** @var Userenrollment The User object */
    protected $user;
    protected $db;
    protected $requiresAuthentication = false;
    protected $innerLayout = 'inline';


    public function init()
    {
        $this->db=Database::get();
    }


    public function userAction(){
        $secret = $this->params->getRequired('secret');
        $user = Userenrollment::on($this->db)->filter(Filter::equal('secret', $secret))->filter(Filter::equal('enabled', 'y'))->first();
        if($user === null){
            $this->redirect('enrollment/register/message');
        }

        $now  = new DateTime();
        $now->setTimezone(new DateTimeZone(date_default_timezone_get()));

        if($user->etime <= $now){
            $user->status = 20;
            $user->save();
            $this->redirect('enrollment/register/message');
        }

        $icinga = Icinga::app();

        if (($requiresSetup = $icinga->requiresSetup()) && $icinga->setupTokenExists()) {
            $this->redirectNow(Url::fromPath('setup'));
        }
        $form = new RegisterUserForm();
        $form->setDb($this->db);

        $userBackend = $this->getUserBackend($user->user_backend, 'Icinga\Data\Updatable');
        $groupBackend = $this->getUserGroupBackend($user->group_backend, 'Icinga\Data\Updatable');
        $form->setBackend($userBackend,$groupBackend);
        $form->populate(['username'=>$user->name, 'secret'=>$secret]);
        $this->view->defaultTitle = $this->translate('Icinga Web 2 User Enrollment');

        $this->view->requiresSetup = $requiresSetup;

        $this->view->addHelperPath(Icinga::app()->getBaseDir()
            . DIRECTORY_SEPARATOR . "application/views/helpers/");
        $this->view->addScriptPath(Icinga::app()->getBaseDir()
            . DIRECTORY_SEPARATOR . "application/views/scripts/");
        $this->_helper->viewRenderer->setRender('authentication/login', null, true);


        $form->handleRequest();

        $this->view->form = $form;
    }
    public function messageAction(){
        $success = $this->params->get('success','0');

        $this->view->addHelperPath(Icinga::app()->getBaseDir()
            . DIRECTORY_SEPARATOR . "application/views/helpers/");
        $this->view->addScriptPath(Icinga::app()->getBaseDir()
            . DIRECTORY_SEPARATOR . "application/views/scripts/");
        $this->_helper->viewRenderer->setRender('authentication/login', null, true);
        $this->view->defaultTitle = $this->translate('Icinga Web 2 User Enrollment');

        $this->view->requiresSetup = false;
        $form = new MessageForm();
        $form->handleRequest();

        if($success === "1"){
            $notificationUl= Html::tag('ul', ['role'=>'alert', 'id'=>'notifications']);
            $notificationLi= Html::tag('li', ['class'=>'success fading-out'],'User enrolled successfully!');
            $notificationI= Html::tag('i', ['class'=>'icon fa fa-check-circle']);
            $notificationLi->add($notificationI);
            $notificationUl->add($notificationLi);

        }elseif($success === "2"){
            $notificationUl= Html::tag('ul', ['role'=>'alert', 'id'=>'notifications']);
            $notificationLi= Html::tag('li', ['class'=>'error fading-out'],'User already registered!');
            $notificationI= Html::tag('i', ['class'=>'icon fa fa-times']);
            $notificationLi->add($notificationI);
            $notificationUl->add($notificationLi);

        }elseif($success === "3"){
            $notificationUl= Html::tag('ul', ['role'=>'alert', 'id'=>'notifications']);
            $notificationLi= Html::tag('li', ['class'=>'success fading-out'],'User password updated!');
            $notificationI= Html::tag('i', ['class'=>'icon fa fa-circle']);
            $notificationLi->add($notificationI);
            $notificationUl->add($notificationLi);

        }else{
            $notificationUl= Html::tag('ul', ['role'=>'alert', 'id'=>'notifications']);
            $notificationLi= Html::tag('li', ['class'=>'error fading-out'],'User enrollment failed!');
            $notificationI= Html::tag('i', ['class'=>'icon fa fa-times']);
            $notificationLi->add($notificationI);
            $notificationUl->add($notificationLi);
        }
        $br = Html::tag('br');
        $form=$notificationUl.$br.$form;

        $this->view->form=$form;
    }
}