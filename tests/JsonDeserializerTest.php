<?php

declare(strict_types=1);

/*
 * This file is part of Envelope Encryption.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\EnvelopEncryption;

use GrahamCampbell\EnvelopeEncryption\Contracts\DeserializerInterface;
use GrahamCampbell\EnvelopeEncryption\Entities\Envelope;
use GrahamCampbell\EnvelopeEncryption\Exceptions\InvalidPayloadException;
use GrahamCampbell\EnvelopeEncryption\JsonDeserializer;
use PHPUnit\Framework\TestCase;

class JsonDeserializerTest extends TestCase
{
    public function test_can_instantiate(): void
    {
        self::assertInstanceOf(DeserializerInterface::class, new JsonDeserializer());
    }

    /**
     * @return list<array{0: string}>
     */
    public static function provides_good_input(): array
    {
        return [
            [
                '{"data_ciphertext":"Zm9v","key_ciphertext":"YmFy","key_id":"key:id"}',
                new Envelope('foo', 'bar', 'key:id'),
            ],
        ];
    }

    /**
     * @dataProvider provides_good_input
     */
    public function test_deserialize_good_input(string $payload, Envelope $expected): void
    {
        $envelope = (new JsonDeserializer())->deserialize($payload);

        self::assertSame($expected->getDataCiphertext(), $envelope->getDataCiphertext());
        self::assertSame($expected->getKeyCiphertext(), $envelope->getKeyCiphertext());
        self::assertSame($expected->getKeyId(), $envelope->getKeyId());
    }

    /**
     * @return list<array{0: string}>
     */
    public static function provides_bad_input(): array
    {
        return [
            [''],
            ['{}'],
            ['[]'],
            ['false'],
            ['""'],
            ['{'],
            ['{"data_ciphertext":}'],
            ['{"data_ciphertext":null,"key_ciphertext":null,"key_id":null}'],
            ['{"data_ciphertext":"","key_ciphertext":"","key_id":""}'],
            ['{"data_ciphertext":"","key_ciphertext":123,"key_id":""}'],
            ['{"data_ciphertext":"Zm9v","key_ciphertext":"YmFy","key_id":""}'],
        ];
    }

    /**
     * @dataProvider provides_bad_input
     */
    public function test_deserialize_bad_input(string $payload): void
    {
        $this->expectException(InvalidPayloadException::class);

        (new JsonDeserializer())->deserialize($payload);
    }
}
