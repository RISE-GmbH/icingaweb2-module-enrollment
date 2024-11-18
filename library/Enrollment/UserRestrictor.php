<?php

/* originally from Icinga Web 2 IcingaDB Module | (c) 2020 Icinga GmbH | GPLv2 */
/* generated by icingaweb2-module-scaffoldbuilder | GPLv2+ */

namespace Icinga\Module\Enrollment;

use Icinga\Authentication\Auth;
use Icinga\Exception\ConfigurationError;
use ipl\Orm\Query;
use ipl\Orm\UnionQuery;
use ipl\Stdlib\Filter;
use ipl\Web\Filter\QueryString;

class UserRestrictor{
    protected $auth;

    /**
     * @return Auth
     */
    public function getAuth()
    {
        return Auth::getInstance();
    }

    public function applyRestrictions(Query $query)
    {
        if ($this->getAuth()->getUser()->isUnrestricted()) {
            return;
        }

        if ($query instanceof UnionQuery) {
            $queries = $query->getUnions();
        } else {
            $queries = [$query];
        }


        foreach ($queries as $query) {
            $relations = [$query->getModel()->getTableName()];
            foreach ($query->getWith() as $relationPath => $relation) {
                $relations[$relationPath] = $relation->getTarget()->getTableName();
            }

            $queryFilter = Filter::any();
            foreach ($this->getAuth()->getUser()->getRoles() as $role) {
                $roleFilter = Filter::all();


                if (($restriction = $role->getRestrictions('enrollment/filter/users'))) {
                    $roleFilter->add($this->parseRestriction($restriction, 'enrollment/filter/users'));
                }

                if (! $roleFilter->isEmpty()) {
                    $queryFilter->add($roleFilter);
                }
            }

            $query->filter($queryFilter);
        }
    }

    public function parseRestriction(string $queryString, string $restriction): Filter\Rule
    {
        $allowedColumns = [
            'name',
        ];

        return QueryString::fromString($queryString)
            ->on(
                QueryString::ON_CONDITION,
                function (Filter\Condition $condition) use (
                    $restriction,
                    $queryString,
                    $allowedColumns
                ) {
                    foreach ($allowedColumns as $column) {
                        if (is_callable($column)) {
                            if ($column($condition->getColumn())) {
                                return;
                            }
                        } elseif ($column === $condition->getColumn()) {
                            return;
                        }
                    }

                    throw new ConfigurationError(
                        t(
                            'Cannot apply restriction %s using the filter %s.'
                            . ' You can only use the following columns: %s'
                        ),
                        $restriction,
                        $queryString,
                        join(
                            ', ',
                            array_map(
                                function ($k, $v) {
                                    return is_string($k) ? $k : $v;
                                },
                                array_keys($allowedColumns),
                                $allowedColumns
                            )
                        )
                    );
                }
            )->parse();
    }

}