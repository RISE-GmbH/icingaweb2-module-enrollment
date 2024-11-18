<?php

/* Icinga Web 2 X.509 Module | (c) 2023 Icinga GmbH | GPLv2 */

namespace Icinga\Module\Enrollment\Model;


use ipl\Orm\Behavior\MillisecondTimestamp;
use ipl\Orm\Behaviors;
use ipl\Sql\Connection;

/**
 * A database model for Activitylog with the tbl_activitylog table
 *
 */
class Activitylog extends DbModel
{
    public function getTableName(): string
    {
        return 'enrollment_activitylog';
    }

    public function getKeyName()
    {
        return 'id';
    }

    public function getColumnDefinitions(): array
    {
        return [
            'username'=>[
                'fieldtype'=>'text',
                'label'=>'Name',
                'description'=>t('A Name of something'),
                'required'=>true
            ],
            'task'=>[
                'fieldtype'=>'text',
                'label'=>t('Task'),
                'description'=>t('Task that was executed'),
            ],
            'ctime'=>[
                'fieldtype'=>t('localDateTime'),
                'label'=>t('Created At'),
                'description'=>t('A Creation Time'),
            ]
        ];
    }

    public function createBehaviors(Behaviors $behaviors): void
    {
        $behaviors->add(new MillisecondTimestamp(['ctime']));

    }
    public function beforeSave(Connection $db)
    {
        parent::beforeSave($db);
        $this->ctime = new \DateTime();
    }



}
