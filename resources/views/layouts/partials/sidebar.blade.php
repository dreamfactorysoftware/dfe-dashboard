<?php
$_uri = URL::getRequest()->getRequestUri();
?>
<div id="sidebar-collapse" class="collapse navbar-collapse">
    <ul class="nav nav-sidebar">
        <li role="presentation" class="dropdown-header">Operations</li>
        <li role="presentation" {{ '/' == $_uri ? ' class="active"' : null }}>
            <a href="/app/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
        </li>
        <li role="presentation" {{ '/app/support' == $_uri ? ' class="active"' : null }}>
            <a href="/app/alerts"><i class="fa fa-bell fa-fw"></i> Alerts</a>
        </li>
        <li role="presentation" {{ '/app/reports' == $_uri ? ' class="active"' : null }}>
            <a href="/app/reports"><i class="fa fa-th-list fa-fw"></i> Reports</a>
        </li>
        <li role="presentation" {{ '/app/support' == $_uri ? ' class="active"' : null }}>
            <a href="/app/profile"><i class="fa fa-user fa-fw"></i> Profile</a>
        </li>
    </ul>

    <ul class="nav nav-sidebar">
        <li role="presentation" class="dropdown-header">Settings</li>
        <li role="presentation" {{ '/settings/roles' == $_uri ? ' class="active"' : null }}>
            <a href="/settings/roles"><i class="fa fa-users fa-fw"></i> Roles & Limits</a>
        </li>
        <li role="presentation" {{ '/settings/servers' == $_uri ? ' class="active"' : null }}>
            <a href="/settings/servers"><i class="fa fa-desktop fa-fw"></i> Servers</a>
        </li>
        <li role="presentation" {{ '/settings/clusters' == $_uri ? ' class="active"' : null }}>
            <a href="/settings/clusters"><i class="fa fa-sitemap fa-fw"></i> Clusters</a>
        </li>
        <li role="presentation" {{ '/settings/instances' == $_uri ? ' class="active"' : null }}>
            <a href="/settings/instances"><i class="fa fa-desktop fa-fw"></i> Instances</a>
        </li>
        <li role="presentation" {{ '/settings/users' == $_uri ? ' class="active"' : null }}>
            <a href="/settings/users"><i class="fa fa-user fa-fw"></i> Users</a>
        </li>
    </ul>
</div>