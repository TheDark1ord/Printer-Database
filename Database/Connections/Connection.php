<?php

namespace Database\Connections;

#Абстрактный класс, который определяет подключение к базе данных,
# что позволяет без изменения кода добавлять поддержку новых баз данных
abstract class Connection
{

    abstract public static function connect(
        ?string $hostname = null, ?string $username = null,
        ?string $password = null, ?string $database = null,
        ?int $port = null, ?string $socket = null
    ): Connection|false;

    abstract public function close();

    # Функция не возвращяет результат запроса, только его статус,
    # для получения результата используется функция get_result
    abstract public function execute_query(string $query, ?array $params = null): bool;

    # Возвращает результат последнего запроса
    abstract public function get_result(): array;

    # Возвращает ошибку после последнего запроса, если запрос был
    # выполнен без ошибок, возвращается пустая строка
    abstract public function get_error(): string;
}

?>