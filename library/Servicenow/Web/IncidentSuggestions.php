<?php

namespace Icinga\Module\Servicenow\Web;

use Icinga\Module\Servicenow\Model\Incident;

use ipl\Sql\Connection;
use ipl\Stdlib\Filter;
use ipl\Web\Control\SearchBar\Suggestions;

class IncidentSuggestions extends Suggestions
{
    /** @var Connection */
    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    protected function createQuickSearchFilter($searchTerm)
    {
        $query = Incident::on($this->db);

        $filter = Filter::any();
        foreach ($query->getModel()->getSearchColumns() as $searchColumn) {
            $filter->add(Filter::like(
                $query->getResolver()->qualifyColumn($searchColumn, $query->getModel()->getTableName()),
                $searchTerm
            ));
        }

        return $filter;
    }

    protected function fetchValueSuggestions($column, $searchTerm, Filter\Chain $searchFilter)
    {
        $query = Incident::on($this->db);

        $query->columns($column);
        $query->filter(Filter::like($column, $searchTerm));

        foreach ($query as $row) {
            yield $row->$column;
        }
    }

    protected function fetchColumnSuggestions($searchTerm)
    {
        $query = Incident::on($this->db);

        foreach ($query->getResolver()->getColumnDefinitions($query->getModel()) as $name => $definition) {
            yield $name => $definition->getLabel();
        }
    }
}
