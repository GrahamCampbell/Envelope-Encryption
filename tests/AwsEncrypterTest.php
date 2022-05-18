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

use AsyncAws\Kms\KmsClient;
use AsyncAws\Kms\Result\GenerateDataKeyResponse;
use LogicException;
use Mockery;
use ParagonIE\HiddenString\HiddenString;
use PHPUnit\Framework\TestCase;
use GrahamCampbell\EnvelopeEncryption\AwsKmsEncrypter;
use GrahamCampbell\EnvelopeEncryption\Contracts\EncrypterInterface;
use GrahamCampbell\EnvelopeEncryption\Entities\Envelope;
use GrahamCampbell\EnvelopeEncryption\Entities\Input;
use GrahamCampbell\EnvelopeEncryption\Exceptions\EncryptionFailedException;

class AwsEncrypterTest extends TestCase
{
    private const DATA_KEY_PLAINTEXT = 'n99arURXnTqajs7K6Ns59SIK5Nk9iqeXql554hn0hqA=';
    private const DATA_KEY_CIPHERTEXT = 'AQIDAHgOVj0wQc06jiZVGlQPyMjyGbHrbb02vc542KC6g2buTgGMuCXPak8K4nPgMVlv4zUyAAAAfjB8BgkqhkiG9w0BBwagbzBtAgEAMGgGCSqGSIb3DQEHATAeBglghkgBZQMEAS4wEQQMlqywgRHX4yrDKLmXAgEQgDuRm+tpHU7kp2s6YWtELD1W7tfXbuUZl3gAuuieT9UFLhGq35qqOAzU8MHnhXf6WrpzC+1mojFqBnm61A==';
    private const MAIN_KEY_ID = 'arn:aws:kms:us-east-1:111111111111:key/xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx';

    public function test_can_instantiate(): void
    {
        self::assertInstanceOf(EncrypterInterface::class, new AwsKmsEncrypter(Mockery::mock(KmsClient::class)));
    }

    public function test_can_encrypt(): void
    {
        $generateDataKeyResponse = Mockery::mock(GenerateDataKeyResponse::class);
        $generateDataKeyResponse->shouldReceive('getPlaintext')->once()->with()->andReturn(base64_decode(self::DATA_KEY_PLAINTEXT));
        $generateDataKeyResponse->shouldReceive('getCiphertextBlob')->once()->with()->andReturn(base64_decode(self::DATA_KEY_CIPHERTEXT));
        $generateDataKeyResponse->shouldReceive('getKeyId')->once()->with()->andReturn(self::MAIN_KEY_ID);

        $kmsClient = Mockery::mock(KmsClient::class);
        $kmsClient->shouldReceive('generateDataKey')->once()->andReturn($generateDataKeyResponse);

        $encrypter = new AwsKmsEncrypter($kmsClient);
        $envelope = $encrypter->encrypt(new Input(new HiddenString('hide me'), 'alias/acme/test'));

        self::assertInstanceOf(Envelope::class, $envelope);
        self::assertSame(self::MAIN_KEY_ID, $envelope->getKeyId());
    }

    public function test_encrypt_bad_key(): void
    {
        $kmsClient = Mockery::mock(KmsClient::class);
        $kmsClient->shouldReceive('generateDataKey')->once()->andThrow(new LogicException());

        $encrypter = new AwsKmsEncrypter($kmsClient);

        $this->expectException(EncryptionFailedException::class);
        $this->expectExceptionMessage('Failed to generate data key.');

        $encrypter->encrypt(new Input(new HiddenString('hide me'), 'alias/acme/test'));
    }

    public function test_encrypt_misc_failure(): void
    {
        $generateDataKeyResponse = Mockery::mock(GenerateDataKeyResponse::class);
        $generateDataKeyResponse->shouldReceive('getPlaintext')->once()->with()->andReturn(' ');
        $generateDataKeyResponse->shouldReceive('getCiphertextBlob')->once()->with()->andReturn(base64_decode(self::DATA_KEY_CIPHERTEXT));
        $generateDataKeyResponse->shouldReceive('getKeyId')->once()->with()->andReturn(self::MAIN_KEY_ID);

        $kmsClient = Mockery::mock(KmsClient::class);
        $kmsClient->shouldReceive('generateDataKey')->once()->andReturn($generateDataKeyResponse);

        $encrypter = new AwsKmsEncrypter($kmsClient);

        $this->expectException(EncryptionFailedException::class);
        $this->expectExceptionMessage('Failed to encrypt data plaintext.');

        $encrypter->encrypt(new Input(new HiddenString('hide me'), 'alias/acme/test'));
    }
}
