<?php

/** @var \Icinga\Application\Modules\Module $this */

$section = $this->menuSection(N_('Enrollment'), [
    'permission' => 'enrollment',
    'url' => 'enrollment',
    'icon' => 'img/enrollment/enrollment-icon.png',
    'priority' => 910
]);


?>

<?php


$this->provideConfigTab('config/moduleconfig', array(
    'title' => $this->translate('Module Configuration'),
    'label' => $this->translate('Module Configuration'),
    'url' => 'moduleconfig'
));

$this->providePermission('config/enrollment', $this->translate('allow access to enrollment configuration'));
$this->providePermission('enrollment/activitylog', $this->translate('allow access to activitylogs'));
$this->providePermission('enrollment/activitylog/modify', $this->translate('allow to modify activitylogs'));

$this->providePermission('enrollment/userenrollment', $this->translate('allow access to userenrollments'));
$this->providePermission('enrollment/userenrollment/modify', $this->translate('allow to modify userenrollment'));


?>
<?php

$this->provideConfigTab('backend', array(
    'title' => $this->translate('Configure the database backend'),
    'label' => $this->translate('Backend'),
    'url' => 'config/backend'
));

?>
<?php

$section->add(N_('User Enrollments'))
    ->setUrl('enrollment/userenrollments')
    ->setPermission('enrollment/userenrollment')
    ->setPriority(20);


$section->add(N_('Activitylog'))
    ->setUrl('enrollment/activitylogs')
    ->setPermission('enrollment/activitylog')
    ->setPriority(30);
?>