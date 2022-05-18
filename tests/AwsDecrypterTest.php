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
use AsyncAws\Kms\Result\DecryptResponse;
use GrahamCampbell\EnvelopeEncryption\AwsKmsDecrypter;
use GrahamCampbell\EnvelopeEncryption\Contracts\DecrypterInterface;
use GrahamCampbell\EnvelopeEncryption\Entities\Envelope;
use GrahamCampbell\EnvelopeEncryption\Exceptions\DecryptionFailedException;
use LogicException;
use Mockery;
use ParagonIE\HiddenString\HiddenString;
use PHPUnit\Framework\TestCase;

class AwsDecrypterTest extends TestCase
{
    private const DATA_KEY_PLAINTEXT = 'n99arURXnTqajs7K6Ns59SIK5Nk9iqeXql554hn0hqA=';
    private const DATA_CIPHERTEXT = 'MUIFAOlHeWeMX8N5ZZuoxyuRYZos9DTcy5fh3a1O/trj9ZDV4seM2wwDbZqYy11w9mxQWOyLkYDA4jyywpOfgeXUgsBBO4+4n6BLXkgUKezZMRuqnIj+CN7QGspFbkgzLS0V4H74D+4YaOTnzjfNBj93OsSSwCDrbrrC8QbSRJYqlLQ=';
    private const DATA_KEY_CIPHERTEXT = 'AQIDAHgOVj0wQc06jiZVGlQPyMjyGbHrbb02vc542KC6g2buTgGMuCXPak8K4nPgMVlv4zUyAAAAfjB8BgkqhkiG9w0BBwagbzBtAgEAMGgGCSqGSIb3DQEHATAeBglghkgBZQMEAS4wEQQMlqywgRHX4yrDKLmXAgEQgDuRm+tpHU7kp2s6YWtELD1W7tfXbuUZl3gAuuieT9UFLhGq35qqOAzU8MHnhXf6WrpzC+1mojFqBnm61A==';
    private const MAIN_KEY_ID = 'arn:aws:kms:us-east-1:111111111111:key/xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx';

    public function test_can_instantiate(): void
    {
        self::assertInstanceOf(DecrypterInterface::class, new AwsKmsDecrypter(Mockery::mock(KmsClient::class)));
    }

    public function test_can_decrypt(): void
    {
        $decryptResponse = Mockery::mock(DecryptResponse::class);
        $decryptResponse->shouldReceive('getPlaintext')->once()->with()->andReturn(base64_decode(self::DATA_KEY_PLAINTEXT));

        $kmsClient = Mockery::mock(KmsClient::class);
        $kmsClient->shouldReceive('decrypt')->once()->andReturn($decryptResponse);

        $decrypter = new AwsKmsDecrypter($kmsClient);
        $result = $decrypter->decrypt(new Envelope(base64_decode(self::DATA_CIPHERTEXT), base64_decode(self::DATA_KEY_CIPHERTEXT), self::MAIN_KEY_ID));

        self::assertInstanceOf(HiddenString::class, $result);
        self::assertSame('hide me', $result->getString());
    }

    public function test_decrypt_bad_key(): void
    {
        $kmsClient = Mockery::mock(KmsClient::class);
        $kmsClient->shouldReceive('decrypt')->once()->andThrow(new LogicException());

        $decrypter = new AwsKmsDecrypter($kmsClient);

        $this->expectException(DecryptionFailedException::class);
        $this->expectExceptionMessage('Failed to decrypt data key.');

        $decrypter->decrypt(new Envelope(base64_decode(self::DATA_CIPHERTEXT), base64_decode(self::DATA_KEY_CIPHERTEXT), self::MAIN_KEY_ID));
    }

    public function test_decrypt_misc_failure(): void
    {
        $decryptResponse = Mockery::mock(DecryptResponse::class);
        $decryptResponse->shouldReceive('getPlaintext')->once()->with()->andReturn(' ');

        $kmsClient = Mockery::mock(KmsClient::class);
        $kmsClient->shouldReceive('decrypt')->once()->andReturn($decryptResponse);

        $decrypter = new AwsKmsDecrypter($kmsClient);

        $this->expectException(DecryptionFailedException::class);
        $this->expectExceptionMessage('Failed to decrypt data ciphertext.');

        $decrypter->decrypt(new Envelope(base64_decode(self::DATA_CIPHERTEXT), base64_decode(self::DATA_KEY_CIPHERTEXT), self::MAIN_KEY_ID));
    }
}
