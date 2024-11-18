<?php

/* Icinga Web 2 X.509 Module | (c) 2023 Icinga GmbH | GPLv2 */

namespace Icinga\Module\Enrollment\Model;

use DateTime;
use DateTimeZone;
use Icinga\Application\Icinga;

use Icinga\Authentication\Auth;
use ipl\Orm\Behavior\BoolCast;
use ipl\Orm\Behavior\MillisecondTimestamp;
use ipl\Orm\Behaviors;
use ipl\Sql\Connection;

/**
 * A database model for User with the tbl_user table
 *
 */
class Userenrollment extends DbModel
{
    public function getStatusOptions()
    {
        return [0=>t('unknown'),1=>t('open'),2=>t('mail sent'),10=>t('already registered'),20=>t('expired'),99=>t('completed')];
    }

    public function getStatusAsText(): string
    {
        $options = $this->getStatusOptions();
        $now  = new DateTime();
        $now->setTimezone(new DateTimeZone(date_default_timezone_get()));

        if($this->etime <= $now){
            $this->status = 20;
        }
        if(isset($options[intval($this->status)])){
            return $options[intval($this->status)];
        }else{
            return "";
        }
    }

    public function getTableName(): string
    {
        return 'enrollment_userenrollment';
    }

    public function getKeyName()
    {
        return 'id';
    }

    public function getColumnDefinitions(): array
    {

        return [
            'name'=>[
                'fieldtype'=>'text',
                'label'=>'Name',
                'description'=>t('Name of the user'),
                'required'=>true
            ],
            'email'=>[
                'fieldtype'=>'text',
                'label'=>'Email',
                'description'=>t('Email of the user'),
                'required'=>false
            ],

            'status'=>[
                'fieldtype'=>'hidden',
                'label'=>t('Status'),
            ],
            'user_backend'=>[
                'fieldtype'=>'select',
                'label'=>t('User Backend'),
                'description'=>t('The User Backend for this User'),
            ],
            'group_backend'=>[
                'fieldtype'=>'select',
                'label'=>t('Group Backend'),
                'description'=>t('The Group Backend for this User'),
            ],
            'allow_password_reset'=>[
                'fieldtype'=>'checkbox',
                'label'=>t('allow password reset'),
                'description'=>t('Allow to use this enrollment for password resetting of an already existing user'),
            ],
            'groups'=>[
                'fieldtype'=>'text',
                'label'=>'Groups',
                'description'=>t('List of groups this user will be added to'),
                'required'=>true
            ],
            'enabled'=>[
                'fieldtype'=>'checkbox',
                'label'=>t('Enabled'),
                'description'=>t('Enable or disable this user enrollment'),
            ],
            'etime'=>[
                'fieldtype'=>'localDateTime',
                'label'=>t('Expires At'),
                'description'=>t('After this time the request is no longer valid'),
                'value'=>(new DateTime('now', new DateTimeZone(date_default_timezone_get())))->modify('+12 hours'),
            ],
            'secret'=>[
                'fieldtype'=>'hidden',
                'label'=>t('Secret'),
            ],
            'mtime'=>[
                'fieldtype'=>'localDateTime',
                'label'=>t('Modified At'),
                'description'=>t('A Modification Time'),
            ],
            'ctime'=>[
                'fieldtype'=>'localDateTime',
                'label'=>t('Created At'),
                'description'=>t('A Creation Time'),
            ]
        ];
    }
    public function beforeSave(Connection $db)
    {
        parent::beforeSave($db);
        $activityLog = new Activitylog();

        if(Icinga::app()->isCli()){
            $activityLog->username = "cli";
        }else{
            if(Auth::getInstance()->isAuthenticated()){
                $activityLog->username = Auth::getInstance()->getUser()->getUsername();
            }else{
                $activityLog->username = 'Enrollment-process';

            }
        }
        if( isset($this->id) && $this->id !== null){
            $this->mtime = new \DateTime();
            $activityLog->task=sprintf("%s modified enrollment for user %s",$activityLog->username,$this->name);

        }else{
            $this->status = 1;
            $this->ctime = new \DateTime();
            $this->secret = bin2hex(random_bytes(32));
            $activityLog->task=sprintf("%s created enrollment for user %s",$activityLog->username,$this->name);

        }
        $activityLog->save(false);

    }


    public function createBehaviors(Behaviors $behaviors): void
    {
        $behaviors->add((new BoolCast(['enabled'])));
        $behaviors->add(new MillisecondTimestamp(['etime']));
        $behaviors->add(new MillisecondTimestamp(['ctime']));
        $behaviors->add(new MillisecondTimestamp(['mtime']));

    }



}
