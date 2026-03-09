<?php

namespace Icinga\Module\Servicenow\Client;

use Icinga\Application\Config;
use Icinga\Application\Logger;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;

use Exception;

/**
 * Snow handles calling the Icinga ServiceNow Daemon API and returning the data.
 */
class Snow
{
    protected const INCIDENT_ENDPOINT = '/api/v1/incident';
    protected const SNOW_INCIDENT_ENDPOINT = '/api/v1/snow-incident';
    protected const STATUS_ENDPOINT = '/-/healthy';

    /** @var $this \Icinga\Application\Modules\Module */
    protected $client = null;

    protected string $URL;

    public function __construct(
        string $baseURI,
        string $username,
        string $password,
        int $timeout,
        bool $tlsVerify,
    ) {
        $this->client = new Client([
            'timeout' => $timeout,
            // 'auth' => [$username, $password],
            'verify' => $tlsVerify
        ]);

        $this->URL = rtrim($baseURI, '/');
    }

    /**
     * send sends an incidents to the Daemon's HTTP API.
     * TODO: Maybe have an Incident Model.
     */
    public function send(
        string $hostName,
        string $serviceName,
        bool $isVolatile,
        string $notificationName,
        string $notificationType,
        string $notificationState,
        string $notificationOutput,
        string $template,
        array $additionalFields,
    ): Response {
        $data = [
            'json' => [
                'host_name' => $hostName,
                'service_name' => $serviceName,
                'is_volatile' => $isVolatile,
                'notification_name' => $notificationName,
                'notification_type' => $notificationType,
                'notification_state' => $notificationState,
                'notification_output' => $notificationOutput,
                'template' => $template,
                'additional_fields' => $additionalFields,
            ]
        ];

        $url = $this->URL . $this::INCIDENT_ENDPOINT;

        Logger::debug('Calling incident API at %s with data: %s', $url, $data);

        $response = $this->client->request('POST', $url, $data);

        return $response;
    }

    /**
     * fetch fetches a single incident from ServiceNow
     */
    public function fetch(string $incidentNumber): Response
    {
        $url = $this->URL . $this::SNOW_INCIDENT_ENDPOINT . '/' . $incidentNumber;

        Logger::debug('Calling incident fetch API at %s', $url);

        $response = $this->client->request('GET', $url);

        return $response;
    }

    /**
     * status calls the Daemon's HTTP API to determine if it is reachable.
     * We use this to validate the configuration and if the API is reachable.
     *
     * @return array
     */
    public function status(): array
    {
        $url = $this->URL . $this::STATUS_ENDPOINT;

        try {
            $response = $this->client->request('GET', $url, []);
            return ['output' =>  $response->getBody()->getContents()];
        } catch (ConnectException $e) {
            return ['output' => 'Connection error: ' . $e->getMessage(), 'error' => true];
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                return ['output' => 'HTTP error: ' . $e->getResponse()->getStatusCode() . ' - ' .
                                      $e->getResponse()->getReasonPhrase(), 'error' => true];
            } else {
                return ['output' => 'Request error: ' . $e->getMessage(), 'error' => true];
            }
        } catch (Exception $e) {
            return ['output' => 'General error: ' . $e->getMessage(), 'error' => true];
        }

        return ['output' => 'Unknown error', 'error' => true];
    }

    /**
     * fromConfig returns a new Snow Client from this module's configuration
     *
     * @param Config $moduleConfig configuration to load (used for testing)
     * @return $this
     */
    public static function fromConfig(Config $moduleConfig = null): Snow
    {
        $default = [
            'api_url' => 'http://localhost:5910',
            'api_timeout' => 10,
            'api_username' => '',
            'api_password' => '',
            'api_tls_insecure' => false,
        ];

        // Try to load the configuration
        if ($moduleConfig === null) {
            try {
                Logger::debug('Loaded ServiceNow module configuration to get Config');
                $moduleConfig = Config::module('servicenow');
            } catch (Exception $e) {
                Logger::error('Failed to load ServiceNow module configuration: %s', $e);
                return $default;
            }
        }

        $baseURI = rtrim($moduleConfig->get('servicenow', 'api_url', $default['api_url']), '/');
        $timeout = (int) $moduleConfig->get('servicenow', 'api_timeout', $default['api_timeout']);
        $username = $moduleConfig->get('servicenow', 'api_username', $default['api_username']);
        $password = $moduleConfig->get('servicenow', 'api_password', $default['api_password']);
        // Hint: We use a "skip TLS" logic in the UI, but Guzzle uses "verify TLS"
        $tlsVerify = !(bool) $moduleConfig->get('servicenow', 'api_tls_insecure', $default['api_tls_insecure']);

        return new static($baseURI, $username, $password, $timeout, $tlsVerify);
    }
}
