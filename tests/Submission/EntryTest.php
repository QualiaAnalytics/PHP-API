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
        self::setExpectedException("InvalidArgumentException");

        Entry::build($this->client)
                ->send();
    }

    public function allowsOnlyCorrectDateNotUnixTime()
    {
        self::setExpectedException("InvalidArgumentException");

        $id = uniqid('', true);

        Entry::build($this->client)
                ->uniqueId($id)
                ->date('q_1J75WdyBwVpwlJUM', time())
                ->send();
    }

    public function testAllowsOnlyCorrectDate()
    {
        self::setExpectedException("InvalidArgumentException");

        Entry::build($this->client)
                ->date('q_1J75WdyBwVpwlJUM', 'invalid')
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

    public function testSingleCheckbox()
    {
        $id = uniqid('', true);

        $response = Entry::build($this->client)
                            ->email('q_3RYJ4MpggyMFuU50', $id."+test@example.com")
                            ->response('q_tVabQ3cUlwZTgQ10', 'o_ACvo61cUuKXxD5C1')
                            ->send();

        self::assertEquals('success', $response['message']);
        self::assertArrayHasKey('id', $response);
    }

    public function testSubmitWithLanguage()
    {
        $response = Entry::build($this->client)
                            ->language('en')
                            ->date('q_1J75WdyBwVpwlJUM', date('Y-m-d'))
                            ->send();

        self::assertEquals('success', $response['message']);
        self::assertArrayHasKey('id', $response);
    }

    public function testSubmitWithInvalidLanguage()
    {
        self::setExpectedException("Qualia\Exceptions\RequestException");

        $response = Entry::build($this->client)
                            ->language('xy')
                            ->date('q_1J75WdyBwVpwlJUM', date('Y-m-d'))
                            ->send();
    }

    public function testWithAllFields()
    {
        $id = uniqid('', true);

        $response = Entry::build($this->client)
                ->uniqueId($id)
                ->email('q_3RYJ4MpggyMFuU50', $id."+test@example.com")
                ->name('q_KCyzOs7VqevWbEO0', "Unit", "Tester")
                ->date('q_1J75WdyBwVpwlJUM', date('Y-m-d'))
                ->response('q_KCyzOs7VqevWbEO0', ['o_wCcuY5a54YBeXLC1', 'o_ACvo61cUuKXxD5C1'])
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