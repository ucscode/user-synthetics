<?php

namespace Ucscode\SQuery;

interface SQueryInterface
{
    public const KEYWORD_INSERT = 'INSERT';
    public const KEYWORD_SELECT = 'SELECT';
    public const KEYWORD_UPDATE = 'UPDATE';
    public const KEYWORD_DELETE = 'DELETE';
    public const KEYWORD_VALUES = 'VALUES';
    public const KEYWORD_FROM = 'FROM';
    public const KEYWORD_JOIN = 'JOIN';
    public const KEYWORD_WHERE = 'WHERE';
    public const KEYWORD_GROUP_BY = 'GROUP BY';
    public const KEYWORD_HAVING = 'HAVING';
    public const KEYWORD_ORDER_BY = 'ORDER BY';
    public const KEYWORD_LIMIT = 'LIMIT';
    public const KEYWORD_OFFSET = 'OFFSET';
    public const KEYWORD_AND = 'AND';
    public const KEYWORD_OR = 'OR';
}
