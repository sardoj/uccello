@yield('before-navbar-header')

@section('navbar-header')
<div class="navbar-header navbar-fixed">
    <nav class="header">
        <div class="nav-wrapper default-bar">
            <ul class="left">
                <li>
                    <a href="#" class="sidenav-trigger" data-target="sidenav-menu" style="margin-left: 0">
                        <i class="material-icons right">menu</i>
                    </a>
                    <a href="javascript:void(0)" id="menu-size-switcher" class="hide-on-med-and-down" style="margin-left: 0">
                        @if (auth()->user()->getSettings('menu_mini', false))
                        <i class="material-icons right">menu</i>
                        @else
                        <i class="material-icons right">menu_open</i>
                        @endif
                    </a>
                </li>
            </ul>

            @section('brand-logo')
            <a class="brand-logo" href="/" style="padding: 7px; max-height: 50px">@section('logo'){{ Html::image(ucasset('images/logo-uccello-white.png'), null, ['style' => 'max-width: 150px;']) }}@show</a>
            @show

            {{-- Display current domain name and a link to open domains list --}}
            <ul class="right">
                <li>
                    <a href="javascript:void(0)" class="search-btn" style="margin-left: 0; margin-right: 0"><i class="material-icons">search</i></a>
                </li>
                @if (uccello()->useMultiDomains() && isset($domain))
                <li>
                    @if (config('uccello.domains.display_tree') !== false)
                    <a href="#domains-modal" class="modal-trigger show-on-large" style="margin-left: 0; margin-right: 0">
                        <span class="hide-on-small-only">{{ $domain->name }}</span>
                        <i class="material-icons right">arrow_drop_down</i>
                    </a>
                    @else
                    <a href="javascript:void(0)" class="show-on-large" style="margin-left: 0; margin-right: 0">
                        <span class="hide-on-small-only">{{ $domain->name }}</span>
                    </a>
                    @endif
                </li>
                @endif
            </ul>
        </div>

        <div class="nav-wrapper search-bar" style="display: none">
            <form action="{{ route('uccello.search', [ 'domain' => $domain ]) }}" novalidate>
                <div class="input-field">
                    <input id="search" type="search" name="q" required autocomplete="off">
                    <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                    <i class="material-icons">close</i>
                </div>
            </form>
        </div>
    </nav>
</div>
@show

@yield('after-navbar-header')

@section('navbar-top')
<header class="navbar-fixed navbar-top">
    <nav class="transparent z-depth-0">
        <div class="row">
            <div class="col s12 m9">
                @section('breadcrumb')&nbsp;@show
            </div>
            <div class="col s12 m3 hide-on-small-only">
                @section('top-action-buttons')&nbsp;@show
            </div>
        </div>
    </nav>
</header>
@show

@yield('after-navbar-top')

@section('domains-modal')
@if (config('uccello.domains.display_tree') !== false)
<div id="domains-modal" class="modal modal-fixed-footer">
    <div class="modal-content">
        <h5>
            <i class="material-icons left primary-text">device_hub</i>
            {{ uctrans('modal.domains.title', $module) }}
        </h4>
        <div class="row">
            <div class="input-field col s12 domain-search-bar">
                <input type="text" id="domain-name">
                <label for="domain-name">{{ uctrans('domain.search', $module) }}</label>
            </div>
        </div>
        <div id="domains-tree">
            {{-- Filled automaticaly --}}
        </div>
    </div>
    <div class="modal-footer">
        <a href="javascript:void(0)" class="btn-flat waves-effect modal-close">{{ uctrans('button.cancel', $module) }}</a>
    </div>
</div>
@endif
@show
