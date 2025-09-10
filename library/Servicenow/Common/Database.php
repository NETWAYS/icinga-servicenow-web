<?php
namespace Icinga\Module\Servicenow\Common;

use Icinga\Application\Config as IcingaConfig;
use Icinga\Data\ResourceFactory;
use ipl\Sql\Config as SqlConfig;
use ipl\Sql\Connection;
use LogicException;
use PDO;

/**
 * Trait for accessing the database
 */
trait Database
{
    /**
     * Check if db exists
     *
     * @return bool true if a database was found otherwise false
     */
    protected function hasSNOWDb()
    {
        return (bool) IcingaConfig::module('servicenow')->get('db', 'resource');
    }

    /**
     * Get a connection to the database
     *
     * @return Connection
     *
     * @throws \Icinga\Exception\ConfigurationError
     */
    protected function getDB(): Connection
    {
        if (! $this->hasSNOWDb()) {
            throw new LogicException('Please check if a db resource was configured');
        }

        $config = new SqlConfig(ResourceFactory::getResourceConfig(
            IcingaConfig::module('servicenow')->get('db', 'resource')
        ));

        if ($config->db === 'mysql') {
            $config->charset = 'utf8mb4';
        }

        $config->options = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ];

        if ($config->db === 'mysql') {
            $config->options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET SESSION SQL_MODE='STRICT_TRANS_TABLES"
                . ",NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'";
        }

        return new Connection($config);
    }
}
