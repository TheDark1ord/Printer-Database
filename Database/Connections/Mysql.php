<?php

namespace Database\Connections;

use mysqli;

class MysqlConn extends Connection
{
    /**
     * Constructor
     *
     * @param string|null $host
     * @param int|null $port
     * @param string|null $username
     * @param string|null $password
     * @param string|null $socket
     * @param string|null $database
     *
     * @return MysqlConn|false
     */
    public static function connect(
        ?string $hostname = null, ?string $username = null,
        ?string $password = null, ?string $database = null,
        ?int $port = null, ?string $socket = null
    ): MysqlConn|false {
        $conn = \mysqli_connect(
            $hostname,
            $username,
            $password,
            $database,
            $port,
            $socket
        );

        if ($conn === false) {
            return false;
        }
        $ret_conn = new MysqlConn;
        $ret_conn->conn = $conn;

        return $ret_conn;
    }

    public function close()
    {
        $this->conn->close();
    }

    /**
     * Executes a query with optional parameters
     *
     * @param string $query
     * @param array|null $params
     */
    public function execute_query(string $query, ?array $params = null): bool
    {
        if ($params === null) {
            $this->result = $this->conn->query($query);
        } else {
            $this->result = $this->conn->execute_query($query, $params);
        }

        # Так как result может принимать значения mysqli_result|bool
        # здесь нельзя написать "return $this->result"
        return $this->result === false ? false : true;
    }

    public function get_result(): array
    {
        if ($this->result != false) {
            return $this->result->fetch_all();
        } else {
            return [];
        }
    }

    public function get_error(): string
    {
        return $this->conn->error;
    }

    private mysqli $conn;
    private \mysqli_result|bool $result;
}

?>