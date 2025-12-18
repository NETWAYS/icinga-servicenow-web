<?php

namespace Icinga\Module\Servicenow\Controllers;

use Icinga\Module\Servicenow\Common\Database;
use Icinga\Module\Servicenow\Model\Incident;
use Icinga\Module\Servicenow\Web\IncidentSuggestions;
use Icinga\Module\Servicenow\Widget\IncidentRenderer;

use ipl\Stdlib\Filter;
use ipl\Web\Compat\CompatController;
use ipl\Web\Compat\SearchControls;
use ipl\Web\Control\LimitControl;
use ipl\Web\Control\SortControl;
use ipl\Web\Filter\QueryString;
use ipl\Web\Layout\ItemLayout;
use ipl\Web\Widget\ItemList;

class IncidentsController extends CompatController
{
    use Database;
    use SearchControls;

    public function completeAction()
    {
        $suggestions = new IncidentSuggestions($this->getDb());
        $suggestions->forRequest($this->getServerRequest());
        $this->getDocument()->addHtml($suggestions);
    }

    public function searchEditorAction()
    {
        $editor = $this->createSearchEditor(Incident::on($this->getDb()), [
            LimitControl::DEFAULT_LIMIT_PARAM,
            SortControl::DEFAULT_SORT_PARAM
        ]);

        $this->getDocument()->addHtml($editor);
        $this->setTitle(t('Adjust Filter'));
    }

    public function indexAction()
    {
        $this->addTitleTab(t('ServiceNow Incidents'));

        $host = $this->params->get('host_name');
        $service = $this->params->get('service_name');

        $filter = Filter::any();

        // This currently shows all incidents for the host.
        // Not sure if this is a bug or a feature.
        if (isset($host)) {
            $filter = Filter::any(
                Filter::equal('host_name', $host),
            );
        }

        if (isset($service)) {
            $filter = Filter::all(
                Filter::equal('host_name', $host),
                Filter::equal('service_name', $service)
            );
        }

        $db = $this->getDb();

        $incidents = Incident::on($db)->orderBy('db_created_at', SORT_ASC);

        $paginationControl = $this->createPaginationControl($incidents);
        $limitControl = $this->createLimitControl();
        $sortControl = $this->createSortControl($incidents, [
            'incident.db_created_at' => 'Created',
            'incident.service_name' => 'Service',
            'incident.host_name' => 'Host',
        ]);

        $searchBar = $this->createSearchBar($incidents, [
            $limitControl->getLimitParam(),
            $sortControl->getSortParam()
        ]);

        if ($searchBar->hasBeenSent() && ! $searchBar->isValid()) {
            if ($searchBar->hasBeenSubmitted()) {
                $filter = QueryString::parse((string) $this->params);
            } else {
                $this->addControl($searchBar);
                $this->sendMultipartUpdate();
                return;
            }
        } else {
            $filter = $searchBar->getFilter();
        }

        $incidents->filter($filter);

        $this->addControl($paginationControl);
        $this->addControl($sortControl);
        $this->addControl($limitControl);
        $this->addControl($searchBar);

        $list = (new ItemList($incidents, new IncidentRenderer()))
            ->setItemLayoutClass(ItemLayout::class);

        $this->addContent($list);

        if (! $searchBar->hasBeenSubmitted() && $searchBar->hasBeenSent()) {
            $this->sendMultipartUpdate();
        }
    }
}
