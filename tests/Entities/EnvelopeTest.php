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

namespace GrahamCampbell\Tests\EnvelopEncryption\Entities;

use PHPUnit\Framework\TestCase;
use ValueError;
use GrahamCampbell\EnvelopeEncryption\Entities\Envelope;

class EnvelopeTest extends TestCase
{
    public function test_accessors(): void
    {
        $envelope = new Envelope('foo', 'bar', 'keyid');

        self::assertSame('foo', $envelope->getDataCiphertext());
        self::assertSame('bar', $envelope->getKeyCiphertext());
        self::assertSame('keyid', $envelope->getKeyId());
    }

    public function test_empty_key_id(): void
    {
        $this->expectException(ValueError::class);

        new Envelope('foo', 'bar', '');
    }
}
