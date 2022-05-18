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

namespace GrahamCampbell\EnvelopeEncryption;

use GrahamCampbell\EnvelopeEncryption\Contracts\DeserializerInterface;
use GrahamCampbell\EnvelopeEncryption\Entities\Envelope;
use GrahamCampbell\EnvelopeEncryption\Exceptions\InvalidPayloadException;

final class JsonDeserializer implements DeserializerInterface
{
    /**
     * @throws InvalidPayloadException
     */
    public function deserialize(string $payload): Envelope
    {
        $decoded = json_decode($payload, true);

        if (!is_array($decoded)) {
            throw new InvalidPayloadException('Payload was not a valid JSON object.');
        }

        $dataCiphertext = $decoded['data_ciphertext'] ?? null;

        if (!is_string($dataCiphertext)) {
            throw new InvalidPayloadException('Payload "data_ciphertext" not a string.');
        }

        $keyCiphertext = $decoded['key_ciphertext'] ?? null;

        if (!is_string($keyCiphertext)) {
            throw new InvalidPayloadException('Payload "key_ciphertext" not a string.');
        }

        $keyId = $decoded['key_id'] ?? null;

        if (!is_string($keyId) || '' === $keyId) {
            throw new InvalidPayloadException('Payload "key_id" not a non-empty-string.');
        }

        return new Envelope(
            base64_decode($dataCiphertext),
            base64_decode($keyCiphertext),
            $keyId,
        );
    }
}
