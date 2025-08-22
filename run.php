<?php

/** @var $this \Icinga\Application\Modules\Module */

$this->provideHook('monitoring/HostActions');
$this->provideHook('monitoring/ServiceActions');

$this->provideHook('icingadb/HostActions');
$this->provideHook('icingadb/ServiceActions');
$this->provideHook('icingadb/IcingadbSupport');

$this->provideHook('health', 'SnowHealth');
