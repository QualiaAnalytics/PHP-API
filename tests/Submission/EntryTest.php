<?php

use Qualia\Client;
use Qualia\Exceptions\RequestException;
use Qualia\Submission\Entry;

class EntryTest extends TestCase
{

    public function testInvalidCredentials()
    {
        self::setExpectedException("\\Qualia\\Exceptions\\RequestException");

        Entry::build(new Client('597768e728d8f1508f6d9f62', 'invalid-credentials'))
                     ->email('q_tVabQ3cUlwZTgQ10', 'unit+test@example.com')
                     ->send();

    }

    public function testFailsWithoutData()
    {
        self::setExpectedException("\\Qualia\\Exceptions\\RequestException");

        $id = uniqid('', true);

        Entry::build($this->client)
             ->uniqueId($id)
             ->send();
    }

    public function testCreatesEntryWithEmail()
    {
        $id = uniqid('', true);

        $response = Entry::build($this->client)
             ->uniqueId($id)
             ->email('q_3RYJ4MpggyMFuU50', $id."+test@example.com")
             ->send();

        self::assertEquals('success', $response['message']);
        self::assertArrayHasKey('id', $response);
    }

    public function testWithAllFields()
    {
        $id = uniqid('', true);

        $response = Entry::build($this->client)
             ->uniqueId($id)
             ->email('q_3RYJ4MpggyMFuU50', $id."+test@example.com")
             ->name('q_KCyzOs7VqevWbEO0', "Unit", "Tester")
             ->date('q_1J75WdyBwVpwlJUM', date('Y-m-d'))
             ->send();

        self::assertEquals('success', $response['message']);
        self::assertArrayHasKey('id', $response);
    }

    public function testCreatesEntryWithEmailAndMarksCompleted()
    {
        $id = uniqid('', true);

        $response = Entry::build($this->client)
             ->uniqueId($id)
             ->email('q_3RYJ4MpggyMFuU50', $id."+test@example.com")
             ->send(true);

        self::assertEquals('success', $response['message']);
        self::assertArrayHasKey('id', $response);
    }
}