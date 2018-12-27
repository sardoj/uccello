<?php

namespace Uccello\Core\Support;

use Spatie\Menu\Laravel\Menu;
use Spatie\Menu\Laravel\Html;
use Uccello\Core\Models\Domain;
use Uccello\Core\Models\Module;

class MenuGenerator
{
    /**
     * Current domain
     *
     * @var \Uccello\Core\Models\Domain
     */
    protected $domain;

    /**
     * Current module
     *
     * @var \Uccello\Core\Models\Module
     */
    protected $module;

    /**
     * Main menu
     *
     * @var \Spatie\Menu\Laravel\Menu
     */
    protected $menu;

    /**
     * All names of modules added in the menu
     *
     * @var array
     */
    protected $menuAddedModules;

    /**
     * Get menu generated
     *
     * @return \Spatie\Menu\Laravel\Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Make the menu according to the environment (main or admin)
     *
     * @param Domain $domain
     * @param Module $module
     * @return \Uccello\Core\Support\MenuGenerator
     */
    public function makeMenu(Domain $domain, Module $module)
    {
        $this->domain = $domain;

        $this->module = $module;

        // Create menu
        $this->menu = Menu::new()
            ->withoutWrapperTag(); // Do not add <ul></ul>

        // Add links to menu
        $this->addLinksToMenu();

        return $this;
    }

    /**
     * Add all links to the menu
     *
     * @return void
     */
    protected function addLinksToMenu()
    {
        // Get the menu to display according to the environment (main or admin)
        $domainMenu = $this->getDomainMenuToDisplay();

        $this->menuAccessibleModules = [];

        // If a menu was created, use it
        if (!is_null($domainMenu)) {
            $this->addLinksToMenuFromDomainMenu($domainMenu);
        }
        // Else add links from the modules list
        else {
            $this->addLinksToMenuFromModulesList();
        }

        // If we are on a module not displayed in the menu, add it to the menu
        $this->addActiveModuleIfNotInMenu();
    }

    /**
     * If a menu was created, use it and add its links in the menu
     *
     * @param \Uccello\Core\Models\Menu $domainMenu
     * @return void
     */
    protected function addLinksToMenuFromDomainMenu($domainMenu)
    {
        if (empty($domainMenu->data)) {
            return;
        }

        foreach ($domainMenu->data as $menuLink) {
            $this->addLink($this->menu, $menuLink);
        }

        // Add links added after the creation of the menu
        $this->addLinksAddedAfterMenuCreation();
    }

    /**
     * If no menu was created we add all links available in the activated modules
     *
     * @return void
     */
    protected function addLinksToMenuFromModulesList()
    {
        $modules = $this->getModulesVisibleInMenu();

        foreach ($modules as $module) {
            foreach ($module->menuLinks as $menuLink) {
                $menuLink->type = 'module';
                $menuLink->module = $module->name;
                $this->addLink($this->menu, $menuLink);
            }
        }
    }

    /**
     * If we are on a module not displayed in the menu, add it to the menu
     *
     * @return void
     */
    protected function addActiveModuleIfNotInMenu()
    {
        if (!in_array($this->module->name, $this->menuAccessibleModules)) {
            $menuLink = new \StdClass;
            $menuLink->label = $this->module->name;
            $menuLink->icon = $this->module->icon ?? 'extension';
            $menuLink->type = 'module';
            $menuLink->module = $this->module->name; // Current module name
            $menuLink->route = request()->route()->getName(); // Current route
            $menuLink->url = 'javascript:void(0)'; // No link
            $this->addLink($this->menu, $menuLink, false, false);
        }
    }

    /**
     * Add to the menu, the links added after the creation of the menu
     * (e.g. new modules or modules activated after the creation)
     */
    protected function addLinksAddedAfterMenuCreation()
    {
        $modules = $this->getModulesVisibleInMenu();

        foreach ($modules as $module) {
            if (!in_array($module->name, $this->menuAccessibleModules)) {
                foreach ($module->menuLinks as $menuLink) {
                    $menuLink->type = 'module';
                    $menuLink->module = $module->name;
                    $this->addLink($this->menu, $menuLink);
                }
            }
        }
    }

