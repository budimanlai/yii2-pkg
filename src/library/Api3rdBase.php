<?php
namespace budimanlai\yii2pkg\library;

use Yii;
use yii\httpclient\Client;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\base\Exception;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

class Api3rdBase {
    public string $baseUrl;
    public string $api_key;
    public string $category;
    public string $path = '/';
    public int|string $user_id;

    private array $_request = [];
    private array $_headers = [];
    private mixed $_response = null;
    private string $_targetUrl = '';

    /**
     * Return the default HTTP headers to be sent with every request.
     * Override this method in subclasses to add custom headers (e.g. Authorization).
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
     * Looks for 'message' at the root level first, then under 'meta.message'.
     *
     * @param array $data The decoded response body
     * @return string The error message
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
     * Send a PATCH request to the API.
     *
     * @param string $endpoint The API endpoint path (appended to `$baseUrl . $path`)
     * @param array  $params   Request body as an associative array
     * @return mixed Decoded response data on success
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
     * Send a DELETE request to the API.
     *
     * @param string $endpoint The API endpoint path (appended to `$baseUrl . $path`)
     * @param array  $params   Request body as an associative array
     * @return mixed Decoded response data on success
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
     * @param string $endpoint The API endpoint path (appended to `$baseUrl . $path`)
     * @param array  $params   Request body as an associative array (sent as JSON)
     * @return mixed Decoded response data on success
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
     * Send a POST request with a form-encoded body (multipart/form-data) to the API.
     * Use this instead of `post()` when uploading files or sending form fields.
     *
     * @param string $endpoint The API endpoint path (appended to `$baseUrl . $path`)
     * @param array  $params   Request body as an associative array (sent as form data)
     * @return mixed Decoded response data on success
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
     * Query parameters are automatically appended to the URL.
     *
     * @param string $endpoint The API endpoint path (appended to `$baseUrl . $path`)
     * @param array  $params   Query parameters as an associative array
     * @return mixed Decoded response data on success
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

    /** @return array The headers sent in the last request */
    public function getHeaderReq(): array { return $this->_headers; }

    /** @return array The request body/params sent in the last request */
    public function getRequest(): array { return $this->_request; }

    /** @return mixed The decoded response data from the last request */
    public function getResponse(): mixed { return $this->_response; }

    /**
     * Insert a log entry into `api_3rd_log` before sending the request.
     *
     * @param int|string $reff_id  The ID of the entity associated with this request (e.g. user or order ID)
     * @param string     $method   HTTP method name in lowercase (e.g. 'get', 'post')
     * @return int|string The inserted log row ID
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
     * @param int|string $log_id  The log row ID returned by `addLog()`
     * @param string     $message The exception message
     * @param float|null $latency Request duration in seconds, or null if unavailable
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
     * @param int|string $log_id   The log row ID returned by `addLog()`
     * @param mixed      $response The decoded response body
     * @param float|null $latency  Request duration in seconds, or null if unavailable
     */
    public function addResponse(int|string $log_id, mixed $response, ?float $latency = null): void {
        Yii::$app->db->createCommand('UPDATE api_3rd_log SET response_log = :RESP, latency = :L WHERE id = :ID', [
            ':RESP' => Json::encode($response),
            ':ID' => $log_id,
            ':L' => $latency,
        ])->execute();
    }
}
