<?PHP

abstract class UserType
{
    const user = "usr";
    const admin = "admin";
}

abstract class UserConnection
{
    const username = "Cuthbert";
    const password = "Cuthbert1997%";
}

abstract class AdminConnection
{
    const name = "Cuthbert";
    const password = "Cuthbert1997%";
}

abstract class Database
{
    const test = "test";
}


abstract class ExceptionCode {
    // SQL exceptions
    public const SQL_FAILED_CONN = 1001;
    public const SQL_INCORRECT_USAGE = 1002;
    // Add more SQL exception codes as needed
    
    // PHP exceptions
    public const PHP_INVALID_ARGUMENT = 2001;
    public const PHP_FILE_NOT_FOUND = 2002;
    public const PHP_DIVISION_BY_ZERO = 2003;
    // Add more PHP exception codes as needed
}


?>