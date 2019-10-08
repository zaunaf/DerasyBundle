<?php
/**
 * Created by PhpStorm.
 * User: Abah
 * Date: 14/10/2018
 * Time: 20.05
 */

namespace Derasy\DerasyBundle\Generator;

/**
 * Class JavaType
 * Refer to:
 * https://agrosner.gitbooks.io/dbflow/content/TypeConverters.html
 * https://docs.microsoft.com/en-us/sql/connect/jdbc/using-basic-data-types?view=sql-server-2017
 *
 * @package Derasy\DerasyBundle\Generator
 */
class JavaType {

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

    const UUID_JAVA_TYPE          = 'UUID';
    const CHAR_JAVA_TYPE          = 'String';
    const VARCHAR_JAVA_TYPE       = 'String';
    const LONGVARCHAR_JAVA_TYPE   = 'String';
    const CLOB_JAVA_TYPE          = 'Byte';
    const CLOB_EMU_JAVA_TYPE      = 'Byte';
    const NUMERIC_JAVA_TYPE       = 'String';
    const DECIMAL_JAVA_TYPE       = 'String';
    const TINYINT_JAVA_TYPE       = 'Integer';
    const SMALLINT_JAVA_TYPE      = 'Integer';
    const INTEGER_JAVA_TYPE       = 'Integer';
    const BIGINT_JAVA_TYPE        = 'BigInteger';
    const REAL_JAVA_TYPE          = 'Double';
    const FLOAT_JAVA_TYPE         = 'Double';
    const DOUBLE_JAVA_TYPE        = 'Double';
    const BINARY_JAVA_TYPE        = 'Byte';
    const VARBINARY_JAVA_TYPE     = 'Byte';
    const LONGVARBINARY_JAVA_TYPE = 'Byte';
    const BLOB_JAVA_TYPE          = 'Blob';
    const BU_DATE_JAVA_TYPE       = 'String';
    const DATE_JAVA_TYPE          = 'Date';
    const TIME_JAVA_TYPE          = 'Time';
    const TIMESTAMP_JAVA_TYPE     = 'Date';
    const BU_TIMESTAMP_JAVA_TYPE  = 'Date';
    const BOOLEAN_JAVA_TYPE       = 'Boolean';
    const BOOLEAN_EMU_JAVA_TYPE   = 'Boolean';
    const OBJECT_JAVA_TYPE        = '';
    const PHP_ARRAY_JAVA_TYPE     = 'array';
    const ENUM_JAVA_TYPE          = 'Integer';
    const SET_JAVA_TYPE           = 'Integer';
    const GEOMETRY_JAVA_TYPE      = 'resource';
    const JSON_JAVA_TYPE          = 'String';

    /**
     * Mapping between Propel mapping types and PHP native types.
     *
     * @var array
     */
    private static $mappingToDoctrineMap = [
        self::UUID          => self::UUID_JAVA_TYPE,
        self::CHAR          => self::CHAR_JAVA_TYPE,
        self::VARCHAR       => self::VARCHAR_JAVA_TYPE,
        self::LONGVARCHAR   => self::LONGVARCHAR_JAVA_TYPE,
        self::CLOB          => self::CLOB_JAVA_TYPE,
        self::CLOB_EMU      => self::CLOB_EMU_JAVA_TYPE,
        self::NUMERIC       => self::NUMERIC_JAVA_TYPE,
        self::DECIMAL       => self::DECIMAL_JAVA_TYPE,
        self::TINYINT       => self::TINYINT_JAVA_TYPE,
        self::SMALLINT      => self::SMALLINT_JAVA_TYPE,
        self::INTEGER       => self::INTEGER_JAVA_TYPE,
        self::BIGINT        => self::BIGINT_JAVA_TYPE,
        self::REAL          => self::REAL_JAVA_TYPE,
        self::FLOAT         => self::FLOAT_JAVA_TYPE,
        self::DOUBLE        => self::DOUBLE_JAVA_TYPE,
        self::BINARY        => self::BINARY_JAVA_TYPE,
        self::VARBINARY     => self::VARBINARY_JAVA_TYPE,
        self::LONGVARBINARY => self::LONGVARBINARY_JAVA_TYPE,
        self::BLOB          => self::BLOB_JAVA_TYPE,
        self::DATE          => self::DATE_JAVA_TYPE,
        self::BU_DATE       => self::BU_DATE_JAVA_TYPE,
        self::TIME          => self::TIME_JAVA_TYPE,
        self::TIMESTAMP     => self::TIMESTAMP_JAVA_TYPE,
        self::BU_TIMESTAMP  => self::BU_TIMESTAMP_JAVA_TYPE,
        self::BOOLEAN       => self::BOOLEAN_JAVA_TYPE,
        self::BOOLEAN_EMU   => self::BOOLEAN_EMU_JAVA_TYPE,
        self::OBJECT        => self::OBJECT_JAVA_TYPE,
        self::PHP_ARRAY     => self::PHP_ARRAY_JAVA_TYPE,
        self::ENUM          => self::ENUM_JAVA_TYPE,
        self::SET           => self::SET_JAVA_TYPE,
        self::GEOMETRY      => self::GEOMETRY_JAVA_TYPE,
        self::JSON          => self::JSON_JAVA_TYPE,
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