@extends('layouts.app')

@section('page', 'list')

@section('extra-meta')
    <meta name="datatable-url" content="{{ ucroute('uccello.datatable', $domain, $module) }}">
    <meta name="datatable-columns" content='{!! json_encode($datatableColumns) !!}'>
@endsection

@section('content')
<div class="dataTable-container listview">

    @section('breadcrumb')
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-sm-6 col-xs-12">
                    <div class="breadcrumb pull-left">
                        {{-- Redirect to previous page. If there is not previous page, redirect to home page --}}
                        <a href="{{ URL::previous() !== URL::current() ? URL::previous() : ucroute('uccello.home', $domain, $module) }}" class="pull-left">
                            <i class="material-icons" data-toggle="tooltip" data-placement="top" title="{{ uctrans('button.return', $module) }}">chevron_left</i>
                        </a>

                        <ol class="breadcrumb filters pull-left">
                            @if ($admin_env)<li><a href="{{ ucroute('uccello.settings.dashboard', $domain) }}">{{ uctrans('breadcrumb.admin', $module) }}</a></li>@endif
                            <li><a href="{{ ucroute('uccello.list', $domain, $module) }}">{{ uctrans($module->name, $module) }}</a></li>
                            <li>
                                <select class="filter show-tick">
                                    @foreach ($filters as $filter)
                                    <option value="{{ $filter->id }}" @if($filter->id == $selectedFilterId)selected="selected"@endif>{{ uctrans($filter->name, $module) }}</option>
                                    @endforeach
                                </select>
                            </li>
                        </ol>

                        <a href="#" class="pull-right" data-config='{"actionType":"modal", "modal":"#addFilterModal"}'>
                            <i class="material-icons add-filter bg-green" data-toggle="tooltip" data-placement="top" title="{{ uctrans('button.add_filter', $module) }}">add</i>
                        </a>
                    </div>
                </div>

                <div class="action-buttons col-sm-6 col-xs-12">
                    <div class="btn-group m-l-10">
                        <button type="button" class="btn bg-primary icon-right waves-effect pull-right dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            {!! uctrans('filter.show_n_records', $module, ['number' => '<strong class="records-number">15</strong>']) !!}
                            <i class="material-icons">keyboard_arrow_down</i>
                        </button>
                        <ul id="items-number" class="dropdown-menu">
                            <li><a href="javascript:void(0);" class="waves-effect waves-block" data-number="15">15</a></li>
                            <li><a href="javascript:void(0);" class="waves-effect waves-block" data-number="30">30</a></li>
                            <li><a href="javascript:void(0);" class="waves-effect waves-block" data-number="50">50</a></li>
                            <li><a href="javascript:void(0);" class="waves-effect waves-block" data-number="100">100</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @show

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="min-height: 600px">
            <div class="card">
                <div class="body p-t-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover dataTable">
                            <thead>
                                <tr>
                                    <th class="select-column">&nbsp;</th>

                                    @foreach ($datatableColumns as $column)
                                    <th>
                                        {{ uctrans('field.'.$column['name'], $module) }}
                                        <?php
                                            // If a special template exists, use it. Else use the generic template
                                            $uitypeViewName = sprintf('uitypes.search.%s', $column[ 'uitype' ]);
                                            $uitypeFallbackView = 'uccello::modules.default.uitypes.search.text';
                                            $uitypeViewToInclude = uccello()->view($column[ 'package' ], $module, $uitypeViewName, $uitypeFallbackView);
                                        ?>
                                        @include($uitypeViewToInclude)
                                    </th>
                                    @endforeach

                                    <th class="actions-column">
                                        <a class="clear-search pull-left col-red" title="{{ uctrans('button.clear_search', $module) }}" data-toggle="tooltip" data-placement="top"><i class="material-icons">close</i></a>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <div class="paginator m-b-25">
            </div>
        </div>
    </div>

    @section('page-action-buttons')
        {{-- Create button --}}
        @if (Auth::user()->canCreate($domain, $module))
        <div id="page-action-buttons">
            <a href="{{ ucroute('uccello.edit', $domain, $module) }}" class="btn btn-success btn-circle-lg waves-effect waves-circle waves-float" title="{{ uctrans('button.new', $module) }}" data-toggle="tooltip" data-placement="top">
                <i class="material-icons">add</i>
            </a>
        </div>
        @endif
    @show

    {{-- Template to use in the table --}}
    <div class="template hide">
        @if (Auth::user()->canUpdate($domain, $module))
        <a href="{{ ucroute('uccello.edit', $domain, $module, ['id' => 'RECORD_ID']) }}" title="{{ uctrans('button.edit', $module) }}" class="edit-btn"><i class="material-icons">edit</i></a>
        @endif

        @if (Auth::user()->canDelete($domain, $module))
        <a href="{{ ucroute('uccello.delete', $domain, $module, ['id' => 'RECORD_ID']) }}" title="{{ uctrans('button.delete', $module) }}" class="delete-btn" data-config='{"actionType":"link","confirm":true,"dialog":{"title":"{{ uctrans('button.delete.confirm', $module) }}"}}'><i class="material-icons">delete</i></a>
        @endif
    </div>
</div>
@endsection

@section('extra-content')
    {{-- Add filter modal --}}
    @include("uccello::modules.default.list.modal.add-filter")
@endsection