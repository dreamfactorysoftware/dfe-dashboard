<?php namespace DreamFactory\Enterprise\Services\Auditing;

use DreamFactory\Enterprise\Services\Auditing\Components\GelfMessage;
use DreamFactory\Enterprise\Services\Auditing\Enums\AuditLevels;
use DreamFactory\Enterprise\Services\Auditing\Utility\GelfLogger;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

/**
 * Contains auditing methods for DFE
 */
class AuditingService
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string
     */
    const DEFAULT_FACILITY = 'dreamfactory-enterprise';

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type GelfLogger
     */
    protected $gelfLogger = null;
    /**
     * @type array
     */
    protected $metadata;
    /**
     * @type Application
     */
    protected $app;

    //********************************************************************************
    //* Public Methods
    //********************************************************************************

    /**
     * boot up
     *
     * @param Application $app
     * @param GelfLogger  $logger
     */
    public function __construct($app, GelfLogger $logger = null)
    {
        $this->app = $app;

        $this->setLogger($logger);
    }

    /**
     * @param string $host
     */
    public function setHost($host = GelfLogger::DEFAULT_HOST)
    {
        $this->getLogger()->setHost($host);
    }

    /**
     * Logs API requests to logging system
     *
     * @param string  $instanceId  The id of the sending instance
     * @param Request $request     The request
     * @param array   $sessionData Any session data to log
     * @param int     $level       The level, defaults to INFO
     * @param string  $facility    The facility, used for sorting
     *
     * @return bool
     */
    public function logRequest($instanceId, Request $request, $sessionData = [], $level = AuditLevels::INFO, $facility = self::DEFAULT_FACILITY)
    {
        try {
            $_metadata = array_get($sessionData, 'metadata', []);
            array_forget($sessionData, 'metadata');

            //  Add in stuff for API request logging
            static::log([
                'facility' => $facility,
                'dfe'      => $this->prepareMetadata($instanceId, $request, $_metadata),
                'user'     => $sessionData,
            ],
                $level,
                $request);
        } catch (\Exception $_ex) {
            //  Completely ignore any issues
        }
    }

    /**
     * @param string                   $instanceId
     * @param \Illuminate\Http\Request $request
     * @param array                    $metadata
     *
     * @return array
     */
    protected function prepareMetadata($instanceId, Request $request, array $metadata = [])
    {
        return $this->metadata
            ?: [
                'instance_id'       => $instanceId,
                'instance_owner_id' => array_get($metadata, 'owner-email-address'),
                'cluster_id'        => array_get($metadata, 'cluster-id', $request->server->get('DFE_CLUSTER_ID')),
                'app_server_id'     => array_get($metadata,
                    'app-server-id',
                    $request->server->get('DFE_APP_SERVER_ID')),
                'db_server_id'      => array_get($metadata, 'db-server-id', $request->server->get('DFE_DB_SERVER_ID')),
                'web_server_id'     => array_get($metadata,
                    'web-server-id',
                    $request->server->get('DFE_WEB_SERVER_ID')),
            ];
    }

    /**
     * Logs API requests to logging system
     *
     * @param array      $data    The data to log
     * @param int|string $level   The level, defaults to INFO
     * @param Request    $request The request, if available
     * @param string     $type    Optional type
     *
     * @return bool
     */
    public function log($data = [], $level = AuditLevels::INFO, $request = null, $type = null)
    {
        try {
            $_request = $request ?: Request::createFromGlobals();
            $_data = array_merge(static::_buildBasicEntry($_request), $data);
            $type && $_data['type'] = $type;

            $_message = new GelfMessage($_data);
            $_message->setLevel($level);
            $_message->setShortMessage($_request->getMethod() . ' ' . $_request->getRequestUri());
            $_message->setFullMessage('DFE Audit | ' . implode(', ',
                    $_data['source_ip']) . ' | ' . $_data['request_timestamp']);

            $this->getLogger()->send($_message);
        } catch (\Exception $_ex) {
            //  Completely ignore any issues
        }
    }

    /**
     * @param Request|\Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    protected function _buildBasicEntry($request)
    {
        return [
            'request_timestamp' => (double)$request->server->get('REQUEST_TIME_FLOAT', microtime(true)),
            'user_agent'        => $request->headers->get('user-agent', 'None'),
            'source_ip'         => $request->getClientIps(),
            'content_type'      => $request->getContentType(),
            'content_length'    => (int)$request->headers->get('Content-Length', 0),
            'token'             => $request->headers->get('x-dreamfactory-session-token',
                $request->headers->get('x-dreamfactory-access-token',
                    $request->headers->get('authorization', $request->query->get('access_token')))),
            'app_name'          => $request->query->get('app_name',
                $request->headers->get('x-dreamfactory-application-name',
                    $request->headers->get('x-application-name'))),
            'dfe'               => [],
            'host'              => $request->getHost(),
            'method'            => $request->getMethod(),
            'path_info'         => $request->getPathInfo(),
            'path_translated'   => $request->server->get('PATH_TRANSLATED'),
            'query'             => $request->query->all(),
        ];
    }

    /**
     * @return GelfLogger
     */
    public function getLogger()
    {
        return $this->gelfLogger;
    }

    /**
     * Sets a logger instance on the object
     *
     * @param GelfLogger $logger
     *
     * @return $this
     */
    public function setLogger(GelfLogger $logger = null)
    {
        $this->gelfLogger = $logger ?: new GelfLogger();

        return $this;
    }

    /**
     * @param array $metadata
     *
     * @return $this
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = [];

        foreach ($metadata as $_key => $_value) {
            $this->metadata[str_replace('-', '_', $_key)] = $_value;
        }

        return $this;
    }
}
