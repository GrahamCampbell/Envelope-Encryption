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

use ValueError;

final class Envelope
{
    /**
     * @param non-empty-string $keyId
     */
    public function __construct(
        private readonly string $dataCiphertext,
        private readonly string $keyCiphertext,
        private readonly string $keyId,
    ) {
        if ('' === $keyId) {
            throw new ValueError(sprintf('%s(): Argument #3 ($keyId) must be non-empty', __METHOD__));
        }
    }

    public function getDataCiphertext(): string
    {
        return $this->dataCiphertext;
    }

    public function getKeyCiphertext(): string
    {
        return $this->keyCiphertext;
    }

    /**
     * @return non-empty-string
     */
    public function getKeyId(): string
    {
        return $this->keyId;
    }
}
