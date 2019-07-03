<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\Group;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class GroupController extends ModuleController
{
    protected $namespace = 'A17\Twill';

    protected $moduleName = 'groups';

    protected $indexWith = ['medias'];

    protected $defaultOrders = ['name' => 'asc'];

    protected $defaultFilters = [
        'search' => 'search',
    ];

    protected $titleColumnKey = 'name';

    protected $indexOptions = [
        'permalink' => false,
    ];

    public function __construct(Application $app, Request $request)
    {
        parent::__construct($app, $request);
        $this->middleware('can:edit-user-groups');
    }

    protected function indexData($request)
    {
        return [
            'defaultFilterSlug' => 'published',
            'primary_navigation' => [
                'users' => [
                    'title' => 'Users',
                    'module' => true,
                    'can' => 'edit-users',
                ],
                'groups' => [
                    'title' => 'Groups',
                    'module' => true,
                    'active' => true,
                    'can' => 'edit-user-groups',
                ],
                'roles' => [
                    'title' => 'Roles',
                    'module' => true,
                    'can' => 'edit-user-role',
                ],
            ],
            'customPublishedLabel' => 'Enabled',
            'customDraftLabel' => 'Disabled',
        ];
    }

    protected function getIndexOption($option, $item = null)
    {
        if (in_array($option, ['publish', 'bulkEdit', 'create'])) {
            return auth('twill_users')->user()->can('edit-user-groups');
        }

        return parent::getIndexOption($option);
    }

    protected function formData($request)
    {
        return [
            'primary_navigation' => [
                'users' => [
                    'title' => 'Users',
                    'module' => true,
                    'can' => 'edit-user',
                ],
                'groups' => [
                    'title' => 'Groups',
                    'module' => true,
                    'active' => true,
                    'can' => 'edit-user-groups',
                ],
                'roles' => [
                    'title' => 'Roles',
                    'module' => true,
                    'can' => 'edit-user-roles',
                ],
            ],
            'customPublishedLabel' => 'Enabled',
            'customDraftLabel' => 'Disabled',
        ];
    }

    protected function indexItemData($item)
    {
        $canEdit = auth('twill_users')->user()->can('edit-user-groups') && ($item->canEdit ?? true);

        return ['edit' => $canEdit ? $this->getModuleRoute($item->id, 'edit') : null];
    }

    protected function getRequestFilters()
    {
        return json_decode($this->request->get('filter'), true) ?? ['status' => 'published'];
    }

    protected function getIndexItems($scopes = [], $forcePagination = false)
    {
        $items = parent::getIndexItems($scopes);
        return $items->prepend(Group::getEveryoneGroup());
    }

    protected function getBrowserItems($scopes = [])
    {
        // Exclude everyone group from browsers

        return parent::getBrowserItems($scopes)->filter(function ($item) {
            return $item->name !== Group::getEveryoneGroup()->name && $item->id != null;
        })->values();
    }

}