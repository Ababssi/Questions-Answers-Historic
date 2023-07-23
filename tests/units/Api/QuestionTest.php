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
                ['errors' => ['Title should not be blank']]
            ],
            [
                [
                    'title' => 'Une autre question Updated',
                    'promoted' => true,
                ],
                400,
                ['errors' => ['Status should not be blank']]
            ],
            [
                [
                    'title' => 'Une Question Updated',
                    'status' => 'published'
                ],
                400,
                ['errors' => ['Promoted should not be blank']]
            ],
            [
                [
                    'title' => 'Question Updated',
                    'status' => 'open',
                    'promoted' => 'true',
                ],
                400,
                ['errors' => ['The value you selected is not a valid choice.']]
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
                ['errors' => ['Body should not be blank']]
            ],
            [
                [
                    'body' => 'test body 2',
                ],
                400,
                ['errors' => ['Channel should not be blank']]
            ],
            [
                [
                    'channel' => 'pop',
                    'body' => 'test body 3',
                ],
                400,
                ['errors' => ['The selected choice is invalid.']]
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