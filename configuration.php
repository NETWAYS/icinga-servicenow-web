<?php

/** @var \Icinga\Application\Modules\Module $this */

$this->provideConfigTab('daemon', [
    'label' => t('ServiceNow Daemon'),
    'title' => t('Configure the ServiceNow daemon'),
    'url'   => 'config/daemon',
]);

$this->provideConfigTab('database', [
    'label' => t('Database'),
    'title' => t('Configure the database backend'),
    'url'   => 'config/database'
]);
