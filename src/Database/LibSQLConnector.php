<?php

namespace Turso\Http\Laravel\Database;

class LibSQLConnector
{
    /**
     * Establish a database connection.
     */
    public function connect(array $config): LibSQLDatabase
    {
        return new LibSQLDatabase($config);
    }
}
