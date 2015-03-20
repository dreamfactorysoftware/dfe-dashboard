<?php
$_uri = URL::getRequest()->getRequestUri();

/**
 * This is the basic definition of the sidebar. The code below renders
 */
$_sidebar = [
    'Operations' => [
        'Dashboard' => ['href' => '/app/dashboard', 'icon' => 'fa-dashboard'],
        'Alerts'    => ['href' => '/app/alerts', 'icon' => 'fa-bell'],
        'Reports'   => ['href' => '/app/reports', 'icon' => 'fa-th-list'],
        'Profile'   => ['href' => '/app/profile', 'icon' => 'fa-user'],
    ],
    'Settings'   => [
        'Roles & Limits' => ['href' => '/settings/roles', 'icon' => 'fa-users'],
        'Servers'        => ['href' => '/settings/servers', 'icon' => 'fa-desktop'],
        'Clusters'       => ['href' => '/settings/clusters', 'icon' => 'fa-sitemap'],
        'Instances'      => ['href' => '/settings/instances', 'icon' => 'fa-desktop'],
        'Instance Users' => ['href' => '/settings/users', 'icon' => 'fa-user'],
    ]
];

$_html = null;

// Build the sidebars
foreach ( $_sidebar as $_sectionTitle => $_section )
{
    $_html .= '<ul class="nav nav-sidebar"><li class="dropdown-header">' . $_sectionTitle . '</li>';

    foreach ( $_section as $_itemTitle => $_item )
    {
        $_active =
            $_uri == $_item['href']
                ? ' class=active '
                : null;

        $_html .= '<li ' .
            $_active .
            ' role="presentation"><a href="' .
            $_item['href'] .
            '"><i class="fa fa-fw ' .
            $_item['icon'] .
            '"></i> ' .
            $_itemTitle .
            '</a>';
    }

    $_html .= '</ul>';
}

?>
<div id="sidebar-collapse" class="collapse navbar-collapse">
    {!! $_html !!}
</div>
