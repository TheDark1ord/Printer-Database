<?php

namespace Aternos\Model\Driver\CustomConn;

use Aternos\Model\{
    Driver\Driver,
    Driver\Features\CRUDAbleInterface,
    Driver\Features\CRUDQueryableInterface,
    ModelInterface,
    Query\Generator\SQL,
    Query\Query,
    Query\QueryResult
};
use Exception;

use Database\Connections\Connection;

class CustomConn extends Driver implements CRUDAbleInterface, CRUDQueryableInterface
{
    public const ID = "custom_conn";
    protected string $id = self::ID;

    /**
     * Host address
     *
     * @var string|null
     */
    protected ?string $host = null;

    /**
     * Host port
     *
     * @var int|null
     */
    protected ?int $port = null;

    /**
     * Authentication username
     *
     * @var string|null
     */
    protected ?string $username = null;

    /**
     * Authentication password
     *
     * @var string|null
     */
    protected ?string $password = null;

    /**
     * Socket path or pipe
     *
     * @var string|null
     */
    protected ?string $socket = null;

    /**
     * Database name
     *
     * @var string
     */
    protected string $database = "data";

    /**
     * @var Connection|null
     */
    protected ?Connection $connection = null;

    protected ?Connection $connection_class = null;

    public function __construct(
        Connection $conn, ?string $host = null, ?int $port = null, ?string $username = null,
        ?string $password = null, ?string $socket = null, ?string $database = null
    ) {
        $this->connection_class = $conn;
        $this->host = $host ?? $this->host;
        $this->port = $port ?? $this->port;
        $this->username = $username ?? $this->username;
        $this->password = $password ?? $this->password;
        $this->socket = $socket ?? $this->socket;
        $this->database = $database ?? $this->database;
    }

    /**
     * Connect to database
     *
     * @throws Exception
     */
    protected function connect(): void
    {


        $this->connection = $this->connection_class::connect(
            $this->host, $this->username, $this->password,
            $this->database, $this->port, $this->socket
        );

        if ($this->connection === false) {
            throw new Exception("Could not connect to the database.");
        }
    }

    /**
     * Execute a query
     *
     * @param string $query
     * @return bool|array
     * @throws Exception
     */
    protected function rawQuery(string $query): array|bool
    {
        $result = $this->connection->execute_query($query);
        if ($this->connection->get_error()) {
            throw new Exception("SQL Error:" . $this->connection->get_error());
        }

        if (!$result) {
            return false;
        }
        return $this->connection->get_result();
    }

    
}

?>