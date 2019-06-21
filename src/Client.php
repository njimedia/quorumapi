<?php

namespace NJIMedia\QuorumAPI;

/**
 * Quorum Public Affairs API Client
 *
 * @package Quorum
 *
 * @see https://www.quorum.us/api/
 */

/**
 * Main API class.
 */
class Client
{
    /**
     * The base URL for API requests, to which various endpoints will be appended.
     */
    const API_BASEURL = 'https://www.quorum.us/api';

    /**
    * The API Key is used in combination with account username for authentication.
    *
    * @since 1.0
    * @access protected
    * @var string $apiKey The API key for authenticating.
    */
    protected $apiKey = null;

    /**
     * The API Username is used in combination with key for authentication.
     *
     * @since 1.0
     * @access protected
     * @var string $apiUsername The API username for authenticating.
     */
    protected $apiUsername = null;

    /**
     * The Guzzle HTTP client object (or mock client, in tests).
     *
     * @since 1.0
     * @access protected
     * @var object $client The HTTP client object.
     */
    protected $client = null;

    /**
     * Constructor
     *
     * @since 1.0
     * @access public
     * @param object $client The HTTP client object.
     * @param string $username The API username to set.
     * @param string $key The API key to set.
     *
     * @return void
     */
    public function __construct($username, $key, $client = null)
    {
        // Let setters handle sanitization and validation of the values given.
        $keyIsValid = $this->setAPIKey($key);
        $userIsValid = $this->setAPIUsername($username);

        if (!$keyIsValid || !$userIsValid) {
            throw new \Exception('Quorum API Client credentials are invalid');
        }

        // Set up the Guzzle HTTP client or mock client for testing.
        if ($client) {
            // Use whatever client was given.
            $this->client = $client;
        } else {
            // No client given, default to regular Guzzle client.
            $this->client = new \GuzzleHttp\Client;
        }
    }

    /**
     * Setter for API Key value.
     *
     * @since 1.0
     * @access protected
     * @param string $key The API key value.
     */
    protected function setAPIKey($input)
    {

        // Strip bad characters.
        $safeInput= filter_var($input, FILTER_SANITIZE_STRIPPED);

        // Check that the safe value has only alpha-numeric values.
        if (ctype_alnum($safeInput)) {
        // Key looks valid. Accept it.
            $this->apiKey = $safeInput;
            return true;
        }

        // Key does not look valid, reject.
        return false;
    }

    /**
     * Setter for API Username value.
     *
     * @since 1.0
     * @access protected
     * @param string $key The API username value.
     */
    protected function setAPIUsername($input)
    {

        // Strip bad characters.
        $safeInput= filter_var($input, FILTER_SANITIZE_STRIPPED);

        // Check that the safe value has an expected number of characters.
        // Usernames may have dashes, plusses, and some other non-alphanum chars.
        if (strlen($safeInput) > 0 && strlen($safeInput) < 60) {
        // Key looks valid. Accept it.
            $this->apiUsername = $safeInput;
            return true;
        }

        // Key does not look valid, reject.
        return false;
    }

    /**
     * Check that the API is available.
     *
     * @return bool
     */
    public function validate()
    {
        // To validate, just make a simple API request and confirm 200 response header.
        try {
            $response = $this->getLists();
            if ($response && 200 === $response->getStatusCode()) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get custom tags (custom fields) from Quorum account.
     *
     * @return object
     */
    public function getCustomTags()
    {
        $query_params = [
            'username' => $this->apiUsername,
            'api_key'  => $this->apiKey,
        ];
        try {
            $response = $this->client->request(
                'GET',
                self::API_BASEURL . '/customtag/',
                ['query' => $query_params]
            );
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
        }
        return $response;
    }

    /**
     * Get lists from Quorum account.
     *
     * @return object
     */
    public function getLists()
    {
        $query_params = [
            'username' => $this->apiUsername,
            'api_key'  => $this->apiKey,
        ];
        try {
            $response = $this->client->request(
                'GET',
                self::API_BASEURL . '/list/',
                ['query' => $query_params]
            );
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
        }
        return $response;
    }

    /**
     * Create a new supporter.
     *
     * @param array $data Key/value pairs of supporter fields.
     *
     * @return object
     */
    public function createSupporter($data)
    {
        $query_params = [
            'username' => $this->apiUsername,
            'api_key'  => $this->apiKey,
        ];
        try {
            $response = $this->client->request(
                'POST',
                self::API_BASEURL . '/supporter/',
                [
                    'query' => $query_params,
                    'json'  => $data,
                ]
            );
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
        }
        return $response;
    }
}
