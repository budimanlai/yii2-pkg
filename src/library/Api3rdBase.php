<?php
namespace budimanlai\yii2pkg\library;

use Yii;
use yii\httpclient\Client;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\base\Exception;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

/**
 * Base class for integrating with third-party REST APIs.
 *
 * Provides standard HTTP methods (GET, POST, PUT, PATCH, DELETE) and automatically
 * logs every request and response to the `api_3rd_log` table for auditing and debugging.
 * Extend this class for each API integration and override {@see getHeaders()} to supply
 * authentication headers.
 *
 * @package budimanlai\yii2pkg\library
 * @author  Budiman Lai <budiman.lai@gmail.com>
 */
class Api3rdBase {

    /** @var string Base URL of the API (e.g. 'https://api.example.com') */
    public string $baseUrl;

    /** @var string API key used for authentication */
    public string $api_key;

    /** @var string Category label recorded in the log (e.g. 'payment', 'shipping') */
    public string $category;

    /** @var string Path prefix prepended to every endpoint */
    public string $path = '/';

    /** @var int|string ID of the user or entity associated with the request, recorded in the log */
    public int|string $user_id;

    /** @var array Request body or query params sent in the last request */
    private array $_request = [];

    /** @var array HTTP headers sent in the last request */
    private array $_headers = [];

    /** @var mixed Decoded response data from the last request */
    private mixed $_response = null;

    /** @var string Full URL used in the last request */
    private string $_targetUrl = '';

    /**
     * Return the default HTTP headers sent with every request.
     *
     * Override in subclasses to add custom headers such as `Authorization`.
     *
     * @return array Associative array of header name => value
     */
    public function getHeaders(): array {
        return [
            'content-type' => 'application/json',
        ];
    }

    /**
     * Extract a human-readable error message from an API response body.
     *
     * Looks for `message` at the root level first, then under `meta.message`.
     *
     * @param  array  $data Decoded response body
     * @return string Error message
     */
    public function parseError(array $data): string {
        if (isset($data['message'])) {
            $message = ArrayHelper::getValue($data, 'message');
        } else if (isset($data['meta']['message'])) {
            $message = ArrayHelper::getValue($data, 'meta.message');
        } else {
            $message = 'Something error happend';
        }

        return $message;
    }