    /**
     * Recursive function to add a link to the menu with all its children
     *
     * @param \Spatie\Menu\Laravel\Menu $menu
     * @param \StdClass $menuLink
     * @param boolean $isInSubMenu
     * @param boolean $checkCapacity
     * @return void
     */
    protected function addLink($menu, $menuLink, $isInSubMenu = false, $checkCapacity = true)
    {
        //TODO: Check needed capacity
        if ($menuLink->type === 'module') {
            $module = ucmodule($menuLink->module);
            if (!$module->isActiveOnDomain($this->domain, $module)) {
                return;
            }
            if ($checkCapacity && !auth()->user()->canRetrieve($this->domain, $module)) {
                return;
            }
        }

        if (!empty($menuLink->module)) {
            if (!in_array($menuLink->module, $this->menuAccessibleModules)) {
                $this->menuAccessibleModules[] = $menuLink->module;
            }
        }

        // Url
        if (!empty($menuLink->url)) { // Prioritary to be compatible with addActiveModuleIfNotInMenu()
            $url = $menuLink->url;
        } elseif (!empty($menuLink->route) && !empty($menuLink->module)) {
            $module = ucmodule($menuLink->module);
            $url = ucroute($menuLink->route, $this->domain, $module);
        } else {
            $url = 'javascript:void(0)';
        }

        // Label
        $label = $menuLink->type === 'module' ? uctrans($menuLink->label, ucmodule($menuLink->module)) : $menuLink->label;

        // Icon
        if ($menuLink->type === 'folder') {
            $fallbackIcon = 'folder';
        } elseif ($menuLink->type === 'link') {
            $fallbackIcon = 'link';
        } else {
            $fallbackIcon = 'extension';
        }

        $icon = $menuLink->icon ?? $fallbackIcon;

        // Is active. If the current route is in the menu, compare also the routes
        if ($menuLink->type === 'module') {
            if ($this->isCurrentRouteInMenu()) {
                $isActive = $this->module->id === $module->id && request()->route()->getName() === $menuLink->route;
            } else {
                $isActive = $this->module->id === $module->id;
            }
        } else {
            $isActive = false;
        }

        $class = '';
        if (!empty($menuLink->children)) {
            $class = 'menu-toggle';
        }

        if ($isActive && $isInSubMenu) {
            $class .= ' toggled';
        }

        // Link html
        $link = Html::raw(
            '<a href="'. $url .'" class="'. $class .'">'.
                (!$isInSubMenu ? '<i class="material-icons">'. $icon .'</i>' : '').
                (!$isInSubMenu ? '<span>'. $label .'</span>' : $label).
            '</a>'
        )->setActive($isActive);


        // Add children
        if (!empty($menuLink->children)) {

            // Make a sub menu
            $subMenu = Menu::new()
                ->addClass('ml-menu');

            // Add all links in the sub menu
            foreach ($menuLink->children as $subMenuLink) {
                $this->addLink($subMenu, $subMenuLink, true); // Recursive
            }

            // Add sub menu
            if ($subMenu->count() > 0) {
                $menu->submenu($link, $subMenu);
            }

        } else {
            // Add link to menu
            $menu->add($link);
        }
    }

    /**
     * Return the menu to display according to the environment (main or admin)
     *
     * @return \Uccello\Core\Models\Menu
     */
    protected function getDomainMenuToDisplay()
    {
        if ($this->isAdminEnv()) {
            $menuToDisplay = $this->domain->adminMenu;
        } else {
            $menuToDisplay = $this->domain->mainMenu;
        }

        return $menuToDisplay;
    }

    /**
     * Return all modules visible in the menu according to the environment (main or admin)
     *
     * @return void
     */
    protected function getModulesVisibleInMenu()
    {
        // Detect what sort of link to add in the menu (admin or not admin) and load the related modules
        if ($this->isAdminEnv()) {
            $modules = $this->domain->adminModules;
        } else {
            $modules = $this->domain->notAdminModules;
        }

        return $modules;
    }

    /**
     * Check if we are in the admin environment
     *
     * @return boolean
     */
    protected function isAdminEnv()
    {
        return $this->module->isAdminModule();
    }

    /**
     * Check if the current route is present in the menu
     *
     * @return boolean
     */
    protected function isCurrentRouteInMenu()
    {
        $currentRoute = request()->route()->getName();

        return $this->isRouteInMenu($currentRoute);
    }

    /**
     * Check if a route is present in the menu
     *
     * @param string $route
     * @return boolean
     */
    protected function isRouteInMenu($route)
    {
        $found = false;

        $modules = $this->getModulesVisibleInMenu();
        foreach ($modules as $module) {
            foreach ($module->menuLinks as $link) {
                if ($link->route === $route) {
                    $found = true;
                    break 2;
                }
            }
        }

        return $found;
    }
}