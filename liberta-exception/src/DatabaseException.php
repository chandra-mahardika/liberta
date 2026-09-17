<?php

namespace Liberta\Exception;

class DatabaseException extends Exception {}

class QueryException extends DatabaseException {}

class ConnectionException extends DatabaseException {}

class MigrationException extends DatabaseException {}
