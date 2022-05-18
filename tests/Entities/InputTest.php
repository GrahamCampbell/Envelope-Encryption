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

use GrahamCampbell\EnvelopeEncryption\Entities\Input;
use ParagonIE\HiddenString\HiddenString;
use PHPUnit\Framework\TestCase;
use ValueError;

class InputTest extends TestCase
{
    public function test_accessors(): void
    {
        $dataPlaintext = new HiddenString('plaintext');

        $input = new Input($dataPlaintext, 'keyid');

        self::assertSame($dataPlaintext, $input->getDataPlaintext());
        self::assertSame($dataPlaintext->getString(), $input->getDataPlaintext()->getString());
        self::assertSame('keyid', $input->getKeyId());
    }

    public function test_empty_key_id(): void
    {
        $this->expectException(ValueError::class);

        new Input(new HiddenString('plaintext'), '');
    }
}
