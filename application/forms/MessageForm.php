<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Enrollment\Forms;


use Icinga\Web\Form;
use Icinga\Web\Url;

/**
 * Form for user authentication
 */
class MessageForm extends Form
{

    const DEFAULT_CLASSES = 'icinga-controls';

    /**
     * Redirect URL
     */
    const REDIRECT_URL = 'dashboard';

    public static $defaultElementDecorators = [
        ['ViewHelper', ['separator' => '']],
        ['Help', []],
        ['Errors', ['separator' => '']],
        ['HtmlTag', ['tag' => 'div', 'class' => 'control-group']]
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setRequiredCue(null);
        $this->setName('form_message');
        $this->setSubmitLabel($this->translate('Goto Login'));
        $this->setProgressLabel($this->translate('Registering...'));
    }

    /**
     * {@inheritdoc}
     */
    public function createElements(array $formData)
    {

    }


    /**
     * {@inheritdoc}
     */
    public function onSuccess()
    {
        if($this->Auth()->isAuthenticated()){
            $this->Auth()->removeAuthorization();
        }

        $this->setRedirectUrl(Url::fromPath('authentication/login'));

        return true;
    }

}
