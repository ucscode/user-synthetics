<?php

namespace Uss\Component\Kernel\Resource;

enum MysqliDataTypeEnum: string 
{
    case _TINYINT = 'TINYINT';
    case _SMALLINT = 'SMALLINT';
    case _MEDIUMINT = 'MEDIUMINT';
    case _INT = 'INT';
    case _BIGINT = 'BIGINT';

    case _FLOAT = 'FLOAT';
    case _DOUBLE = 'DOUBLE';
    case _DECIMAL = 'DECIMAL';

    case _CHAR = 'CHAR';
    case _VARCHAR = 'VARCHAR';
    case _TINYTEXT = 'TINYTEXT';
    case _TEXT = 'TEXT';
    case _MEDIUMTEXT = 'MEDIUMTEXT';
    case _LONGTEXT = 'LONGTEXT';

    case _DATE = 'DATE';
    case _TIME = 'TIME';
    case _DATETIME = 'DATETIME';
    case _TIMESTAMP = 'TIMESTAMP';

    case _BOOLEAN = 'BOOLEAN';
    case _BIT = 'BIT';

    case _ENUM = 'ENUM';
    case _SET = 'SET';

    case _BINARY = 'BINARY';
    case _VARBINARY = 'VARBINARY';
    case _TINYBLOB = 'TINYBLOB';
    case _BLOB = 'BLOB';
    case _MEDIUMBLOB = 'MEDIUMBLOB';
    case _LONGBLOB = 'LONGBLOB';

    case _YEAR = 'YEAR';

    case _POINT = 'POINT';
    case _LINESTRING = 'LINESTRING';
    case _POLYGON = 'POLYGON';
    case _GEOMETRY = 'GEOMETRY';
    case _MULTIPOINT = 'MULTIPOINT';
    case _MULTILINESTRING = 'MULTILINESTRING';
    case _MULTIPOLYGON = 'MULTIPOLYGON';
    case _GEOMETRYCOLLECTION = 'GEOMETRYCOLLECTION';

    case _JSON = 'JSON';
}
