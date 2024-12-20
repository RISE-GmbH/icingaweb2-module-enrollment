<?php

/* originally from Icinga Web 2 X.509 Module | (c) 2018 Icinga GmbH | GPLv2 */
/* generated by icingaweb2-module-scaffoldbuilder | GPLv2+ */

namespace Icinga\Module\Enrollment\Controllers;

use Icinga\Exception\ConfigurationError;

use Icinga\Module\Enrollment\ActivitylogRestrictor;
use Icinga\Module\Enrollment\Common\Database;
use Icinga\Module\Enrollment\Controller;
use Icinga\Module\Enrollment\Model\Activitylog;

use Icinga\Module\Enrollment\ActivitylogTable;
use Icinga\Module\Enrollment\Web\Control\SearchBar\ObjectSuggestions;


use ipl\Web\Control\LimitControl;
use ipl\Web\Control\SortControl;

use ipl\Web\Url;
use ipl\Web\Widget\ButtonLink;

class ActivitylogsController extends Controller
{
    public function init()
    {
        parent::init();
        $this->assertPermission("enrollment/activitylog");
    }

    public function indexAction()
    {

        if ($this->hasPermission('enrollment/activitylog/modify')) {
            $this->addControl(
                (new ButtonLink(
                    $this->translate('New Activitylog'),
                    Url::fromPath('enrollment/activitylog/new'),
                    'plus'
                ))->openInModal()
            );
        }

        $this->addTitleTab($this->translate('Activitylogs'));

        try {
            $conn = Database::get();
        } catch (ConfigurationError $_) {
            $this->render('missing-resource', null, true);
            return;
        }

        $models = Activitylog::on($conn)
            ->with([])
            ->withColumns([]);


        $sortColumns = [
            'name' => $this->translate('Name'),

        ];
        $restrictor = new ActivitylogRestrictor();
        $restrictor->applyRestrictions($models);

        $limitControl = $this->createLimitControl();
        $paginator = $this->createPaginationControl($models);
        $sortControl = $this->createSortControl($models, $sortColumns);

        $searchBar = $this->createSearchBar($models, [
            $limitControl->getLimitParam(),
            $sortControl->getSortParam()
        ]);

        if ($searchBar->hasBeenSent() && ! $searchBar->isValid()) {
            if ($searchBar->hasBeenSubmitted()) {
                $filter = $this->getFilter();
            } else {
                $this->addControl($searchBar);
                $this->sendMultipartUpdate();

                return;
            }
        } else {
            $filter = $searchBar->getFilter();
        }

        $models->peekAhead($this->view->compact);

        $models->filter($filter);

        $this->addControl($paginator);
        $this->addControl($sortControl);
        $this->addControl($limitControl);
        $this->addControl($searchBar);

        $this->addContent((new ActivitylogTable())->setData($models));

        if (! $searchBar->hasBeenSubmitted() && $searchBar->hasBeenSent()) {
            $this->sendMultipartUpdate(); // Updates the browser search bar
        }
    }

    public function completeAction()
    {
        $this->getDocument()->add(
            (new ObjectSuggestions())
                ->setModel(Activitylog::class)
                ->forRequest($this->getServerRequest())
        );
    }

    public function searchEditorAction()
    {
        $editor = $this->createSearchEditor(Activitylog::on(Database::get()), [
            LimitControl::DEFAULT_LIMIT_PARAM,
            SortControl::DEFAULT_SORT_PARAM
        ]);

        $this->getDocument()->add($editor);
        $this->setTitle(t('Adjust Filter'));
    }


}
