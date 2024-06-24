<?php

namespace Preseem;

class Preseem
{

    private $api_url = '';
    private $api_key = '';

    /**
     * __construct method
     *
     * @return void
     */
    public function __construct($url,$key)
    {
        global $api_url;
        $this->api_url = $url;
        $this->logger('DEBUG', 'API URL: ' . $this->api_url);
        global $api_key;
        $this->api_key = $key;
        $this->logger('DEBUG', 'API Key: ' . $this->api_key);
        global $api_responses;
        $this->api_responses = $api_responses;
    }

    private function getResponseMessage($verb, $method, $response_code)
    {
        if (isset($this->api_responses[$verb][$method][$response_code])) {
            return $this->api_responses[$verb][$method][$response_code];
        }
        return 'Unrecognized Response: ' . json_encode(array($verb, $method, $response_code));
    }

    /**
     * send the request to preseem api
     *
     * @param  mixed $object
     * @param  mixed $__URI
     * @param  mixed $action
     * @param  mixed $params
     * @return void
     */
    private function send($object = '', $__URI = '', $action = '', $params = array())
    {

        $method = 'GET';
        $headers = array();

        empty($object) && $this->logger('FATAL', 'Object not set');

        empty($__URI) && $this->logger('FATAL', 'URI not set');

        empty($action) && $this->logger('FATAL', 'Action not set');

        empty($this->api_url) && $this->logger('FATAL', 'Server not set. Please use Obj->setServer("server")');

        empty($this->api_key) && $this->logger('FATAL', 'API Key not set. Please use Obj->setAPIKey("your_key")');

        array_push($headers, 'Content-Type: application/json');

        switch ($action) {
            case 'LIST':
                $method = 'GET';
                break;
            case 'CREATE':
                $method = 'PUT';
                break;
            case 'DELETE':
                $method = 'DELETE';
                break;
            case 'GET':
                $method = 'GET';
                break;
            default:
                $this->logger('FATAL', "Invalid HTTP method: {$method}");
                return false;
                break;
        }

        /**
         * Inialize the cURL object
         */
        $ch = curl_init();

        /**
         * Set the URL we will be calling to
         */
        curl_setopt($ch, CURLOPT_URL, $this->api_url . $__URI);

        /**
         * Set Username & Password for Basic Auth
         */
        curl_setopt($ch, CURLOPT_USERPWD, $this->api_key . ':');

        /**
         * Set custom method
         */
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        /**
         * Setup cURL to have the body content when performing a PUT request
         */
        if ($method === 'PUT') {
            $payload = json_encode($params);
            array_push($headers, 'Content-Length: ' . strlen($payload));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }

        /**
         * Add headers
         */
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        /**
         * Set cURL to follow redirection Location header
         */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        /**
         * Execute request and capture response
         */
        $data = curl_exec($ch);

        /**
         * Capture last response code
         */
        $response_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        /**
         * Close and destroy cURL object
         */
        curl_close($ch);

        $this->logger('DEBUG', json_encode(['object' => $object, 'action' => $action, 'response_code' => $response_code, 'data' => $data]));

        if ($response_code != 200) {
            $this->logger('INFO', $this->getResponseMessage($object, $action, $response_code) . ' - Data Returned: ' . trim($data));
        }

        return json_decode($data);
    }

    public function setServer($server = '')
    {
        $this->__server = $server;
        return true;
    }

    public function setAPIKey($APIKey = '')
    {
        $this->api_key = $APIKey;
        return true;
    }

    /**
     * list method will list all of your entities in preseem (sites, packages, access points, etc)
     *
     * @param  mixed $object
     * @param  mixed $page
     * @param  mixed $limit
     * @return void
     */

    public function list($object, $page = 1, $limit = 500) {
        $this->logger('INFO', json_encode(['object' => $object]));
        return $this->send($object, ($object . '?' . http_build_query(['page' => $page, 'limit' => $limit])), 'LIST');
    }

    /**
     * dump method will dump all the entities without pagination
     */

     public function dump($object) {
        $page = 1;
        $array = [];
        while(true) {
            $res = $this->list($object,$page);
            $array = array_merge($array,$res->data);
            if($page++ > $res->paginator->page_count) break;
        }
        return($array);
 
    }

    public function create($object, $params)
    {
        $this->logger('INFO', json_encode(['object' => $object, $params]));
        return $this->send($object, ($object . '/' . rawurlencode($params['id'])), 'CREATE', $params);
    }
    /**
     * delete method will delete your entity in preseem
     *
     * @param  mixed $object
     * @param  mixed $id
     * @return void
     */
    public function delete($object, $id)
    {
        $this->logger('INFO', json_encode(['object' => $object, 'id' => $id]));
        return $this->send($object, ($object . '/' . rawurlencode($id)), 'DELETE');
    }

    /**
     * get method will list your entity in preseem
     *
     * @param  mixed $object
     * @param  mixed $id
     * @return void
     */
    public function get($object, $id)
    {
        $this->logger('INFO', json_encode(['object' => $object, 'id' => $id]));
        if (($results = $this->send($object, ($object . '/' . rawurlencode($id)), 'GET')) === false) {
            $this->logger('FATAL', ucfirst($object) . " ID: {$id}");
        }
        return $results;
    }


    /**
     * Logs your messages into storage/logs/preseem.log filr
     *
     * @param  mixed $error_level
     * @param  mixed $message
     * @return void
     */
    public function logger($error_level = 'info', $message)
    {
        global $timestamp;
        global $pid;
        global $logfile;

        $pid = getmypid();

        $logfile = storage_path('logs/preseem.log');

        if (is_array($message)) {
            $message = json_encode($message);
        }

        $suffix = date('Y/m/d H:i:s') . " [ {$pid} ] - [ {$error_level} ] - ";

        $line = $suffix . $message . PHP_EOL;

        if (defined('DEBUG')) {
            if (strtoupper($error_level) === 'DEBUG') {
                if (DEBUG) {
                    echo $line;
                } else {
                    $line = '';
                }
            }
        }

        !empty($line) && file_put_contents($logfile, $line, FILE_APPEND);

        if (strtoupper($error_level) === 'FATAL') {
            echo $line;
            foreach (debug_backtrace() as $k => $v) {
                $line = $suffix . "#{$k}  " . (isset($v['class']) ? $v['class'] . '->' : '') . "{$v['function']}(" . json_encode($v['args']) . ") called at [{$v['file']}:{$v['line']}]" . PHP_EOL;
                #      echo $line;
                file_put_contents($logfile, $line, FILE_APPEND);
            }
        }
    }

}
