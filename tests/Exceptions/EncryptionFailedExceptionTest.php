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

namespace GrahamCampbell\Tests\EnvelopEncryption\Exceptions;

use Exception;
use GrahamCampbell\EnvelopeEncryption\Exceptions\EncryptionFailedException;
use PHPUnit\Framework\TestCase;

class EncryptionFailedExceptionTest extends TestCase
{
    public function test_can_instantiate(): void
    {
        self::assertInstanceOf(
            Exception::class,
            new EncryptionFailedException(),
        );
    }
}
