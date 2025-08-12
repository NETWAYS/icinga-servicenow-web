<?php

/** @var \Icinga\Application\Modules\Module $this */

$this->provideConfigTab('daemon', [
    'label' => t('ServiceNow Daemon'),
    'title' => t('Configure the ServiceNow Daemon'),
    'url'   => 'config/daemon',
]);
