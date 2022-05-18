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

use AsyncAws\Kms\Enum\EncryptionAlgorithmSpec;
use AsyncAws\Kms\Input\DecryptRequest;
use AsyncAws\Kms\KmsClient;
use Exception;
use LogicException;
use ParagonIE\Halite\Symmetric\Crypto;
use ParagonIE\Halite\Symmetric\EncryptionKey;
use ParagonIE\HiddenString\HiddenString;
use GrahamCampbell\EnvelopeEncryption\Contracts\DecrypterInterface;
use GrahamCampbell\EnvelopeEncryption\Entities\Envelope;
use GrahamCampbell\EnvelopeEncryption\Exceptions\DecryptionFailedException;

final class AwsKmsDecrypter implements DecrypterInterface
{
    public function __construct(
        private KmsClient $client
    ) {
    }

    /**
     * @throws DecryptionFailedException
     */
    public function decrypt(Envelope $envelope): HiddenString
    {
        try {
            $dataKey = $this->client->decrypt(
                new DecryptRequest([
                    'CiphertextBlob' => $envelope->getKeyCiphertext(),
                    'EncryptionAlgorithm' => EncryptionAlgorithmSpec::SYMMETRIC_DEFAULT,
                    'KeyId' => $envelope->getKeyId(),
                ]),
            );

            $keyPlaintext = new HiddenString($dataKey->getPlaintext() ?: throw new LogicException('Data key plaintext not set.'));
        } catch (Exception $e) {
            throw new DecryptionFailedException('Failed to decrypt data key.', 0, $e);
        } finally {
            unset($dataKey);
        }

        try {
            return Crypto::decrypt(
                $envelope->getDataCiphertext(),
                new EncryptionKey($keyPlaintext),
                true,
            );
        } catch (Exception $e) {
            throw new DecryptionFailedException('Failed to decrypt data ciphertext.', 0, $e);
        } finally {
            unset($keyPlaintext);
        }
    }
}
