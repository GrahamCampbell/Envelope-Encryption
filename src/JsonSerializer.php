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

use GrahamCampbell\EnvelopeEncryption\Contracts\SerializerInterface;
use GrahamCampbell\EnvelopeEncryption\Entities\Envelope;
use GrahamCampbell\EnvelopeEncryption\Utilities\Base64;

final class JsonSerializer implements SerializerInterface
{
    public function serialize(Envelope $envelope): string
    {
        return json_encode([
            'data_ciphertext' => Base64::encode($envelope->getDataCiphertext()),
            'key_ciphertext'  => Base64::encode($envelope->getKeyCiphertext()),
            'key_id'          => $envelope->getKeyId(),
        ]);
    }
}
