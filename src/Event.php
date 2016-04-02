<?php
// @codingStandardsIgnoreFile

namespace Hubkat\Event;

class Event
{
    const EVENT_COMMIT_COMMENT              = 'commit_comment';
    const EVENT_CREATE                      = 'create';
    const EVENT_DELETE                      = 'delete';
    const EVENT_DEPLOYMENT                  = 'deployment';
    const EVENT_DEPLOYMENT_STATUS           = 'deployment_status';
    const EVENT_FORK                        = 'fork';
    const EVENT_GOLLUM                      = 'gollum';
    const EVENT_ISSUE_COMMENT               = 'issue_comment';
    const EVENT_ISSUES                      = 'issues';
    const EVENT_MEMBER                      = 'member';
    const EVENT_MEMBERSHIP                  = 'membership';
    const EVENT_PAGE_BUILD                  = 'page_build';
    const EVENT_PING                        = 'ping';
    const EVENT_PUBLIC                      = 'public';
    const EVENT_PULL_REQUEST_REVIEW_COMMENT = 'pull_request_review_comment';
    const EVENT_PULL_REQUEST                = 'pull_request';
    const EVENT_PUSH                        = 'push';
    const EVENT_REPOSITORY                  = 'repository';
    const EVENT_RELEASE                     = 'release';
    const EVENT_STATUS                      = 'status';
    const EVENT_TEAM_ADD                    = 'team_add';
    const EVENT_WATCH                       = 'watch';

    const TYPES = [
        self::EVENT_COMMIT_COMMENT,
        self::EVENT_CREATE,
        self::EVENT_DELETE,
        self::EVENT_DEPLOYMENT,
        self::EVENT_DEPLOYMENT_STATUS,
        self::EVENT_FORK,
        self::EVENT_GOLLUM,
        self::EVENT_ISSUE_COMMENT,
        self::EVENT_ISSUES,
        self::EVENT_MEMBER,
        self::EVENT_MEMBERSHIP,
        self::EVENT_PAGE_BUILD,
        self::EVENT_PING,
        self::EVENT_PUBLIC,
        self::EVENT_PULL_REQUEST_REVIEW_COMMENT,
        self::EVENT_PULL_REQUEST,
        self::EVENT_PUSH,
        self::EVENT_REPOSITORY,
        self::EVENT_RELEASE,
        self::EVENT_STATUS,
        self::EVENT_TEAM_ADD,
        self::EVENT_WATCH,
    ];

    /**
     * Name
     *
     * @var mixed
     *
     * @access protected
     */
    protected $name;

    /**
     * Signature
     *
     * @var mixed
     *
     * @access protected
     */
    protected $signature;

    /**
     * Delivery
     *
     * @var mixed
     *
     * @access protected
     */
    protected $delivery;

    /**
     * Raw body
     *
     * @var mixed
     *
     * @access protected
     */
    protected $rawBody;

    /**
     * Payload
     *
     * @var mixed
     *
     * @access protected
     */
    protected $payload;

    /**
     * __construct
     *
     * @param mixed $delivery DESCRIPTION
     * @param mixed $name     DESCRIPTION
     * @param mixed $sig      DESCRIPTION
     * @param mixed $body     DESCRIPTION
     *
     * @return mixed
     *
     * @access public
     */
    public function __construct($delivery, $name, $sig, $body)
    {
        $this->delivery  = $delivery;
        $this->name      = $name;
        $this->signature = $sig;
        $this->rawBody   = $body;

        $this->payload = json_decode($body);
    }


    /**
     * Validate the signature
     *
     * @param string $secret secret string
     *
     * @return bool
     *
     * @access public
     */
    public function isValid($secret)
    {
        if (!extension_loaded('hash')) {
            // @codeCoverageIgnoreStart
            throw new \Exception('Hash extension not loaded');
            // @codeCoverageIgnoreEnd
        }

        list($algo, $sig) = explode("=", $this->signature);

        if (!in_array($algo, hash_algos(), true)) {
            throw new \Exception("Hash algorithm '$algo' is not supported.");
        }

        $hash = hash_hmac($algo, $this->rawBody, $secret);

        return (md5($sig) === md5($hash));
    }

    /**
     * __get
     *
     * @param mixed $key DESCRIPTION
     *
     * @return mixed
     *
     * @access public
     */
    public function __get($key)
    {
        return $this->$key;
    }

    public function toArray()
    {
        return [
            'delivery' => $this->delivery,
            'name' => $this->name,
            'signature' => $this->signature,
            'payload' => $this->payload
        ];
    }

    public function getDelivery()
    {
        return $this->delivery;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSignature()
    {
        return $this->signature;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function getRawBody()
    {
        return $this->rawBody;
    }
}