    /**
     * Send a PATCH request with a JSON body to the API.
     *
     * Use for partial resource updates. For full replacement use {@see put()}.
     *
     * @param  string $endpoint API endpoint path (appended to `$baseUrl . $path`)
     * @param  array  $params   Request body as an associative array
     * @return mixed  Decoded response data on success
     * @throws \Exception If the request fails or the API returns a non-2xx status
     */
    public function patch(string $endpoint, array $params = []): mixed {
        $this->_headers = $this->getHeaders();
        $this->_request = $params;
        $this->_targetUrl = $this->baseUrl . $this->path . $endpoint;

        $start_time = microtime(true);
        $log_id = $this->addLog($this->user_id, 'patch');
        try {
            $client = new Client([
                'transport' => 'yii\httpclient\CurlTransport',
            ]);
            $response = $client->createRequest()
                ->setFormat(Client::FORMAT_JSON)
                ->setMethod('PATCH')
                ->addHeaders($this->_headers)
                ->setData($this->_request)
                ->setUrl($this->_targetUrl)
                ->send();

            $latency = microtime(true) - $start_time;
            $this->_response = $response->data;
            $this->addResponse($log_id, $response->data, $latency);

            if ($response->isOk) {
                return $response->data;
            } else {
                throw new \Exception($this->parseError($response->data));
            }
        } catch (Exception | InvalidArgumentException | BadRequestHttpException $e) {
            $this->addException($log_id, $e->getMessage(), microtime(true) - $start_time);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Send a PUT request with a JSON body to the API.
     *
     * Use for full resource replacement. For partial updates use {@see patch()}.
     *
     * @param  string $endpoint API endpoint path (appended to `$baseUrl . $path`)
     * @param  array  $params   Request body as an associative array
     * @return mixed  Decoded response data on success
     * @throws \Exception If the request fails or the API returns a non-2xx status
     */
    public function put(string $endpoint, array $params = []): mixed {
        $this->_headers = $this->getHeaders();
        $this->_request = $params;
        $this->_targetUrl = $this->baseUrl . $this->path . $endpoint;

        $start_time = microtime(true);
        $log_id = $this->addLog($this->user_id, 'put');
        try {
            $client = new Client([
                'transport' => 'yii\httpclient\CurlTransport',
            ]);
            $response = $client->createRequest()
                ->setFormat(Client::FORMAT_JSON)
                ->setMethod('PUT')
                ->addHeaders($this->_headers)
                ->setData($this->_request)
                ->setUrl($this->_targetUrl)
                ->send();

            $latency = microtime(true) - $start_time;
            $this->_response = $response->data;
            $this->addResponse($log_id, $response->data, $latency);

            if ($response->isOk) {
                return $response->data;
            } else {
                throw new \Exception($this->parseError($response->data));
            }
        } catch (Exception | InvalidArgumentException | BadRequestHttpException $e) {
            $this->addException($log_id, $e->getMessage(), microtime(true) - $start_time);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Send a DELETE request to the API.
     *
     * @param  string $endpoint API endpoint path (appended to `$baseUrl . $path`)
     * @param  array  $params   Request body as an associative array
     * @return mixed  Decoded response data on success
     * @throws \Exception If the request fails or the API returns a non-2xx status
     */
    public function delete(string $endpoint, array $params = []): mixed {
        $this->_headers = $this->getHeaders();
        $this->_request = $params;
        $this->_targetUrl = $this->baseUrl . $this->path . $endpoint;

        $start_time = microtime(true);
        $log_id = $this->addLog($this->user_id, 'delete');
        try {
            $client = new Client([
                'transport' => 'yii\httpclient\CurlTransport',
            ]);
            $response = $client->createRequest()
                ->setFormat(Client::FORMAT_JSON)
                ->setMethod('DELETE')
                ->addHeaders($this->_headers)
                ->setData($this->_request)
                ->setUrl($this->baseUrl . $this->path . $endpoint)
                ->send();

            $latency = microtime(true) - $start_time;
            $this->_response = $response->data;
            $this->addResponse($log_id, $response->data, $latency);

            if ($response->isOk) {
                return $response->data;
            } else {
                throw new \Exception($this->parseError($response->data));
            }
        } catch (Exception | InvalidArgumentException $e) {
            $this->addException($log_id, $e->getMessage(), microtime(true) - $start_time);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Send a POST request with a JSON body to the API.
     *
     * @param  string $endpoint API endpoint path (appended to `$baseUrl . $path`)
     * @param  array  $params   Request body as an associative array (sent as JSON)
     * @return mixed  Decoded response data on success
     * @throws \Exception If the request fails or the API returns a non-2xx status
     */
    public function post(string $endpoint, array $params): mixed {
        $this->_headers = $this->getHeaders();
        $this->_request = $params;
        $this->_targetUrl = $this->baseUrl . $this->path . $endpoint;

        $start_time = microtime(true);
        $log_id = $this->addLog($this->user_id, 'post');
        try {
            $client = new Client([
                'transport' => 'yii\httpclient\CurlTransport',
            ]);
            $response = $client->createRequest()
                ->setFormat(Client::FORMAT_JSON)
                ->setMethod('POST')
                ->addHeaders($this->_headers)
                ->setData($this->_request)
                ->setUrl($this->baseUrl . $this->path . $endpoint)
                ->send();

            $latency = microtime(true) - $start_time;
            $this->_response = $response->data;
            $this->addResponse($log_id, $response->data, $latency);

            if ($response->isOk) {
                return $response->data;
            } else {
                throw new Exception($this->parseError($response->data));
            }
        } catch (Exception | InvalidArgumentException | BadRequestHttpException $e) {
            $this->addException($log_id, $e->getMessage(), microtime(true) - $start_time);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Send a POST request with a form-encoded body to the API.
     *
     * Use instead of {@see post()} when uploading files or sending form fields
     * (multipart/form-data).
     *
     * @param  string $endpoint API endpoint path (appended to `$baseUrl . $path`)
     * @param  array  $params   Request body as an associative array (sent as form data)
     * @return mixed  Decoded response data on success
     * @throws \Exception If the request fails or the API returns a non-2xx status
     */
    public function postForm(string $endpoint, array $params): mixed {
        $this->_headers = $this->getHeaders();
        $this->_request = $params;
        $this->_targetUrl = $this->baseUrl . $this->path . $endpoint;

        $start_time = microtime(true);
        $log_id = $this->addLog($this->user_id, 'post');
        try {
            $client = new Client([
                'transport' => 'yii\httpclient\CurlTransport',
            ]);
            $response = $client->createRequest()
                ->setMethod('POST')
                ->addHeaders($this->_headers)
                ->setData($this->_request)
                ->setUrl($this->baseUrl . $this->path . $endpoint)
                ->send();

            $latency = microtime(true) - $start_time;
            $this->_response = $response->data;
            $this->addResponse($log_id, $response->data, $latency);

            if ($response->isOk) {
                return $response->data;
            } else {
                throw new Exception($this->parseError($response->data));
            }
        } catch (Exception | InvalidArgumentException | BadRequestHttpException $e) {
            $this->addException($log_id, $e->getMessage(), microtime(true) - $start_time);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Send a GET request to the API.
     *
     * Query parameters are appended to the URL automatically.
     *
     * @param  string $endpoint API endpoint path (appended to `$baseUrl . $path`)
     * @param  array  $params   Query parameters as an associative array
     * @return mixed  Decoded response data on success
     * @throws \Exception If the request fails or the API returns a non-2xx status
     */
    public function get(string $endpoint, array $params = []): mixed {
        $this->_headers = $this->getHeaders();
        $this->_request = $params;
        $this->_targetUrl = $this->baseUrl . $this->path . $endpoint;

        if (!empty($params)) {
            $query = parse_url($this->_targetUrl, PHP_URL_QUERY);
            if ($query) {
                $this->_targetUrl .= '&' . http_build_query($params);
            } else {
                $this->_targetUrl .= '?' . http_build_query($params);
            }
        }

        $start_time = microtime(true);
        $log_id = $this->addLog($this->user_id, 'get');

        try {
            $client = new Client([
                'transport' => 'yii\httpclient\CurlTransport',
            ]);
            $response = $client->createRequest()
                ->setFormat(Client::FORMAT_JSON)
                ->setMethod('GET')
                ->addHeaders($this->_headers)
                ->setUrl($this->_targetUrl)
                ->send();

            $latency = microtime(true) - $start_time;
            $this->_response = $response->data;
            $this->addResponse($log_id, $response->data, $latency);

            if ($response->isOk) {
                return $response->data;
            } else {
                throw new \Exception($this->parseError($response->data));
            }
        } catch (Exception | InvalidArgumentException | BadRequestHttpException $e) {
            $this->addException($log_id, $e->getMessage(), microtime(true) - $start_time);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Return the HTTP headers sent in the last request.
     *
     * @return array
     */
    public function getHeaderReq(): array { return $this->_headers; }

    /**
     * Return the request body or query params sent in the last request.
     *
     * @return array
     */
    public function getRequest(): array { return $this->_request; }

    /**
     * Return the decoded response data from the last request.
     *
     * @return mixed
     */
    public function getResponse(): mixed { return $this->_response; }

    /**
     * Insert a log entry into `api_3rd_log` before sending the request.
     *
     * @param  int|string $reff_id ID of the entity associated with the request (e.g. user or order ID)
     * @param  string     $method  HTTP method in lowercase (e.g. 'get', 'post')
     * @return int|string Inserted log row ID
     */
    public function addLog(int|string $reff_id, string $method): int|string {
        Yii::$app->db->createCommand()->insert('api_3rd_log', [
            'category' => $this->category,
            'created_datetime' => date('Y-m-d H:i:s'),
            'method' => $method,
            'url' => $this->_targetUrl,
            'reff_id' => $reff_id,
            'headers' => Json::encode($this->_headers),
            'request_log' => Json::encode($this->_request),
        ])->execute();

        return Yii::$app->db->getLastInsertID();
    }

    /**
     * Update the log entry with the exception message when a request fails.
     *
     * @param  int|string $log_id  Log row ID returned by {@see addLog()}
     * @param  string     $message Exception message
     * @param  float|null $latency Request duration in seconds
     * @return void
     */
    public function addException(int|string $log_id, string $message, ?float $latency = null): void {
        Yii::$app->db->createCommand('UPDATE api_3rd_log SET response_log = :RESP, latency = :L WHERE id = :ID', [
            ':RESP' => Json::encode(['message' => $message]),
            ':ID' => $log_id,
            ':L' => $latency,
        ])->execute();
    }

    /**
     * Update the log entry with the API response data after a successful request.
     *
     * @param  int|string $log_id   Log row ID returned by {@see addLog()}
     * @param  mixed      $response Decoded response body
     * @param  float|null $latency  Request duration in seconds
     * @return void
     */
    public function addResponse(int|string $log_id, mixed $response, ?float $latency = null): void {
        Yii::$app->db->createCommand('UPDATE api_3rd_log SET response_log = :RESP, latency = :L WHERE id = :ID', [
            ':RESP' => Json::encode($response),
            ':ID' => $log_id,
            ':L' => $latency,
        ])->execute();
    }
}
