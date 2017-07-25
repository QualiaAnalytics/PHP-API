<?php namespace Qualia\Submission;

/*
 * This file is part of Qualia Analytics.
 *
 * (c) Qualia Analytics Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Qualia\Client;
use Qualia\Util;

class Entry
{
    /**
     * @var array
     */
    protected $additional;
    /**
     * @var array
     */
    private $data;
    /**
     * @var \Qualia\Client
     */
    private $client;


    private function __construct(Client $client) {
        $this->data         = array();
        $this->additional   = array(
            'mark_complete' => 0,
        );
        $this->client = $client;
    }

    /**
     * Build a configuration
     *
     * @param \Qualia\Client $client
     * @return \Qualia\Submission\Entry
     */
    public static function build(Client $client)
    {
        return (new Entry($client));
    }

    /**
     * Provide an unique identifier for an entry. It can be either string or integer e.g. incremental id from your database
     * This ensures you are not submitting the entry twice and even if you do submit it, it will update the previous entry
     * rather than creating a new one.
     *
     * @param $identifier
     * @return $this
     */
    public function uniqueId($identifier)
    {
        $this->additional['unique_id'] = $identifier;

        return $this;
    }

    /**
     * Append name question
     *
     * @param      $id          Question identifier
     * @param      $firstName   First name
     * @param null $lastName    Last name
     * @return $this
     */
    public function name($id, $firstName = null, $lastName = null) {

        $this->data[$id] = array(
            'first_name' => $firstName,
            'last_name'  => $lastName,
        );

        return $this;
    }

    /**
     * Append email question
     *
     * @param $id           Question identifier
     * @param $address      E-mail address
     * @return $this
     */
    public function email($id, $address) {
        $this->data[$id] = $address;

        return $this;
    }


    /**
     * Append date question
     *
     * @param $id           Question identifier
     * @param $date         Date in Y-m-d format
     * @return $this
     */
    public function date($id, $date)
    {
        if (! Util::isValidDate($date)) {
            throw new \InvalidArgumentException("Date must be provided in Y-m-d format");
        }

        $this->data[$id] = $date;

        return $this;
    }

    /**
     * Append any other type of response
     *
     * @param $id           Question identifier
     * @param $response     Response to a question
     * @return $this
     */
    public function response($id, $response)
    {
        $this->data[$id] = $response;

        return $this;
    }

    /**
     * Once we have everything added, we can submit.
     *
     * @param bool $markComplete   If you would like submit the survey and mark it as completed,please set this field to true,
     *                             otherwise, survey will not be marked as completed and an email will be sent to the user
     *                             asking to complete the rest of the survey.
     *
     *                             Depending on survey distribution channels this may vary:
     *                             Institution may have an Enrollment form enabled where it collects some data on site,
     *                             some through this API, some maybe shared in social media. If that's the case, must likely
     *                             you do not need to mark it as completed.
     *
     *                             However,
     *                             If all responses are collected via this API and second survey is send later, you may
     *                             need to mark it as completed.
     * @return array
     * @throws \Qualia\Exceptions\RequestException
     */
    public function send($markComplete = false)
    {
        if ($markComplete) {
            $this->additional['mark_complete'] = 1;
        }

        return $this->client->post('survey/'. $this->client->getSurveyId() .'/entries', $this->data, $this->additional);
    }

}