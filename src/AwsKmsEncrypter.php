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

use AsyncAws\Kms\Enum\DataKeySpec;
use AsyncAws\Kms\Input\GenerateDataKeyRequest;
use AsyncAws\Kms\KmsClient;
use Exception;
use LogicException;
use ParagonIE\Halite\Symmetric\Crypto;
use ParagonIE\Halite\Symmetric\EncryptionKey;
use ParagonIE\HiddenString\HiddenString;
use GrahamCampbell\EnvelopeEncryption\Contracts\EncrypterInterface;
use GrahamCampbell\EnvelopeEncryption\Entities\Envelope;
use GrahamCampbell\EnvelopeEncryption\Entities\Input;
use GrahamCampbell\EnvelopeEncryption\Exceptions\EncryptionFailedException;

final class AwsKmsEncrypter implements EncrypterInterface
{
    public function __construct(
        private KmsClient $client
    ) {
    }

    /**
     * @throws EncryptionFailedException
     */
    public function encrypt(Input $input): Envelope
    {
        try {
            $dataKey = $this->client->generateDataKey(
                new GenerateDataKeyRequest([
                    'KeyId' => $input->getKeyId(),
                    'KeySpec' => DataKeySpec::AES_256,
                ]),
            );

            $keyPlaintext = new HiddenString($dataKey->getPlaintext() ?: throw new LogicException('Data key plaintext not set.'));
            $keyCiphertext = $dataKey->getCiphertextBlob() ?: throw new LogicException('Data key ciphertext not set.');
            $keyId = $dataKey->getKeyId() ?: throw new LogicException('KMS key id not set.');
        } catch (Exception $e) {
            throw new EncryptionFailedException('Failed to generate data key.', 0, $e);
        } finally {
            unset($dataKey);
        }

        try {
            $dataCiphertext = Crypto::encrypt(
                $input->getDataPlaintext(),
                new EncryptionKey($keyPlaintext),
                true,
            );
        } catch (Exception $e) {
            throw new EncryptionFailedException('Failed to encrypt data plaintext.', 0, $e);
        } finally {
            unset($keyPlaintext);
        }

        return new Envelope(
            $dataCiphertext,
            $keyCiphertext,
            $keyId,
        );
    }
}
