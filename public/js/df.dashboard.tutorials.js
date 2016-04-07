/*!
 * DreamFactory Enterprise(tm) User Dashboard
 * Copyright 2012-2102 DreamFactory Software, Inc. All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains the property of DreamFactory Software, Inc. and its
 * suppliers, if any. The intellectual and technical concepts contained herein are proprietary to DreamFactory
 * Software, Inc. and its suppliers and may be covered by U.S. and Foreign Patents, patents in process, and are
 * protected by trade secret or copyright law. Dissemination of this information or reproduction of this material is
 * strictly forbidden unless prior written permission is obtained from DreamFactory Software, Inc.
 */
var tutOpts = {
        tour:     [null, null],
        settings: {
            new:    {
                debug:    true,
                template: '<div class="popover tour"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div><div class="popover-navigation"><button class="btn btn-sm btn-warning" data-role="prev"><i class="fa fa-fw fa-arrow-circle-left"></i> Prev</button><span data-role="separator">|</span><button class="btn btn-sm btn-warning" data-role="next">Next <i class="fa fa-fw fa-arrow-circle-right"></i></button><button class="btn btn-sm btn-warning" data-role="end">End tour</button></div></div>',
                name:     'dfe-dashboard-tutorial-new',
                steps:    [
                    {
                        element:   '#new-instance input#instance-id',
                        title:     'New Instance Name',
                        content:   'Choose a name for your new DreamFactory&trade; instance, and enter it here',
                        placement: 'left'
                    },
                    {
                        element:   '#new-instance #upload-package',
                        title:     'Upload a package',
                        content:   'You may also upload an existing DreamFactory&trade;-created package file to be installed automatically on your new instance',
                        placement: 'left'
                    }, {
                        element:   '.instance-panel-group .panel-instance:first-child .panel-toolbar button[id^="instance-launch-"]:first',
                        title:     'Launch Your Instance',
                        content:   'This is a link directly to your new instance. It will open in a new tab/window.',
                        placement: 'top'
                    },
                    {
                        element:   '.instance-panel-group .panel-instance:first-child .panel-toolbar button[id^="instance-delete-"]:first',
                        title:     'Delete Your Instance',
                        content:   'If you have uploaded your own backup, you can choose a new name for your instance here',
                        placement: 'top'
                    },
                    {
                        element:   '.instance-panel-group .panel-instance:first-child .panel-toolbar button[id^="instance-export-"]:first',
                        title:     'Export Your Instance',
                        content:   'This button will delete your instance. There is no way to get it back. Make sure you get an export first.',
                        placement: 'top'
                    }
                ]
            },
            import: {
                debug:    true,
                template: '<div class="popover tour"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div><div class="popover-navigation"><button class="btn btn-sm btn-warning" data-role="prev"><i class="fa fa-fw fa-arrow-circle-left"></i> Prev</button><span data-role="separator">|</span><button class="btn btn-sm btn-warning" data-role="next">Next <i class="fa fa-fw fa-arrow-circle-right"></i></button><button class="btn btn-sm btn-warning" data-role="end">End tour</button></div></div>',
                name:     'dfe-dashboard-tutorial-import',
                steps:    [
                    {
                        element:   '#import-instance #import-id',
                        title:     'Have an existing export?',
                        content:   'We keep a backup of all your exported instances, choose one to instantly restore it',
                        placement: 'left'
                    },
                    {
                        element:   '#import-instance input#upload-file',
                        title:     'Upload your own!',
                        content:   'No backups available? You can upload any DreamFactory&trade; export and restore it on this system',
                        placement: 'left'
                    },
                    {
                        element:   '#import-instance input#instance-id',
                        title:     'Name your instance',
                        content:   'If you have uploaded your own backup, you can choose a new name for your instance here',
                        placement: 'left'
                    }
                ]
            }
        }
    }
    ;

/**
 * Initializes the tour
 */
var initializeTutorials = function(newInstance, force) {
    var tab = newInstance ? 0 : 1;

    if (force || null === tutOpts.tour[tab]) {
        tutOpts.tour[tab] = new Tour(newInstance ? tutOpts.settings.new : tutOpts.settings.import);
        tutOpts.tour[tab].init();

        //  Prevent disabled clicks
        $('button').on('click', '.disabled', function(e) {
                e.preventDefault();
                return false;
            }
        )
    }

    if (tutOpts.tour[tab]) {
        tutOpts.tour[tab].start(true);
    }
};

/**
 * DR
 */
jQuery(function($) {
        initializeTutorials(true);

        $('#instance-create-tabs').find('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                initializeTutorials(e.target.id == 'new-instance');
            }
        );
    }
);
