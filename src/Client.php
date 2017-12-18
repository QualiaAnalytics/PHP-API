<?php namespace Qualia;

use Httpful\Mime;
use Httpful\Request;
use Qualia\Exceptions\ConnectionErrorException;
use Qualia\Exceptions\EmailExistsException;
use Qualia\Exceptions\RequestException;
use Qualia\Exceptions\ValidationException;

class Client
{
    /**
     * @var string
     */
    protected $survey;
    /**
     * @var string
     */
    protected $token;
    /**
     * @var string
     */
    private $endpoint;

    public function __construct($survey, $token, $endpoint = 'api.qualiaanalytics.org')
    {
        $this->survey = $survey;
        $this->token  = $token;
        $this->endpoint = $endpoint;

        $template = Request::init()
            ->withStrictSSL()
            ->addHeader('Authorization', 'token')
            ->addHeader('User-Agent', 'Qualia API/PHP/'. PHP_VERSION . ' (' . PHP_OS . ')')
            ->parseWith(function($body) {
                return json_decode($body, true);
            })
            ->sendsAndExpects(Mime::JSON);

        Request::ini($template);
    }

    /**
     * Get the provided survey
     *
     * @return string
     */
    public function getSurveyId()
    {
        return $this->survey;
    }

    public function post($url, $data = array(), $additional = array())
    {
        try {
            $response = Request::post("https://" . $this->endpoint . '/' . $url)->body(array_merge($additional, array(
                'api_token'     => $this->token,
                'data'          => $data,
            )))->send();
            $body = $response->body;
        } catch (\Httpful\Exception\ConnectionErrorException $e) {
            // rethrow the error.
            throw new ConnectionErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        if ($response->code === 422) {
            throw new ValidationException("Validation Exception: " . json_encode($body['errors']), 422, json_encode($body['errors']));
        }

        if (isset($body['status_code']) && $body['status_code'] == 208) {
            throw new EmailExistsException($body['id'], $response->code);
        }

        if ($response->code !== 200) {
            throw new RequestException($body['message'], $response->code);
        }

        return $body;
    }

    public function get($url)
    {
        $url = "https://" . $this->endpoint . '/' . $url . '?api_token=' . $this->token;

        echo("You may want to view this in your browser with enabled JSON Formatter or with REST API Client: $url");

        return Request::get($url)
                      ->send()->body;
    }
}