<?php
// @codingStandardsIgnoreFile

namespace Hubkat\Event;

use Hubkat\EventInterface\EventInterface;

class Event implements EventInterface
{
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
