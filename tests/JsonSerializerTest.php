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

use GrahamCampbell\EnvelopeEncryption\Contracts\SerializerInterface;
use GrahamCampbell\EnvelopeEncryption\Entities\Envelope;
use GrahamCampbell\EnvelopeEncryption\JsonSerializer;
use PHPUnit\Framework\TestCase;

class JsonSerializerTest extends TestCase
{
    public function test_can_instantiate(): void
    {
        self::assertInstanceOf(SerializerInterface::class, new JsonSerializer());
    }

    public function test_serialize(): void
    {
        self::assertSame(
            '{"data_ciphertext":"Zm9v","key_ciphertext":"YmFy","key_id":"keyid"}',
            (new JsonSerializer())->serialize(new Envelope('foo', 'bar', 'keyid')),
        );
    }
}
