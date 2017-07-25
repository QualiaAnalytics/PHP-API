<?php namespace Qualia;

use Httpful\Mime;
use Httpful\Request;

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

    private function __construct($survey, $token, $endpoint = 'api.qualiaanalytics.org')
    {
        $this->survey = $survey;
        $this->token  = $token;
        $this->endpoint = $endpoint;

        $template = Request::init()
            ->withStrictSSL()
            ->addHeader('Authorization', 'token')
            ->addHeader('User-Agent', 'Qualia API/PHP/'. PHP_VERSION . ' (' . PHP_OS . ')')
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

    public function post($url, $data)
    {
        Request::post("https://" . $this->endpoint . '/' . $url)
               ->body($data)
               ->send();
    }

    public function get($url)
    {
        Request::post("https://" . $this->endpoint . '/' . $url)
               ->body($data)
               ->send();
    }
}