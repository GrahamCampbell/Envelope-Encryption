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

namespace GrahamCampbell\EnvelopeEncryption\Entities;

use ParagonIE\HiddenString\HiddenString;
use ValueError;

final class Input
{
    /**
     * @param non-empty-string $keyId
     */
    public function __construct(
        private readonly HiddenString $dataPlaintext,
        private readonly string $keyId,
    ) {
        if ('' === $keyId) {
            throw new ValueError(sprintf('%s(): Argument #2 ($keyId) must be non-empty', __METHOD__));
        }
    }

    public function getDataPlaintext(): HiddenString
    {
        return $this->dataPlaintext;
    }

    /**
     * @return non-empty-string
     */
    public function getKeyId(): string
    {
        return $this->keyId;
    }
}
