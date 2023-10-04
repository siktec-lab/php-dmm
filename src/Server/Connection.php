<?php

namespace Siktec\PhpRedis\Server;

use Siktec\PhpRedis\Exceptions\ConnectionException;

class Connection
{

    private string $name;

    private string $host;

    private int $port;

    private float $timeout;

    private ?string $id;

    private int $retry_interval;

    private float $read_timeout;

    private array $context;

    private bool $should_persist;

    private bool $persistant = false;

    private \Redis $redis;

    /**
     * Connection constructor.
     */
    public function __construct(
        string  $name           = 'default',
        bool    $persistant     = false,
        string  $host           = '127.0.0.1',
        int     $port           = 6379,
        float   $timeout        = 0.0,
        ?string $id             = null,
        int     $retry_interval = 0,
        float   $read_timeout   = 0.0,
        array   $context        = []
    )
    {
        $this->name         = strtolower($name);
        $this->should_persist = $persistant;
        $this->host         = $host;
        $this->port         = $port;
        $this->timeout      = $timeout;
        $this->id           = $id ?? $this->name . '_' . $this->port;
        $this->retry_interval = $retry_interval;
        $this->read_timeout = $read_timeout;
        $this->context      = $context;
        $this->redis        = new \Redis();
    }


    public function isName(?string $name = null): bool|string
    {
        return is_null($name) ? $this->name : $this->name === strtolower($name);
    }

    /**
     * check is this connection is persistant
     * 
     * @return bool
     */
    public function isPersistant(): bool
    {
        return $this->persistant;
    }

    /**
     * check if this connection is connected
     * 
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->redis->isConnected();
    }

    /**
     * establish a connection:
     * 
     * @return bool
     * @throws /RedisException
     */
    private function _connect() : bool {
        $this->persistant = false;
        return $this->redis->connect(
            $this->host,
            $this->port,
            $this->timeout,
            $this->id,
            $this->retry_interval,
            $this->read_timeout,
            $this->context
        );
    }

    private function _pconnect() {
        $this->persistant = true;
        return $this->redis->pconnect(
            $this->host,
            $this->port,
            $this->timeout,
            $this->id,
            $this->retry_interval,
            $this->read_timeout,
            $this->context
        );
    }
    /**
     * establish a connection to this connection
     * 
     * @return \Redis
     * @throws ConnectionException if the connection could not be established
     */
    public function connect(?bool $persistant = null): bool
    {
        try {
            if ($persistant ?? $this->should_persist) {
                return $this->_pconnect();
            }
            return $this->_connect();
        } catch (\RedisException $e) {
            throw new ConnectionException($this->name, code: 140, previous: $e);
        }
    }

    public function disconnect(): bool
    {
        return $this->redis->close();
    }

    public function getRedis(): \Redis
    {
        return $this->redis;
    }

    // string
    public function __toString() : string
    {
        return sprintf(
            'Connection %s to %s:%s [persistant: %s, connected: %s, id: %s]',
            $this->name,
            $this->host,
            $this->port,
            $this->persistant ? 'yes' : 'no',
            $this->isConnected() ? 'yes' : 'no',
            $this->id
        );
    }


}