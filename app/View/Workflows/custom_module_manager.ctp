<?php
    $fields = [
        [
            'element' => 'selector',
            'data' => [
                'id' => [
                    'value_path' => 'id'
                ]
            ]
        ],
        [
            'name' => __('Module name'),
            'sort' => 'name',
            'data_path' => 'name',
            'element' => 'custom',
            'class' => 'bold shortish',
            'function' => function ($row) use ($baseurl) {
                if (!empty($row['icon'])) {
                    return sprintf('<i class="fa-fw %s"></i> %s', $this->FontAwesome->getClass($row['icon']), h($row['name']));
                } else if (!empty($row['icon_path'])) {
                    return sprintf('<img src="%s" alt="Icon of %s" style="width: 12px; filter: grayscale(1);"> %s', sprintf('%s/%s/%s', $baseurl, 'img', h($row['icon_path'])), h($row['name']), h($row['name']));
                }
                return h($row['name']);
            }
        ],
        [
            'name' => __('Description'),
            'data_path' => 'description',
        ],
        [
            'name' => __('Type'),
            'sort' => 'module_type',
            'class' => 'short',
            'data_path' => 'module_type',
        ],
        [
            'name' => __('Enabled'),
            'sort' => 'disabled',
            'class' => 'short',
            'data_path' => 'disabled',
            'element' => 'booleanOrNA',
            'boolean_reverse' => true,
            'colors' => true,
        ],
    ];

    if (!empty($module_service_error)) {
        echo sprintf('<div class="alert alert-error"><strong>%s</strong> %s<div>%s</div></div>',
            __('MISP Modules Action Services is not reachable!'),
            __('Some modules will not be available.'),
            __('Make sure the %s %s is enabled and the action service is reachable.', sprintf('<a href="%s">%s</a>', $baseurl . '/servers/serverSettings/Plugin',  __('setting')), sprintf('<code>%s</code>', 'Plugin.Action_services_enable'))
        );
    }
    if (!empty($errorWhileLoading)) {
        $loadingErrorHtml = sprintf('<ul>%s</ul>', implode('', array_map(function ($filepath, $message) {
            return sprintf('<li><strong>%s</strong>: %s</li>', h($filepath), h($message));
        }, array_keys($errorWhileLoading), $errorWhileLoading)));
        echo sprintf('<div class="alert alert-error"><strong>%s</strong><div>%s%s</div></div>', __('Error while trying to load modules!'), __('The following module(s) failed to load:'), $loadingErrorHtml);
    }

    echo $this->element('genericElements/IndexTable/scaffold', [
        'scaffold_data' => [
            'data' => [
                'data' => $data,
		'top_bar' => [
                    'children' => [
                        [
                            'type' => 'simple',
                            'children' => [
                                [
				    'class' => 'hidden mass-select',
                                    'text' => __('Enable selected'),
                                    'onClick' => 'multiSelectToggleField',
                                    'onClickParams' => ['workflows', 'massToggleField', 'enabled', '1', '#WorkflowModuleIds'],
                                ],
                                [
				    'class' => 'hidden mass-select',
                                    'text' => __('Disable selected'),
                                    'onClick' => 'multiSelectToggleField',
                                    'onClickParams' => ['workflows', 'massToggleField', 'enabled', '0', '#WorkflowModuleIds'],
                                ],
				[
				    'class' => 'hidden mass-select',
                                    'text' => __('Delete selected'),
                                    'onClick' => 'multiSelectToggleField',
                                    'onClickParams' => ['workflows', 'massToggleField', 'delete', '1', '#WorkflowModuleIds'],
                                ],
                            ],
                        ],
                        [
                            'type' => 'simple',
                            'children' => [
                                [
                                    'url' => $baseurl . '/servers/serverSettings/files#title_modules_action',
                                    'text' => __('Upload New'),
                                    'active' => $indexType === 'all',
                                ]
			    ]
			]
		    ]
		],
                'fields' => $fields,
                'icon' => 'flag',
                'title' => __('Custom Workflow Modules Manager'),
                'description' => __('List the available custom modules that can be used by workflows'),
                'actions' => [
                    [
                        'title' => __('Enable'),
                        'icon' => 'play',
                        'postLink' => true,
                        'url' => $baseurl . '/workflows/toggleModule',
                        'url_params_data_paths' => ['id'],
                        'url_suffix' => '/1',
                        'postLinkConfirm' => __('Are you sure you want to enable this module?'),
                        'complex_requirement' => array(
                            'function' => function ($row, $options) use ($isSiteAdmin) {
                                return $isSiteAdmin && $options['datapath']['disabled'];
                            },
                            'options' => array(
                                'datapath' => array(
                                    'disabled' => 'disabled'
                                )
                            )
                        ),
                    ],
                    [
                        'title' => __('Disable'),
                        'icon' => 'stop',
                        'postLink' => true,
                        'url' => $baseurl . '/workflows/toggleModule',
                        'url_params_data_paths' => ['id'],
                        'url_suffix' => '/0',
                        'postLinkConfirm' => __('Are you sure you want to disable this module?'),
                        'complex_requirement' => array(
                            'function' => function ($row, $options) use ($isSiteAdmin) {
                                return $isSiteAdmin && !$options['datapath']['disabled'];
                            },
                            'options' => array(
                                'datapath' => array(
                                    'disabled' => 'disabled'
                                )
                            )
                        ),
                    ],
                    [
                        'url' => $baseurl . '/workflows/moduleView',
                        'url_params_data_paths' => ['id'],
                        'icon' => 'eye',
                        'dbclickAction' => true,
                    ],
                    [
                        'title' => __('Delete'),
                        'icon' => 'trash',
                        'postLink' => true,
                        'url' => $baseurl . '/workflows/delete',
                        'url_params_data_paths' => ['id'],
                        'postLinkConfirm' => __('Are you sure you want to delete this module?'),
                        'complex_requirement' => array(
                            'function' => function ($row, $options) use ($isSiteAdmin) {
                                return $isSiteAdmin && $options['datapath']['disabled'];
                            },
                            'options' => array(
                                'datapath' => array(
                                    'disabled' => 'disabled'
                                )
                            )
                        ),
                    ],
                ]
            ]
        ]
    ]);
    /*
    echo $this->Form->create('Server', array('type' => 'file', 'url' => $baseurl . '/servers/uploadFile/modules_action'));?>
        <fieldset>
            <?php
            echo $this->Form->file('file', array(
                'error' => array('escape' => false),
            ));
            ?>
        </fieldset>
    <?php
    echo $this->Form->button(__('Upload'), array('class' => 'btn btn-primary'));
    echo $this->Form->end();
     */
