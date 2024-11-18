<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Enrollment\Forms;


use Icinga\Common\Database;
use Icinga\Exception\Http\HttpMethodNotAllowedException;
use Icinga\Repository\Repository;
use Icinga\Util\StringHelper;
use Icinga\Web\Form;
use Icinga\Web\Url;
use ipl\Stdlib\Filter;

/**
 * Form for user authentication
 */
class RegisterUserForm extends Form
{
    use Database;

    const DEFAULT_CLASSES = 'icinga-controls';
    protected $db;
    protected $userBackend;
    protected $groupBackend;
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
        $this->setName('form_login');
        $this->setSubmitLabel($this->translate('Register'));
        $this->setProgressLabel($this->translate('Registering...'));
    }
    public function setDb($db)
    {
        $this->db = $db;
    }
    public function setBackend($userBackend, $groupBackend)
    {
        $this->userBackend = $userBackend;
        $this->groupBackend = $groupBackend;
    }


    /**
     * {@inheritdoc}
     */
    public function createElements(array $formData)
    {
        $this->addElement(
            'text',
            'username',
            array(
                'placeholder'   => $formData['username'],
                'disabled'          => true
            )
        );
        $this->addElement(
            'password',
            'password',
            array(
                'required'      => true,
                'autocomplete'  => 'current-password',
                'placeholder'   => $this->translate('Password'),
                'class'         => isset($formData['username']) ? 'autofocus' : ''
            )
        );
        $this->addElement(
            'password',
            'password_check',
            array(
                'required'      => true,
                'autocomplete'  => 'validate-password',
                'placeholder'   => $this->translate('Retype Password'),
                'class'         => isset($formData['username']) ? 'autofocus' : ''
            )
        );
        $this->addElement(
            'hidden',
            'secret'

        );

    }



    /**
     * {@inheritdoc}
     */
    public function onSuccess()
    {
        $password = $this->getElement('password')->getValue();
        $password_check = $this->getElement('password_check')->getValue();
        $secret = $this->getElement('secret')->getValue();

        if($secret === ""){
            $this->setErrorMessages(['Secret is empty']);
            return false;
        }
        if($password === ""){
            $this->setErrorMessages(['Password is empty']);
            return false;
        }

        if($password !== $password_check){
            $this->setErrorMessages(['Passwords do not match']);
            return false;
        }
        $user = \Icinga\Module\Enrollment\Model\Userenrollment::on($this->db)->filter(Filter::equal('secret', $secret))->filter(Filter::equal('enabled', 'y'))->first();
        if($user === null){
            throw new HttpMethodNotAllowedException('Enrollment secret invalid');
        }
        /* @var $backend Repository */
        if($this->userBackend->select()->where('name',$user->name)->fetchRow() === false){
            $this->userBackend->insert($this->userBackend->getBaseTable(), ['name'=>$user->name, 'active'=>1, 'password'=>$password]);
            $groups = StringHelper::trimSplit($user->groups);
            foreach ($groups as $groupName){
                try{
                    $this->groupBackend->insert(
                        'group_membership',
                        array(
                            'group_name'    => $groupName,
                            'user_name'     => $user->name
                        )
                    );
                }catch (\Throwable $e){
                    //do nothing
                    //most likely some not deleted group link
                }

            }
            $user->status=99;
            $user->enabled='n';
            $this->setRedirectUrl(Url::fromPath('enrollment/register/message',['success'=>1]));
        }elseif($user->allow_password_reset){
            $this->userBackend->update($this->userBackend->getBaseTable(), ['password'=>$password],['name = ?' => $user->name]);
            $user->status=99;
            $user->enabled='n';
            $this->setRedirectUrl(Url::fromPath('enrollment/register/message',['success'=>1]));
        }else{
            $user->status=10;
            $user->enabled='n';
            $this->setRedirectUrl(Url::fromPath('enrollment/register/message',['success'=>2]));
        }



        $user->save();


        return true;
    }

}
