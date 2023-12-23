<?php

namespace Ucscode\SQuery;

interface SQueryInterface
{
    public const FILTER_IGNORE = 1;
    public const FILTER_BACKTICK = 3;
    public const FILTER_QUOTE = 6;

    public const IS_NULL = '@47a619b0';
    public const IS_NOT_NULL = '@edf5fe23';

    public const TYPE_INSERT = 'INSERT'; //C
    public const TYPE_SELECT = 'SELECT'; //R
    public const TYPE_UPDATE = 'UPDATE'; //U
    public const TYPE_DELETE = 'DELETE'; //D

    /**
     * The order of this constants below is important to render the correct SQL syntax
     * Please do not re-organize it to avoid undesirable error
     */
    public const SECTION_DELETE = 'DELETE';
    public const SECTION_UPDATE = 'UPDATE';
    public const SECTION_INSERT = 'INSERT';
    public const SECTION_VALUES = 'VALUES';
    public const SECTION_SELECT = 'SELECT';
    public const SECTION_FROM = 'FROM';
    public const SECTION_JOIN = 'JOIN';
    public const SECTION_WHERE = 'WHERE';
    public const SECTION_GROUP_BY = 'GROUP BY';
    public const SECTION_HAVING = 'HAVING';
    public const SECTION_ORDER_BY = 'ORDER BY';
    public const SECTION_LIMIT = 'LIMIT';
    public const SECTION_FINAL = 'FINAL';

}
