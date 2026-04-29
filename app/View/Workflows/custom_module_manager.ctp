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
            'name' => __('Blocking'),
            'sort' => 'blocking',
            'class' => 'short',
            'data_path' => 'blocking',
            'element' => 'boolean',
            'colors' => true,
        ],
        [
            'name' => __('MISP Core format'),
            'sort' => 'expect_misp_core_format',
            'class' => 'short',
            'data_path' => 'expect_misp_core_format',
            'element' => 'boolean',
            'colors' => true,
            'title' => __('Does this module expect data compliant with the MISP core format'),
        ],
        [
            'name' => __('misp-module'),
            'sort' => 'is_misp_module',
            'data_path' => 'is_misp_module',
            'element' => 'boolean',
            'colors' => true,
            'class' => 'short',
        ],
        [
            'name' => __('Custom'),
            'sort' => 'is_custom',
            'data_path' => 'is_custom',
            'element' => 'boolean',
            'colors' => true,
            'class' => 'short',
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
                ]
            ]
        ]
    ]);
