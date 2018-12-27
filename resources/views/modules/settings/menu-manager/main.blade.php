@extends('layouts.app')

@section('page', 'menu-manager')

@section('extra-meta')
<meta name="main-menu-structure" content='{!! json_encode($mainMenuJson ?? '') !!}'>
<meta name="admin-menu-structure" content='{!! json_encode($adminMenuJson ?? '') !!}'>
<meta name="save-url" content="{{ ucroute('uccello.settings.menu.store', $domain) }}">
@endsection

@section('content')

    @section('breadcrumb')
    <div class="row">
        <div class="col-sm-4 col-xs-12">
            <div class="breadcrumb pull-left">
                {{-- Redirect to previous page. If there is not previous page, redirect to list view --}}
                <a href="{{ URL::previous() !== URL::current() ? URL::previous() : ucroute('uccello.list', $domain, $module) }}" class="pull-left">
                    <i class="material-icons" data-toggle="tooltip" data-placement="top" title="{{ uctrans('button.return', $module) }}">chevron_left</i>
                </a>

                <ol class="breadcrumb pull-left">
                    @if ($admin_env)<li><a href="{{ ucroute('uccello.settings.dashboard', $domain) }}">{{ uctrans('breadcrumb.admin', $module) }}</a></li>@endif
                    <li class="active">{{ uctrans('menu.manager', $module) }}</li>
                </ol>
            </div>
        </div>
    </div>
    @show

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card block">
                <div class="header">
                    <h2>
                        <div class="block-label-with-icon">
                            <i class="material-icons">menu</i>
                            <span>{{ uctrans('menu.manager', $module) }}</span>
                        </div>
                        <small>{{ uctrans('menu.manager.description', $module) }}</small>
                    </h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col-md-5 col-lg-4 col-md-offset-2 col-lg-offset-3">
                            {{-- Field --}}
                            <div class="form-switch text-center">
                                <div class="switch" style="padding-top: 10px; padding-bottom: 5px;">
                                    <label class="switch-label">
                                        <strong>{{ uctrans('menu.type.main', $module) }}</strong>
                                        <input type="checkbox" name="menu-switcher" id="menu-switcher" value="admin" />
                                        <span class="lever switch-col-primary"></span>
                                        <strong class="col-primary">{{ uctrans('menu.type.admin', $module) }}</strong>
                                    </label>
                                </div>
                            </div>

                            <div class="menu-manager menu-main dd" data-type="main">
                                <ol class="dd-list">
                                    @if (empty($menu->data))
                                        @foreach ($domain->notAdminModules as $_module)
                                            @foreach ($_module->menuLinks as $link)
                                            @include('uccello::modules.settings.menu-manager.item')
                                            @endforeach
                                        @endforeach
                                    @endif
                                </ol>
                            </div>

                            <div class="menu-manager menu-admin dd" data-type="admin" style="display: none">
                                <ol class="dd-list">
                                    @if (empty($menu->data))
                                        @foreach ($domain->adminModules as $_module)
                                            @foreach ($_module->menuLinks as $link)
                                            @include('uccello::modules.settings.menu-manager.item')
                                            @endforeach
                                        @endforeach
                                    @endif
                                </ol>
                            </div>
                        </div>

                        <div class="col-md-3 col-lg-2 p-t-45">
                            <a class="waves-effect waves-block btn icon-right bg-green m-b-10" data-config='{"actionType":"modal","modal":"#addGroupModal"}'>
                                <i class="material-icons">folder</i>
                                {{ uctrans('menu.button.add_group', $module) }}
                            </a>

                            {{-- <a class="waves-effect waves-block btn icon-right bg-red m-b-10" data-config='{"actionType":"modal","modal":"#addRouteLinkModal"}'>
                                <i class="material-icons">link</i>
                                {{ uctrans('menu.button.add_route_link', $module) }}
                            </a> --}}

                            <a class="waves-effect waves-block btn icon-right bg-primary m-b-10" data-config='{"actionType":"modal","modal":"#addLinkModal"}'>
                                <i class="material-icons">link</i>
                                {{ uctrans('menu.button.add_link', $module) }}
                            </a>

                            {{-- <a href="{{ ucroute('uccello.settings.menu.store', $domain) }}"
                                class="save-menu waves-effect waves-block btn icon-right bg-orange">
                                <i class="material-icons">save</i>
                                {{ uctrans('menu.button.save', $module) }}
                            </a> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra-content')

    @include('uccello::modules.settings.menu-manager.modal.group')
    @include('uccello::modules.settings.menu-manager.modal.route-link')
    @include('uccello::modules.settings.menu-manager.modal.link')
@endsection

@section('extra-script')
    {{ Html::script(ucasset('js/settings/autoloader.js')) }}
@endsection