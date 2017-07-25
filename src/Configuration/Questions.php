<?php namespace Qualia\Configuration;

use Qualia\Client;

class Questions
{
    /**
     * Get a configuration for a survey
     *
     * @param \Qualia\Client $client
     * @return \Qualia\Qualia
     */
    public static function get(Client $client)
    {

        return $client->get('surveys/' . $client->getSurveyId() . '/fields');
    }
}