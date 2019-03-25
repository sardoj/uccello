<?php

namespace Uccello\Core\Http\Controllers\Core;

use Schema;
use DB;
use Illuminate\Http\Request;
use Uccello\Core\Models\Domain;
use Uccello\Core\Models\Module;
use Uccello\Core\Facades\Uccello;
use Uccello\Core\Models\Relatedlist;
use Uccello\Core\Models\Filter;

class ListController extends Controller
{
    protected $viewName = 'list.main';

    /**
     * Check user permissions
     */
    protected function checkPermissions()
    {
        $this->middleware('uccello.permissions:retrieve');
    }

    /**
     * @inheritDoc
     */
    public function process(?Domain $domain, Module $module, Request $request)
    {
        // Pre-process
        $this->preProcess($domain, $module, $request);

        // Selected filter
        if ($request->input('filter')) {
            $selectedFilterId = $request->input('filter');
            $selectedFilter = Filter::find($selectedFilterId);
        } else {
            $selectedFilter = $module->filters()->where('type', 'list')->first();
            $selectedFilterId = $selectedFilter->id;
        }

        // Get datatable columns
        $datatableColumns = Uccello::getDatatableColumns($module, $selectedFilterId);

        // Get filters
        $filters = Filter::where('module_id', $module->id)
            ->where('type', 'list')
            ->get();

        // Order by
        $filterOrderBy = (array) $selectedFilter->order_by;

        return $this->autoView(compact('datatableColumns', 'filters', 'selectedFilter', 'filterOrderBy'));
    }

