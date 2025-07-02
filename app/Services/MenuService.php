<?php

namespace App\Services;

class MenuService
{
    protected $moduleService;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->moduleService = new ModuleService();
    }

    public function getMenus()
    {
        $isChatbotModulePublished = $this->moduleService->isModulePublished('chatbot');

        $menuItems = [
            [
                'name' => 'Dashboard',
                'url' => route('dashboard'),
                'icon' => 'bi bi-house',
                'active' => request()->routeIs('dashboard'),
                'permission' => 1
            ],
            [
                'name' => 'Manager Functions',
                'icon' => 'bi bi-layout-three-columns',
                'active' => request()->routeIs('users.*') || request()->routeIs('roles.*'),
                'permission' => auth()->user()->can('view users') || auth()->user()->can('view roles') || auth()->user()->can('view modules') || auth()->user()->can('view sponsors') || auth()->user()->can('view posts'),
                'children' => [
                    [
                        'name' => 'Users',
                        'url' => route('users.index'),
                        'active' => request()->routeIs('users.*'),
                        'permission' => 'view users',
                    ],
                    [
                        'name' => 'Roles',
                        'url' => route('roles.index'),
                        'active' => request()->routeIs('roles.*'),
                        'permission' => 'view roles',
                    ],
                    [
                        'name' => 'Modules',
                        'url' => route('modules.index'),
                        'active' => request()->routeIs('modules.*'),
                        'permission' => 'view modules',
                    ],
                    [
                        'name' => 'Sponsors',
                        'url' => route('sponsors.index'),
                        'active' => request()->routeIs('sponsors.*'),
                        'permission' => 'view sponsors',
                    ],
                    [
                        'name' => 'Posts',
                        'url' => route('posts.index'),
                        'active' => request()->routeIs('posts.*'),
                        'permission' => 'view posts',
                    ],
                ],
            ],
            [
                'name' => 'System',
                'icon' => 'bi bi-sliders2',
                'active' => request()->routeIs('system.*'),
                'permission' => auth()->user()->can('view activity logs') || auth()->user()->can('view log storage') || auth()->user()->can('manage system') || auth()->user()->can('manage telescope') || auth()->user()->can('view backups'),
                'children' => [
                    [
                        'name' => 'Activity Logs',
                        'url' => route('activity-logs.index'),
                        'active' => request()->routeIs('activity-logs.*'),
                        'permission' => 'view activity logs',
                    ],
                    [
                        'name' => 'Storage Report',
                        'url' => route('storage.index'),
                        'active' => request()->routeIs('storage.*'),
                        'permission' => 'view log storage',
                    ],
                    [
                        'name' => 'System Info',
                        'url' => route('system.index'),
                        'active' => request()->routeIs('system.*'),
                        'permission' => 'manage system',
                    ],
                    [
                        'name' => 'Telescope',
                        'url' => '/telescope',
                        'active' => request('/telescope'),
                        'permission' => 'manage telescope',
                    ],
                    [
                        'name' => 'Backups',
                        'url' => route('backups.index'),
                        'active' => request()->routeIs('backups.*'),
                        'permission' => 'view backups',
                    ],
                ],
            ],
        ];
        if (isset($isChatbotModulePublished) && $isChatbotModulePublished) {
            array_push($menuItems, [
                'name' => 'Chatbot',
                'icon' => 'bi bi-robot',
                'active' => request()->routeIs('chatbot.*') || request()->routeIs('chatbot.*'),
                'permission' => 1,
                'children' => [
                    [
                        'name' => 'Chatbot introduction',
                        'url' => route('chatbot.index'),
                        'active' => request()->routeIs('chatbot.index'),
                        'permission' => '',
                    ],
                    [
                        'name' => 'Run Demo',
                        'url' => route('chatbot.demo'),
                        'active' => request()->routeIs('chatbot.demo'),
                        'permission' => '',
                    ],
                    [
                        'name' => 'Chatbot Settings',
                        'url' => route('chatbot.setting.index'),
                        'active' => request()->routeIs('chatbot.setting.index'),
                        'permission' => '',
                    ],
                ],
            ]);
        }
        return $menuItems;
    }
}
