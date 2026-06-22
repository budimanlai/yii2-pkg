<?php
namespace budimanlai\yii2pkg\traits;

use yii\web\BadRequestHttpException;
use budimanlai\yii2pkg\helpers\QueryHelper;

/**
 * Controller trait that provides database query shortcuts and uniform HTTP error helpers.
 *
 * Mix this trait into any Yii2 controller to get concise wrappers around
 * {@see QueryHelper} methods and standardized error-response helpers.
 *
 * @package budimanlai\yii2pkg\traits
 * @author  Budiman Lai <budiman.lai@gmail.com>
 */
trait BaseController {

    /**
     * Check if a record matching the given SQL query exists in the database.
     *
     * @param  string $sql    Raw SQL query
     * @param  array  $params Bound parameters for the query
     * @return bool   True if a matching record exists, false otherwise
     */
    public function isExists(string $sql, array $params = []): bool {
        return QueryHelper::isExists($sql, $params);
    }

    /**
     * Select a single record from the database.
     *
     * @param  string     $sql    Raw SQL query
     * @param  array      $params Bound parameters for the query
     * @return array|null Matched row as an associative array, or null if not found
     */
    public function queryOne(string $sql, array $params = []): array|null {
        return QueryHelper::queryOne($sql, $params);
    }

    /**
     * Select all matching records from the database.
     *
     * @param  string $sql    Raw SQL query
     * @param  array  $params Bound parameters for the query
     * @return array  List of matched rows, each as an associative array
     */
    public function queryAll(string $sql, array $params = []): array {
        return QueryHelper::queryAll($sql, $params);
    }

    /**
     * Get the value of the first column in the first row of the result.
     *
     * Useful for scalar queries such as `COUNT`, `SUM`, or `MAX`.
     *
     * @param  string $sql    Raw SQL query
     * @param  array  $params Bound parameters for the query
     * @return mixed  Scalar value, or null if no record is found
     */
    public function queryScalar(string $sql, array $params = []): mixed {
        return QueryHelper::queryScalar($sql, $params);
    }

    /**
     * Execute a non-query SQL statement (e.g. `UPDATE`, `DELETE`, custom `INSERT`).
     *
     * @param  string $sql    Raw SQL statement to execute
     * @param  array  $params Bound parameters for the statement
     * @return void
     */
    public function queryExecute(string $sql, array $params = []): void {
        QueryHelper::queryExecute($sql, $params);
    }

    /**
     * Update records in a table using Yii2's query builder.
     *
     * @param  string       $table     Table name
     * @param  array        $columns   Associative array of column => value to update
     * @param  array|string $condition WHERE condition (string or Yii2 array format)
     * @param  array        $params    Additional bound parameters for the condition
     * @return void
     */
    public function queryUpdate(string $table, array $columns, array|string $condition, array $params = []): void {
        QueryHelper::queryUpdate($table, $columns, $condition, $params);
    }

    /**
     * Insert a single record into a table using Yii2's query builder.
     *
     * @param  string $table   Table name
     * @param  array  $columns Associative array of column => value to insert
     * @return void
     */
    public function queryInsert(string $table, array $columns): void {
        QueryHelper::queryInsert($table, $columns);
    }

    /**
     * Insert multiple records into a table in a single query using Yii2's query builder.
     *
     * @param  string   $table   Table name
     * @param  string[] $columns List of column names
     * @param  array    $rows    List of rows to insert; each row is an array of values
     *                           ordered to match `$columns`
     * @return void
     */
    public function queryBatchInsert(string $table, array $columns, array $rows): void {
        QueryHelper::queryBatchInsert($table, $columns, $rows);
    }

    /**
     * Select one record and lock the row with `FOR UPDATE` to prevent concurrent
     * modifications until the current transaction completes.
     *
     * @param  string     $sql    Raw SQL query (without `LIMIT` or `FOR UPDATE`)
     * @param  array      $params Bound parameters for the query
     * @return array|null Matched row, or null if not found
     */
    public function queryForUpdate(string $sql, array $params = []): array|null {
        return QueryHelper::queryForUpdate($sql, $params);
    }

    /**
     * Throw a {@see BadRequestHttpException} with the given message.
     *
     * Use this to return a uniform error response from a controller action.
     *
     * @param  string $message Error message to return to the client
     * @param  int    $code    Application-specific error code (default: 999)
     * @return never
     * @throws BadRequestHttpException Always
     */
    public function asError(string $message, int $code = 999): never {
        throw new BadRequestHttpException($message, $code);
    }

    /**
     * Throw a {@see BadRequestHttpException} with the first validation error from a Yii2 model.
     *
     * Use this after a failed `$model->validate()` to return the error to the client.
     *
     * @param  \yii\base\Model $model Model whose validation errors will be returned
     * @return never
     * @throws BadRequestHttpException Always
     */
    public function asErrorModel(\yii\base\Model $model): never {
        throw new BadRequestHttpException(QueryHelper::getErrorModel($model), 888);
    }

    /**
     * Validate that required fields are present and non-empty in the given input.
     *
     * Calls {@see asError()} immediately on the first missing field.
     *
     * @param  array|object $json  Input data to validate (e.g. request body)
     * @param  string[]     $field List of required field keys
     * @return void
     * @throws BadRequestHttpException If `$json` is empty or a required field is missing
     */
    public function requiredField(array|object $json, array $field): void {
        if (empty($json)) {
            $this->asError('Invalid paramenter', 999);
        }
        foreach ($field as $key) {
            if (empty($json[$key])) {
                $this->asError("Invalid paramenter. Required {$key}", 999);
            }
        }
    }
}
