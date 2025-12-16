<?php

/** @var \Icinga\Application\Modules\Module $this */

$this->providePermission('servicenow/template/edit', $this->translate('Allow the user to edit templates'));

$section = $this->menuSection(N_('ServiceNow'))
    ->setUrl('servicenow/incidents')
    ->setPriority(63)
    ->setIcon('tasks');

$section->add(N_('Incidents'))->setUrl('servicenow/incidents')->setPriority(10);
$section->add(N_('Templates'))->setUrl('servicenow/templates')->setPriority(20);

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
