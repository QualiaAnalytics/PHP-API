<?php

use Qualia\Configuration\Questions;

class QuestionsTest extends TestCase
{

    public function testGetQuestions()
    {
        $questions = Questions::get($this->client);

        self::assertArrayHasKey('surveys', $questions);
        self::assertCount(2, $questions['surveys']);
        self::assertArrayHasKey('structure', $questions);
        self::assertArrayHasKey('q_1J75WdyBwVpwlJUM', $questions['structure'], "Date Question disappeared!");
        self::assertArrayHasKey('q_tVabQ3cUlwZTgQ10', $questions['structure'], "Checkbox Question disappeared!");
        self::assertArrayHasKey('q_KCyzOs7VqevWbEO0', $questions['structure'], "Name Question disappeared!");
        self::assertArrayHasKey('q_3RYJ4MpggyMFuU50', $questions['structure'], "Email Question disappeared!");
    }

}