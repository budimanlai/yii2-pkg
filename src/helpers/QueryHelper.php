<?php
/*
 * @author Budiman Lai <budiman.lai@gmail.com>
 * @created_at 2023-07-12 14:32:15
 */
namespace budimanlai\yii2pkg\helpers;

use Yii;

class QueryHelper {

    /**
     * Collect and concatenate all first error messages from a Yii2 model
     * into a single comma-separated string.
     *
     * @param \yii\base\Model $model The model whose errors will be extracted
     * @return string Comma-separated error messages
     */
    public static function getErrorModel(\yii\base\Model $model): string {
        $msg = [];
        foreach ($model->getFirstErrors() as $message) {
            $msg[] = $message;
        }

        return implode(', ', $msg);
    }

    /**
     * Select one record and lock the row with `FOR UPDATE` to prevent
     * concurrent modifications until the current transaction completes.
     *
     * @param string $sql    Raw SQL query (without `LIMIT` or `FOR UPDATE`)
     * @param array  $params Bound parameters for the query
     * @return array|null The matched row, or null if not found
     */
    public static function queryForUpdate(string $sql, array $params = []): array|null {
        return self::queryOne("{$sql} LIMIT 1 FOR UPDATE", $params);
    }

    /**
     * Check if at least one record matching the query exists.
     *
     * @param string $sql    Raw SQL query
     * @param array  $params Bound parameters for the query
     * @return bool True if a matching record exists, false otherwise
     */
    public static function isExists(string $sql, array $params = []): bool {
        $model = self::queryOne($sql, $params);
        return !empty($model);
    }

    /**
     * Select a single record from the database.
     *
     * @param string $sql    Raw SQL query
     * @param array  $params Bound parameters for the query
     * @return array|null The matched row as an associative array, or null if not found
     */
    public static function queryOne(string $sql, array $params = []): array|null {
        return Yii::$app->db->createCommand($sql, $params)->queryOne();
    }

    /**
     * Select all matching records from the database.
     *
     * @param string $sql    Raw SQL query
     * @param array  $params Bound parameters for the query
     * @return array List of matched rows, each as an associative array
     */
    public static function queryAll(string $sql, array $params = []): array {
        return Yii::$app->db->createCommand($sql, $params)->queryAll();
    }

    /**
     * Get the value of the first column in the first row of the result.
     * Useful for scalar queries such as `COUNT`, `SUM`, or `MAX`.
     *
     * @param string $sql    Raw SQL query
     * @param array  $params Bound parameters for the query
     * @return mixed The scalar value, or null if no record is found
     */
    public static function queryScalar(string $sql, array $params = []): mixed {
        return Yii::$app->db->createCommand($sql, $params)->queryScalar();
    }

    /**
     * Execute a non-query SQL statement (e.g. `UPDATE`, `DELETE`, custom `INSERT`).
     *
     * @param string $sql    Raw SQL statement to execute
     * @param array  $params Bound parameters for the statement
     */
    public static function queryExecute(string $sql, array $params = []): void {
        Yii::$app->db->createCommand($sql, $params)->execute();
    }

    /**
     * Update records in a table using Yii2's query builder.
     *
     * @param string       $table     Table name
     * @param array        $columns   Associative array of column => value to update
     * @param array|string $condition WHERE condition (string or Yii2 array format)
     * @param array        $params    Additional bound parameters for the condition
     */
    public static function queryUpdate(string $table, array $columns, array|string $condition, array $params = []): void {
        Yii::$app->db->createCommand()->update($table, $columns, $condition, $params)->execute();
    }

    /**
     * Insert a single record into a table using Yii2's query builder.
     *
     * @param string $table   Table name
     * @param array  $columns Associative array of column => value to insert
     */
    public static function queryInsert(string $table, array $columns): void {
        Yii::$app->db->createCommand()->insert($table, $columns)->execute();
    }

    /**
     * Insert multiple records into a table in a single query using Yii2's query builder.
     *
     * @param string   $table   Table name
     * @param string[] $columns List of column names
     * @param array    $rows    List of rows to insert, each row is an array of values
     *                          ordered to match `$columns`
     */
    public static function queryBatchInsert(string $table, array $columns, array $rows): void {
        Yii::$app->db->createCommand()->batchInsert($table, $columns, $rows)->execute();
    }
}
