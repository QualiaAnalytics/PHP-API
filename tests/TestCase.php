<?php
/**
 * @property \Qualia\Client client
 */
abstract class TestCase extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        parent::setUp();

        // Initialize client
        $this->client = new \Qualia\Client('597768e728d8f1508f6d9f62', 'cT6hCqyxD8MQ0k6gfYwWBt3lIMexpsLQ');

    }

}