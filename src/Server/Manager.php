<?php

namespace Siktec\Dmm\Server;

use Siktec\Dmm\Exceptions;
use Siktec\Dmm\Server\Connection;

class Manager
{
    /**
     * @var Connection[];
     */
    private static array $connections = [];


    /**
     * define a connection
     * 
     * @param string $name
     * @param bool $persistant
     * @param string $host
     * @param int $port
     * @param float $timeout
     * @param string|null $reserved
     * @param int $retry_interval
     * @param float $read_timeout
     * @return bool true if the connection was added, false if the connection already exists
     */
    public static function defineConnection(
        string  $name           = 'default',
        bool    $persistant     = false,
        string  $host           = '127.0.0.1',
        int     $port           = 6379,
        float   $timeout        = 0.0,
        ?string $reserved       = null,
        int     $retry_interval = 0,
        float   $read_timeout   = 0.0

    ) : bool {
        return self::addConnection(new Connection(
            $name,
            $persistant,
            $host,
            $port,
            $timeout,
            $reserved,
            $retry_interval,
            $read_timeout
        ));
    }

    /**
     * add a connection from a connection object
     * @param Connection $connection
     * 
     * @return bool true if the connection was added, false if the connection already exists
     */
    public static function addConnection(Connection $connection) : bool
    {
        if (!self::hasConnection($connection->isName())) {
            self::$connections[$connection->isName()] = $connection;
            return true;
        }
        return false;
    }

    /**
     * check if a connection is defined
     * 
     * @param string $name
     * 
     * @return bool
     */
    public static function hasConnection(string $name) : bool
    {
        return array_key_exists($name, self::$connections);
    }


    /**
     * remove a connection
     * 
     * @param string $name
     * 
     * @return bool true if the connection was removed, false if the connection was not found
     */
    public static function removeConnection(string $name) : bool
    {
        if (self::hasConnection($name)) {
            unset(self::$connections[$name]);
            return true;
        }
        return false;
    }

    /** 
     * get a connection by name 
     * will auto connect if not connected
     * 
     * @param string $name the name of the connection
     * @param bool $auto_connect should the connection be auto connected
     * @return Connection
     * 
     * @throws Exceptions\ConnectionException if the connection could not be established or the connection does not exist
     * 
     */
    public static function getConnection(
        string $name = 'default',
        bool $auto_connect = true
    ) : Connection
    {
        if (!self::hasConnection($name)) {
            throw new Exceptions\ConnectionException($name, 141);
        }
        $connection = self::$connections[$name];
        if ($auto_connect && !$connection->isConnected()) {
            $connection->connect();
        }
        return $connection;
    }

    /**
     * manually perform a connection
     * this will connect all connections if no name is provided
     * should not be used in production since getConnection will auto connect
     * 
     * @param string[] $names the name of the connection
     * 
     * @return void
     */
    public static function manualConnect(string ...$names) : void
    {
        if (empty($name)) {
            $names = array_keys(self::$connections);
        }
        foreach ($names as $name) {
            if (self::hasConnection($name)) {
                self::$connections[$name]->connect();
            }
        }
    }

    /**
     * manually disconnect a connection
     * this will disconnect all connections if no name is provided
     * 
     * @param string[] $names the name of the connection
     * 
     * @return void
     */
    public static function manualDisconnect(string ...$names) : void
    {  
        if (empty($name)) {
            $names = array_keys(self::$connections);
        }
        foreach ($names as $name) {
            if (self::hasConnection($name)) {
                self::$connections[$name]->disconnect();
            }
        }
    }

    public static function print()
    {
        $connections = [];
        foreach (self::$connections as $connection) {
            $connections[] = " - ".((string)$connection);
        }
        printf(
            "Manager with %s connections: ".PHP_EOL."%s",
            count(self::$connections), 
            implode(PHP_EOL, $connections)
        );
    }

    
}
