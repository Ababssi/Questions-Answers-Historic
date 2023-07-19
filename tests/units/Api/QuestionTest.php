<?php

declare(strict_types=1);

namespace App\Tests\units\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class QuestionTest extends WebTestCase
{
    protected static int $questionId;

    /**
     * @dataProvider providePostQuestionData
     */
    public function testCreateQuestion(array $payload, int $expectedStatusCode, ?array $expectedError): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/questions',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertEquals($expectedStatusCode, $client->getResponse()->getStatusCode());
        if($expectedStatusCode === 200) {
            $response = json_decode($client->getResponse()->getContent(), true);
            self::$questionId = self::$questionId ?? $response['id'];
        }
        if ($expectedError !== null) {
            $this->assertJsonStringEqualsJsonString(json_encode($expectedError), $client->getResponse()->getContent());
        }
    }

    public static function providePostQuestionData(): array
    {
        return [
            [
                [
                    'title' => 'Question Test',
                    'status' => 'draft',
                    'promoted' => true,
                ],
                200,
                null
            ],
            [
                [
                    'status' => 'published',
                    'promoted' => true,
                ],
                400,
                ['errors' => ['Title should not be blank']]
            ],
            [
                [
                    'title' => 'Question Test',
                    'promoted' => true,
                ],
                400,
                ['errors' => ['Status should not be blank']]
            ],
            [
                [
                    'title' => 'Question Test',
                    'status' => 'draft',
                ],
                400,
                ['errors' => ['Promoted should not be blank']]
            ],
            [
                [
                    'title' => 'Question Test',
                    'status' => 'open',
                    'promoted' => true,
                ],
                400,
                ['errors' => ['The value you selected is not a valid choice.']]
            ],
        ];
    }

    /**
     * @dataProvider provideReadQuestionData
     */
    public function testReadQuestion(?int $id, int $expectedStatusCode): void
    {
        $client = static::createClient();
        $id = $id ?? self::$questionId;
        $client->request(
            'GET',
            '/questions/' . $id,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );
        $this->assertEquals($expectedStatusCode, $client->getResponse()->getStatusCode());
    }

    public static function provideReadQuestionData(): array
    {
        return [
            [
                null,
                200,
            ],
            [
                99,
                404,
            ]
        ];
    }

    /**
     * @dataProvider provideUpdateQuestionData
     */
    public function testUpdateQuestion(array $payload, int $expectedStatusCode, ?array $expectedError): void
    {
        $client = static::createClient();
        $client->request(
            'PUT',
            '/questions/'. self::$questionId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );
        $this->assertEquals($expectedStatusCode, $client->getResponse()->getStatusCode());

        if ($expectedError !== null) {
            $this->assertJsonStringEqualsJsonString(json_encode($expectedError), $client->getResponse()->getContent());
        }
    }

    public static function provideUpdateQuestionData(): array
    {
        return [
            [
                [
                    'title' => 'Question Test',
                    'status' => 'published',
                    'promoted' => true,
                ],
                200,
                null
            ],
            [
                [
                    'title' => 'Une Question Updated',
                    'status' => 'draft',
                    'promoted' => true,
                ],
                200,
                null
            ],
            [
                [
                    'status' => 'published',
                    'promoted' => true,
                ],
                400,
                ['error' => 'Missing parameters']
            ],
            [
                [
                    'status' => 'published',
                    'promoted' => true,
                ],
                400,
                ['error' => 'Missing parameters']
            ],
            [
                [
                    'title' => 'Une Question Updated',
                    'status' => 'published'
                ],
                400,
                ['error' => 'Missing parameters']
            ],
            [
                [
                    'title' => 'Question Updated',
                    'status' => 'draft',
                ],
                400,
                ['error' => 'Missing parameters']
            ],
            [
                [
                    'title' => 'Question Updated',
                    'status' => 'open',
                    'promoted' => true,
                ],
                400,
                ['errors' => "Object(App\\Entity\\Questions).status:\n    The value you selected is not a valid choice. (code 8e179f1b-97aa-4560-a02f-2a8b42e49df7)\n"]
            ],
            [
                [
                    'title' => 'Question Updated',
                    'status' => 'open',
                    'promoted' => 'true',
                ],
                400,
                ['error' => 'Promoted must be a boolean']
            ],
        ];
    }

    /**
     * @dataProvider providePostAnswersData
     */
    public function testCreateAnswers(array $payload, int $expectedStatusCode, ?array $expectedError): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/questions/'.self::$questionId.'/answers',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertEquals($expectedStatusCode, $client->getResponse()->getStatusCode());
        if ($expectedError !== null) {
            $this->assertJsonStringEqualsJsonString(json_encode($expectedError), $client->getResponse()->getContent());
        }
    }

    public static function providePostAnswersData(): array
    {
        return [
            [
                [
                    'channel' => 'bot',
                    'body' => 'test body 1',
                ],
                200,
                null
            ],
            [
                [
                    'channel' => 'faq',
                ],
                400,
                ['error' => 'Missing parameters']
            ],
            [
                [
                    'body' => 'test body 2',
                ],
                400,
                ['error' => 'Missing parameters']
            ],
            [
                [
                    'channel' => 'pop',
                    'body' => 'test body 3',
                ],
                400,
                ['error' => "Object(App\\Entity\\Answers).channel:\n    The value you selected is not a valid choice. (code 8e179f1b-97aa-4560-a02f-2a8b42e49df7)\n"]
            ],
        ];
    }

    /**
     * @dataProvider provideDeleteQuestionData
     */
    public function testDeleteQuestion(?int $id, int $expectedStatusCode): void
    {
        $client = static::createClient();
        $id = $id ?? self::$questionId;
        $client->request(
            'DELETE',
            '/questions/' . $id,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );
        $this->assertEquals($expectedStatusCode, $client->getResponse()->getStatusCode());
    }

    public static function provideDeleteQuestionData(): array
    {
        return [
            [
                null,
                200,
            ],
            [
                99,
                404,
            ]
        ];
    }
}