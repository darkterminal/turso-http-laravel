<?php

namespace Turso\Http\Laravel\Database;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Query\Processors\SQLiteProcessor;

class LibSQLQueryProcessor extends SQLiteProcessor
{
    /**
     * Process the list of tables.
     *
     * @param  mixed  $results
     */
    public function processTables($results): array
    {
        return $this->useAssoc($results);
    }

    public function processSelect(Builder $query, $results)
    {
        $results = $this->useAssoc($results);
        return $results;
    }

    private function useAssoc(array $results)
    {
        $data = [];
        $columns = array_map(function ($col) {
            return $col['name'];
        }, $results['cols']);

        $values = array_map(function ($vals) {
            $arr_vals = [];
            $i = 0;
            foreach ($vals as $val) {
                if ($val['type'] === "null")
                    $val['value'] = null;
                $arr_vals[] = $val['value'];
                $i++;
            }
            return $arr_vals;
        }, $results['rows']);

        foreach ($values as $value) {
            $data[] = array_combine($columns, $value);
        }

        return $data;
    }
}
