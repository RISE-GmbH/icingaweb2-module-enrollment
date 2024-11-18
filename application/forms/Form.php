<?php

/* originally from Icinga Web 2 Reporting Module | (c) 2019 Icinga GmbH | GPLv2 */
/* generated by icingaweb2-module-scaffoldbuilder | GPLv2+ */

namespace Icinga\Module\Enrollment\Forms;

use Icinga\Module\Enrollment\Model\;
use ipl\Html\Contract\FormSubmitElement;
use ipl\Html\HtmlDocument;
use ipl\Sql\Connection;
use ipl\Web\Compat\CompatForm;

class Form extends CompatForm
{

    protected $id;
    /** @var Connection $db */
    protected $db;
    protected $renderCreateAndShowButton = false;
    protected $submitButtonLabel;

    /**
     * Set whether the create and show submit button should be rendered
     *
     * @param bool $renderCreateAndShowButton
     *
     * @return \Icinga\Module\Enrollment\Forms\Form
     */
    public function setRenderCreateAndShowButton(bool $renderCreateAndShowButton): self
    {
        $this->renderCreateAndShowButton = $renderCreateAndShowButton;

        return $this;
    }

    /**
     * Create a new form instance with the given  id
     *
     * @param $id
     *
     * @return \Icinga\Module\Enrollment\Forms\Form
     */
    public static function fromId($id): self
    {
        $form = new static();
        $form->id = $id;

        return $form;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDb() :Connection
    {
        return $this->db;
    }

    public function setDb($db)
    {
        $this->db = $db;
        return $this;
    }


    public function hasBeenSubmitted(): bool
    {
        return $this->hasBeenSent() && (
                $this->getPopulatedValue('submit')
                || $this->getPopulatedValue('create_show')
                || $this->getPopulatedValue('remove')
                || $this->getPopulatedValue('wipe')
            );
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
    /**
     * Get the label of the submit button
     *
     * @return string
     */
    public function getSubmitButtonLabel(): string
    {
        if ($this->submitButtonLabel !== null) {
            return $this->submitButtonLabel;
        }

        return $this->id === null ? $this->translate('Create ') : $this->translate('Update ');
    }
    protected function assemble()
    {

        $this->addElement('text', 'name', [
            'required' => true,
            'label' => 'Name'
        ]);

        $this->addElement('checkbox', 'enabled', [
            'label' => 'Enabled'
        ]);

        $this->addElement('submit', 'submit', [
            'label' => $this->getSubmitButtonLabel()
        ]);
        if ($this->id !== null) {
            /** @var FormSubmitElement $removeButton */
            $removeButton = $this->createElement('submit', 'remove', [
                'label'          => $this->translate('Remove '),
                'class'          => 'btn-remove',
                'formnovalidate' => true
            ]);
            $this->registerElement($removeButton);

            /** @var HtmlDocument $wrapper */
            $wrapper = $this->getElement('submit')->getWrapper();
            $wrapper->prepend($removeButton);


        } elseif ($this->renderCreateAndShowButton) {
            $createAndShow = $this->createElement('submit', 'create_show', [
                'label' => $this->translate('Create and Show'),
            ]);
            $this->registerElement($createAndShow);

            /** @var HtmlDocument $wrapper */
            $wrapper = $this->getElement('submit')->getWrapper();
            $wrapper->prepend($createAndShow);
        }

    }

    public function onSuccess()
    {
        /** @var Connection db */
        $db = $this->getDb();

        $values = $this->getValues();



        $dbValues=[];
        $dbValues['name']=$values['name'];
        $model = new ();

        if ($this->id === null) {
            $values['id'] =  random_bytes(20);
            $model->setValues($values);
            $model->save();
        } else {
            if ($this->getPopulatedValue('remove')) {
                $model->id=$this->id;
                $model->delete();
                return;
            }

            $model->setValues($values);
            $model->save();

        }

    }


}
