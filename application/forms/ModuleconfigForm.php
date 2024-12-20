<?php

/* Originally from Icinga Web 2 Reporting Module (c) Icinga GmbH | GPLv2+ */
/* generated by icingaweb2-module-scaffoldbuilder | GPLv2+ */

namespace Icinga\Module\Enrollment\Forms;

use Icinga\Forms\ConfigForm;

class ModuleconfigForm extends ConfigForm
{

    public function init()
    {

        $this->setName('enrollment_settings');
        $this->setSubmitLabel($this->translate('Save Changes'));
    }

    public function createElements(array $formData)
    {
        $this->addElement('text', 'icingaweb2_fqdn', [
            'label' => $this->translate('IcingaWeb2 FQDN'),
        ]);
        $this->addElement('text', 'icingaweb2_port', [
            'label' => $this->translate('IcingaWeb2 Port'),
        ]);
        $this->addElement('select', 'icingaweb2_scheme', [
            'label' => $this->translate('IcingaWeb2 Scheme'),
            'multiOptions'=>['http'=>'http', 'https'=>'https']
        ]);
        $this->addElement('text', 'mail_subject', [
            'label' => $this->translate('Mail Subject'),
        ]);

        $this->addElement('text', 'mail_from', [
            'label' => $this->translate('Mail Sender'),
        ]);
        $this->addElement('textarea', 'mail_body', [
            'label' => $this->translate('Mail Body'),
            'rows'=>10,
            'value'=> "A new User was created for you:\nInstance: %%INSTANCE%%\nUsername: %%USERNAME%%\nRegistrationUrl:\n%%REGISTRATIONURL%%\n"
        ]);

    }


}