    /**
     * Display a listing of the resources.
     * The result is formated differently if it is a classic query or one requested by datatable.
     * Filter on domain if domain_id column exists.
     * @param  \Uccello\Core\Models\Domain|null $domain
     * @param  \Uccello\Core\Models\Module $module
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function processForDatatable(?Domain $domain, Module $module, Request $request)
    {
        // If we don't use multi domains, find the first one
        if (!uccello()->useMultiDomains()) {
            $domain = Domain::first();
        }

        // Get data formated for Datatable
        $result = $this->getResultForDatatable($domain, $module, $request);


        return $result;
    }

    /**
     * Display a listing of the resources.
     * The result is formated differently if it is a classic query or one requested by datatable.
     * Filter on domain if domain_id column exists.
     * @param  \Uccello\Core\Models\Domain|null $domain
     * @param  \Uccello\Core\Models\Module $module
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function processForDatatableConfig(?Domain $domain, Module $module, Request $request)
    {
        // If we don't use multi domains, find the first one
        if (!uccello()->useMultiDomains()) {
            $domain = Domain::first();
        }

        // Get filter type
        $filterType = $request->get('filter_type', 'list');

        // Get selected filter id
        $filterId = $request->get('filter');
        $filter = Filter::find($filterId);

        // Get data formated for Datatable
        return [
            'columns' => uccello()->getDatatableColumns($module, $filterId, $filterType),
            'filter' => $filter
        ];
    }

    /**
     * Autocomplete a listing of the resources.
     * The result is formated differently if it is a classic query or one requested by datatable.
     * Filter on domain if domain_id column exists.
     * @param  \Uccello\Core\Models\Domain|null $domain
     * @param  \Uccello\Core\Models\Module $module
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function processForAutocomplete(?Domain $domain, Module $module, Request $request)
    {
        // If we don't use multi domains, find the first one
        if (!uccello()->useMultiDomains()) {
            $domain = Domain::first();
        }

        // Query
        $q = $request->get('q');

        // Model class
        $modelClass = $module->model_class;

        if ($q) {
            DB::statement("SET SESSION sql_mode = ''");
            $query = $modelClass::search($q);
        } else {
            $query = $modelClass::query();
        }

        return $query->paginate(10);
    }

    public function getContent(?Domain $domain, Module $module, Request $request)
    {
        $length = (int)$request->get('length') ?? env('UCCELLO_ITEMS_PER_PAGE', 15);
        $order = $request->get('order');
        $columns = $request->get('columns');

        // Pre-process
        $this->preProcess($domain, $module, $request);

        // Get model model class
        $modelClass = $module->model_class;

        // Check if the class exists
        if (!class_exists($modelClass)) {
            return false;
        }

        // Filter on domain if column exists
        if (Schema::hasColumn((new $modelClass)->getTable(), 'domain_id')) {
            $query = $modelClass::where('domain_id', $domain->id);
        } else {
            $query = $modelClass::query();
        }

        // Search by column
        foreach ($columns as $fieldName => $column) {
            if (!empty($column[ "search" ])) {
                $searchValue = is_array($column[ "search" ]) ? implode(',', $column[ "search" ]) : $column[ "search" ];
            } else {
                $searchValue = null;
            }

            // Get field by name and search by field column
            $field = $module->getField($fieldName);
            if (isset($searchValue) && !is_null($field)) {
                $query = $field->uitype->addConditionToSearchQuery($query, $field, $searchValue);
            }
        }

        // Order results
        if (!empty($order)) {
            foreach ($order as $column => $value) {
                $query = $query->orderBy($column, $value);
            }
        }

        // Limit the number maximum of items per page
        $maxItemsPerPage = env('UCCELLO_MAX_ITEMS_PER_PAGE', 100);
        if ($length > $maxItemsPerPage) {
            $length = $maxItemsPerPage;
        }

        // Paginate results
        $records = $query->paginate($length);

        $records->getCollection()->transform(function ($record) use ($module) {

            foreach ($module->fields as $field) {

                // If a special template exists, use it. Else use the generic template
                $uitypeViewName = sprintf('uitypes.list.%s', $field->uitype->name);
                $uitypeFallbackView = 'uccello::modules.default.uitypes.list.text';
                $uitypeViewToInclude = uccello()->view($field->uitype->package, $module, $uitypeViewName, $uitypeFallbackView);
                $record->{$field->name} = view()->make($uitypeViewToInclude, compact('domain', 'module', 'record', 'field'))->render();
            }

            return $record;
        });

        return $records;
    }

    /**
     * Save list filter into database
     *
     * @param \Uccello\Core\Models\Domain|null $domain
     * @param \Uccello\Core\Models\Module $module
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function saveFilter(?Domain $domain, Module $module, Request $request)
    {
        $saveOrder = $request->input('save_order');
        $savePageLength = $request->input('save_page_length');

        // Optional data
        $data = [];
        if ($savePageLength) {
            $data["length"] = $request->input('page_length');
        }

        $filter = Filter::firstOrNew([
            'domain_id' => $domain->id,
            'module_id' => $module->id,
            'user_id' => auth()->id(),
            'name' => $request->input('name'),
            'type' => $request->input('type')
        ]);
        $filter->columns = $request->input('columns');
        $filter->conditions = $request->input('conditions') ?? null;
        $filter->order_by = $saveOrder ? $request->input('order') : null;
        $filter->is_default = $request->input('default');
        $filter->is_public = $request->input('public');
        $filter->data = !empty($data) ? $data : null;
        $filter->save();

        return $filter;
    }

    /**
     * Retrieve a filter by its id and delete it
     *
     * @param \Uccello\Core\Models\Domain|null $domain
     * @param \Uccello\Core\Models\Module $module
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteFilter(?Domain $domain, Module $module, Request $request)
    {
        // Retrieve filter by id
        $filterId = $request->input('id');
        $filter = Filter::find($filterId);

        if ($filter) {
            if ($filter->readOnly) {
                // Response
                $success = false;
                $message = uctrans('error.filter.read.only', $module);
            } else {
                // Delete
                $filter->delete();

                // Response
                $success = true;
                $message = uctrans('success.filter.deleted', $module);
            }
        } else {
            // Response
            $success = false;
            $message = uctrans('error.filter.not.found', $module);
        }

        return [
            'success' => $success,
            'message' => $message
        ];
    }

    /**
     * Get result formatted for Datatable
     *
     * @param  \Uccello\Core\Models\Domain $domain
     * @param  \Uccello\Core\Models\Module $module
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    protected function getResultForDatatable(Domain $domain, Module $module, Request $request)
    {
        $draw = (int)$request->get('draw');
        $start = (int)$request->get('start');
        $length = (int)$request->get('length');
        $order = $request->get('order');
        $columns = $request->get('columns');
        $recordId = $request->get('id');
        $relatedListId = $request->get('relatedlist');
        $action = $request->get('action');

        // Get model model class
        $modelClass = $module->model_class;

        // If the class exists, make the query
        if (class_exists($modelClass)) {

            // Filter on domain if column exists
            if (Schema::hasColumn((new $modelClass)->getTable(), 'domain_id')) {
                // Count all results
                $total = $modelClass::where('domain_id', $domain->id)->count();

                // Paginate results
                $query = $modelClass::where('domain_id', $domain->id);
            } else {
                // Count all results
                $total = $modelClass::count();

                // Paginate results
                $query = $modelClass::query();
            }

            // Search by column
            foreach ($columns as $column) {
                $fieldName = $column[ "data" ];
                $searchValue = $column[ "search" ][ "value" ];

                // Get field by name and search by field column
                $field = $module->getField($fieldName);
                if (isset($searchValue) && !is_null($field)) {
                    $query = $field->uitype->addConditionToSearchQuery($query, $field, $searchValue);
                }
            }

            // Count filtered results
            $totalFiltered = $query->count();

            $initialQuery = $query;
            $query = $query->skip($start)->take($length);

            // Order results
            foreach ($order as $orderInfo) {
                $columnIndex = (int)$orderInfo[ "column" ];
                $column = $columns[ $columnIndex ];
                $fieldName = $column[ "data" ];

                // Get field by name and order by field column
                $field = $module->getField($fieldName);
                if (!is_null($field)) {
                    $query = $query->orderBy($field->column, $orderInfo[ "dir" ]);
                }
            }

            // If the query is for a related list, add conditions
            if ($relatedListId && $action !== 'select') {
                // Get related list
                $relatedList = Relatedlist::find($relatedListId);

                if ($relatedList && $relatedList->method) {
                    // Related list method
                    $method = $relatedList->method;
                    $countMethod = $method.'Count';

                    // Update query
                    $model = new $modelClass;
                    $records = $model->$method($relatedList, $recordId, $query, $start, $length);

                    // Count all results
                    $total = $model->$countMethod($relatedList, $recordId);
                    $totalFiltered = $total;
                }
            }
            elseif ($relatedListId && $action === 'select') {
                // Get related list
                $relatedList = Relatedlist::find($relatedListId);

                if ($relatedList && $relatedList->method) {
                    // Related list method
                    $method = $relatedList->method;
                    $recordIdsMethod = $method . 'RecordIds';

                    // Get related records ids
                    $model = new $modelClass;
                    $filteredRecordIds = $model->$recordIdsMethod($relatedList, $recordId);

                    // Add the record id itself to be filtered
                    if ($relatedList->related_module_id === $module->id && !empty($recordId) && !$filteredRecordIds->contains($recordId)) {
                        $filteredRecordIds[] = (int)$recordId;
                    }

                    // Make the query
                    $records = $query->whereNotIn($model->getKeyName(), $filteredRecordIds)->get();

                    // Count all results
                    $total = $initialQuery->whereNotIn($model->getKeyName(), $filteredRecordIds)->count();
                    $totalFiltered = $total;
                }
            }
            else {
                // Make the query
                $records = $query->get();
            }

            foreach ($records as &$record) {
                foreach ($module->fields as $field) {
                    // $displayedValue = $field->uitype->getFormattedValueToDisplay($field, $record);

                    // if ($displayedValue !== $record->{$field->column}) {
                    //     $record->{$field->name} = $displayedValue;
                    // }

                    // If a special template exists, use it. Else use the generic template
                    $uitypeViewName = sprintf('uitypes.list.%s', $field->uitype->name);
                    $uitypeFallbackView = 'uccello::modules.default.uitypes.list.text';
                    $uitypeViewToInclude = uccello()->view($field->uitype->package, $module, $uitypeViewName, $uitypeFallbackView);
                    $record->{$field->name} = view()->make($uitypeViewToInclude, compact('domain', 'module', 'record', 'field'))->render();
                }
            }

            $data = $records;

        } else {
            $data = [ ];
            $total = 0;
            $totalFiltered = 0;
        }

        return [
            "data" => $data->toArray(),
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $totalFiltered,
        ];
    }
}
