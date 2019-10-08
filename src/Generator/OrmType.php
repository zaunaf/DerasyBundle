<?php
/**
 * Created by PhpStorm.
 * User: Abah
 * Date: 14/10/2018
 * Time: 20.05
 */

namespace Derasy\DerasyBundle\Generator;

class OrmType {

    const UUID          = 'UUID';
    const CHAR          = 'CHAR';
    const VARCHAR       = 'VARCHAR';
    const LONGVARCHAR   = 'LONGVARCHAR';
    const CLOB          = 'CLOB';
    const CLOB_EMU      = 'CLOB_EMU';
    const NUMERIC       = 'NUMERIC';
    const DECIMAL       = 'DECIMAL';
    const TINYINT       = 'TINYINT';
    const SMALLINT      = 'SMALLINT';
    const INTEGER       = 'INTEGER';
    const BIGINT        = 'BIGINT';
    const REAL          = 'REAL';
    const FLOAT         = 'FLOAT';
    const DOUBLE        = 'DOUBLE';
    const BINARY        = 'BINARY';
    const VARBINARY     = 'VARBINARY';
    const LONGVARBINARY = 'LONGVARBINARY';
    const BLOB          = 'BLOB';
    const DATE          = 'DATE';
    const TIME          = 'TIME';
    const TIMESTAMP     = 'TIMESTAMP';
    const BU_DATE       = 'BU_DATE';
    const BU_TIMESTAMP  = 'BU_TIMESTAMP';
    const BOOLEAN       = 'BOOLEAN';
    const BOOLEAN_EMU   = 'BOOLEAN_EMU';
    const OBJECT        = 'OBJECT';
    const PHP_ARRAY     = 'ARRAY';
    const ENUM          = 'ENUM';
    const SET           = 'SET';
    const GEOMETRY      = 'GEOMETRY';
    const JSON          = 'JSON';

    const UUID_DOCTRINE_TYPE          = 'guid';
    const CHAR_DOCTRINE_TYPE          = 'string';
    const VARCHAR_DOCTRINE_TYPE       = 'string';
    const LONGVARCHAR_DOCTRINE_TYPE   = 'text';
    const CLOB_DOCTRINE_TYPE          = 'string';
    const CLOB_EMU_DOCTRINE_TYPE      = 'resource';
    const NUMERIC_DOCTRINE_TYPE       = 'string';
    const DECIMAL_DOCTRINE_TYPE       = 'string';
    const TINYINT_DOCTRINE_TYPE       = 'smallint';
    const SMALLINT_DOCTRINE_TYPE      = 'smallint';
    const INTEGER_DOCTRINE_TYPE       = 'integer';
    const BIGINT_DOCTRINE_TYPE        = 'bigint';
    const REAL_DOCTRINE_TYPE          = 'double';
    const FLOAT_DOCTRINE_TYPE         = 'double';
    const DOUBLE_DOCTRINE_TYPE        = 'double';
    const BINARY_DOCTRINE_TYPE        = 'string';
    const VARBINARY_DOCTRINE_TYPE     = 'string';
    const LONGVARBINARY_DOCTRINE_TYPE = 'string';
    const BLOB_DOCTRINE_TYPE          = 'resource';
    const BU_DATE_DOCTRINE_TYPE       = 'string';
    const DATE_DOCTRINE_TYPE          = 'string';
    const TIME_DOCTRINE_TYPE          = 'string';
    const TIMESTAMP_DOCTRINE_TYPE     = 'datetime';
    const BU_TIMESTAMP_DOCTRINE_TYPE  = 'datetime';
    const BOOLEAN_DOCTRINE_TYPE       = 'boolean';
    const BOOLEAN_EMU_DOCTRINE_TYPE   = 'boolean';
    const OBJECT_DOCTRINE_TYPE        = '';
    const PHP_ARRAY_DOCTRINE_TYPE     = 'array';
    const ENUM_DOCTRINE_TYPE          = 'int';
    const SET_DOCTRINE_TYPE           = 'int';
    const GEOMETRY_DOCTRINE_TYPE      = 'resource';
    const JSON_DOCTRINE_TYPE          = 'string';

    /**
     * Mapping between Propel mapping types and PHP native types.
     *
     * @var array
     */
    private static $mappingToDoctrineMap = [
        self::UUID          => self::UUID_DOCTRINE_TYPE,
        self::CHAR          => self::CHAR_DOCTRINE_TYPE,
        self::VARCHAR       => self::VARCHAR_DOCTRINE_TYPE,
        self::LONGVARCHAR   => self::LONGVARCHAR_DOCTRINE_TYPE,
        self::CLOB          => self::CLOB_DOCTRINE_TYPE,
        self::CLOB_EMU      => self::CLOB_EMU_DOCTRINE_TYPE,
        self::NUMERIC       => self::NUMERIC_DOCTRINE_TYPE,
        self::DECIMAL       => self::DECIMAL_DOCTRINE_TYPE,
        self::TINYINT       => self::TINYINT_DOCTRINE_TYPE,
        self::SMALLINT      => self::SMALLINT_DOCTRINE_TYPE,
        self::INTEGER       => self::INTEGER_DOCTRINE_TYPE,
        self::BIGINT        => self::BIGINT_DOCTRINE_TYPE,
        self::REAL          => self::REAL_DOCTRINE_TYPE,
        self::FLOAT         => self::FLOAT_DOCTRINE_TYPE,
        self::DOUBLE        => self::DOUBLE_DOCTRINE_TYPE,
        self::BINARY        => self::BINARY_DOCTRINE_TYPE,
        self::VARBINARY     => self::VARBINARY_DOCTRINE_TYPE,
        self::LONGVARBINARY => self::LONGVARBINARY_DOCTRINE_TYPE,
        self::BLOB          => self::BLOB_DOCTRINE_TYPE,
        self::DATE          => self::DATE_DOCTRINE_TYPE,
        self::BU_DATE       => self::BU_DATE_DOCTRINE_TYPE,
        self::TIME          => self::TIME_DOCTRINE_TYPE,
        self::TIMESTAMP     => self::TIMESTAMP_DOCTRINE_TYPE,
        self::BU_TIMESTAMP  => self::BU_TIMESTAMP_DOCTRINE_TYPE,
        self::BOOLEAN       => self::BOOLEAN_DOCTRINE_TYPE,
        self::BOOLEAN_EMU   => self::BOOLEAN_EMU_DOCTRINE_TYPE,
        self::OBJECT        => self::OBJECT_DOCTRINE_TYPE,
        self::PHP_ARRAY     => self::PHP_ARRAY_DOCTRINE_TYPE,
        self::ENUM          => self::ENUM_DOCTRINE_TYPE,
        self::SET           => self::SET_DOCTRINE_TYPE,
        self::GEOMETRY      => self::GEOMETRY_DOCTRINE_TYPE,
        self::JSON          => self::JSON_DOCTRINE_TYPE,
    ];

    /**
     * Returns the native PHP type which corresponds to the
     * mapping type provided. Use in the base object class generation.
     *
     * @param  string $mappingType
     * @return string
     */
    public static function getDoctrineType($mappingType)
    {
        return self::$mappingToDoctrineMap[$mappingType];
    }

}