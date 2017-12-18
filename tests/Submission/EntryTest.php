<?php

use Qualia\Client;
use Qualia\Exceptions\ConnectionErrorException;
use Qualia\Exceptions\EmailExistsException;
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

    public function testInvalidSurvey()
    {
        self::setExpectedException("\\Qualia\\Exceptions\\RequestException");

        Entry::build(new Client('aaa', 'cT6hCqyxD8MQ0k6gfYwWBt3lIMexpsLQ'))
                        ->email('q_tVabQ3cUlwZTgQ10', 'unit+test@example.com')
                        ->send();

    }

    public function testServerError()
    {
        try {
            Entry::build(new Client('aaa', 'cT6hCqyxD8MQ0k6gfYwWBt3lIMexpsLQ', 'api.qualiaanalytics.test'))
                 ->email('q_tVabQ3cUlwZTgQ10', 'unit+test@example.com')
                 ->send();
        } catch (ConnectionErrorException $e) {
            self::assertTrue(true);
        } catch (RequestException $e) {
            self::assertFalse(true);
        }
    }

    public function testValidationException()
    {
        try {
            Entry::build($this->client)
                 ->email('dadad', 'unit+test@example.com')
                 ->send();
        } catch (ConnectionErrorException $e) {
            self::assertTrue(false);
        } catch (EmailExistsException $e) {
            self::assertTrue(false);
        } catch (RequestException $e) {
            self::assertTrue(true);
            self::assertEquals(400, $e->getCode());
            self::assertEquals("Invalid Question: dadad", $e->getMessage());
        }
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
        $response = Entry::build($this->client)
                            ->language('xy')
                            ->date('q_1J75WdyBwVpwlJUM', date('Y-m-d'))
                            ->send();

        // will allow to submit and sets language as default one
    }

    public function testSubmitDuplicateEmail()
    {
        $id = uniqid('', true);

        $response = Entry::build($this->client)
                            ->email('q_3RYJ4MpggyMFuU50', $id."+test@example.com")
                            ->send();

        // allowDuplicates will allow to submit
        $response = Entry::build($this->client)
                         ->allowDuplicates()
                         ->email('q_3RYJ4MpggyMFuU50', $id."+test@example.com")
                         ->send();

        try {
            // will throw an exception
            $response = Entry::build($this->client)
                             ->email('q_3RYJ4MpggyMFuU50', $id."+test@example.com")
                             ->send();
        } catch (EmailExistsException $e) {
            self::assertTrue(strlen($e->getEntryId()) > 0);
            return;
        }

        self::assertFalse(true, "Exception was not thrown");
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