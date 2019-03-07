@extends('twill::layouts.form', [
    'contentFieldsetLabel' => 'Account',
    'editModalTitle' => 'Edit user name'
])

@section('contentFields')
    @formField('input', [
        'name' => 'email',
        'label' => 'Email'
    ])

    @can('edit-user-role')
        @unless($item->is_superadmin)
            @formField('select', [
                'name' => "role_id",
                'label' => "Role",
                'options' => $roleList,
                'placeholder' => 'Select a role'
            ])
        @endunless
    @endcan

    @if(config('twill.enabled.users-image'))
        @formField('medias', [
            'name' => 'profile',
            'label' => 'Profile image'
        ])
    @endif
    @if(config('twill.enabled.users-description'))
        @formField('input', [
            'name' => 'title',
            'label' => 'Title',
            'maxlength' => 250
        ])
        @formField('input', [
            'name' => 'description',
            'rows' => 4,
            'type' => 'textarea',
            'label' => 'Description'
        ])
    @endif

    @unless($item->is_superadmin)
      @formField('browser', [
        'moduleName' => 'groups',
        'name' => 'groups',
        'label' => 'Groups',
        'sortable' => false,
        'max' => 100
      ])
    @endunless
@stop

@can('edit-users')
  @unless($item->is_superadmin)
    @section('fieldsets')
        @foreach($permission_modules as $module_name => $module_items)
            <a17-fieldset title='{{ ucfirst($module_name) . " Permissions"}}' id='{{ $module_name }}'>
                <h2>{{ ucfirst($module_name) .' permission' }}</h2>
                @foreach ($module_items as $module_item)
                    @formField('select', [
                        'name' => $module_name . '_' . $module_item->id . '_permission',
                        'label' => $module_item->title,
                        'unpack' => true,
                        'options' => [
                            [
                                'value' => '',
                                'label' => 'None' 
                            ],
                            [
                                'value' => 'view-item',
                                'label' => 'View'
                            ],
                            [
                                'value' => 'edit-item',
                                'label' => 'Edit'
                            ],
                            [
                                'value' => 'manage-item',
                                'label' => 'Manage'
                            ],
                        ]
                    ])
                @endforeach
            </a17-fieldset>
        @endforeach
    @stop
  @endunless
@endcan

@push('vuexStore')
    window.STORE.publication.submitOptions = {
        draft: [
          {
            name: 'save',
            text: 'Update disabled user'
          },
          {
            name: 'save-close',
            text: 'Update disabled and close'
          },
          {
            name: 'save-new',
            text: 'Update disabled user and create new'
          },
          {
            name: 'cancel',
            text: 'Cancel'
          }
        ],
        live: [
          {
            name: 'publish',
            text: 'Enable user'
          },
          {
            name: 'publish-close',
            text: 'Enable user and close'
          },
          {
            name: 'publish-new',
            text: 'Enable user and create new'
          },
          {
            name: 'cancel',
            text: 'Cancel'
          }
        ],
        update: [
          {
            name: 'update',
            text: 'Update'
          },
          {
            name: 'update-close',
            text: 'Update and close'
          },
          {
            name: 'update-new',
            text: 'Update and create new'
          },
          {
            name: 'cancel',
            text: 'Cancel'
          }
        ]
      }
    @if ($item->id == $currentUser->id)
        window.STORE.publication.withPublicationToggle = false
    @endif
@endpush