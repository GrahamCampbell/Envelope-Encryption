# Envelope Encryption

This package was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell), and provides symmetric envelope encryption using AWS KMS. Feel free to check out the [change log](CHANGELOG.md), [releases](https://github.com/GrahamCampbell/Envelope-Encryption/releases), [security policy](https://github.com/GrahamCampbell/Envelope-Encryption/security/policy), [license](LICENSE), [code of conduct](.github/CODE_OF_CONDUCT.md), and [contribution guidelines](.github/CONTRIBUTING.md). This package is not affiliated with or endorsed by AWS. Amazon Web Services and AWS are trademarks of Amazon.com, Inc. or its affiliates.

<p align="center">
<a href="https://github.com/GrahamCampbell/Envelope-Encryption/actions?query=workflow%3ATests"><img src="https://img.shields.io/github/workflow/status/GrahamCampbell/Envelope-Encryption/Tests?label=Tests&style=flat-square" alt="Build Status"></img></a>
<a href="https://github.styleci.io/repos/493842297"><img src="https://github.styleci.io/repos/493842297/shield" alt="StyleCI Status"></img></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-brightgreen?style=flat-square" alt="Software License"></img></a>
<a href="https://packagist.org/packages/graham-campbell/envelope-encryption"><img src="https://img.shields.io/packagist/dt/graham-campbell/envelope-encryption?style=flat-square" alt="Packagist Downloads"></img></a>
<a href="https://github.com/GrahamCampbell/Envelope-Encryption/releases"><img src="https://img.shields.io/github/release/GrahamCampbell/Envelope-Encryption?style=flat-square" alt="Latest Version"></img></a>
</p>


## Installation

This version requires [PHP](https://www.php.net/) 8.1.

To get the latest version, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require "graham-campbell/envelope-encryption:^1.0" --dev
```


## Usage

### Encrypting and Serializing

```php
$encrypter = new GrahamCampbell\EnvelopeEncryption\AwsKmsEncrypter(
    new AsyncAws\Kms\KmsClient(),
);

$envelope = $encrypter->encrypt(
    new Input(
        new ParagonIE\HiddenString\HiddenString('hide me'),
        'alias/acme/test', // main KMS key ID or alias
    ),
);

$serializer = new GrahamCampbell\EnvelopeEncryption\JsonSerializer();

$payload = $serializer->serialize($envelope);

// {"data_ciphertext":"MUIFAOlHeWeMX8N5ZZuoxyuRYZos9DTcy5fh3a1O\/trj9ZDV4seM2wwDbZqYy11w9mxQWOyLkYDA4jyywpOfgeXUgsBBO4+4n6BLXkgUKezZMRuqnIj+CN7QGspFbkgzLS0V4H74D+4YaOTnzjfNBj93OsSSwCDrbrrC8QbSRJYqlLQ=","key_ciphertext":"AQIDAHgOVj0wQc06jiZVGlQPyMjyGbHrbb02vc542KC6g2buTgGMuCXPak8K4nPgMVlv4zUyAAAAfjB8BgkqhkiG9w0BBwagbzBtAgEAMGgGCSqGSIb3DQEHATAeBglghkgBZQMEAS4wEQQMlqywgRHX4yrDKLmXAgEQgDuRm+tpHU7kp2s6YWtELD1W7tfXbuUZl3gAuuieT9UFLhGq35qqOAzU8MHnhXf6WrpzC+1mojFqBnm61A==","key_id":"arn:aws:kms:us-east-1:111111111111:key\/xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"}
echo $payload;
```

### Deserializing and Decrypting

```php
$deserializer = new GrahamCampbell\EnvelopeEncryption\JsonDeserializer();

$envelope = $deserializer->deserialize($payload);

$decrypter = new GrahamCampbell\EnvelopeEncryption\AwsKmsDecrypter(
    new AsyncAws\Kms\KmsClient(),
);

$data = $decrypter->decrypt($envelope);

// hide me
echo $data->getString();
```

## Encryption Details

We are using symmetric envelope encryption. This means that a main key is held by AWS. In order to encrypt some data plaintext, we:

1. Request a new 256-bit data key from KMS. This data key arrives both in plaintext and also as ciphertext, encrypted using the specified main key.
2. We use the data key plaintext to encrypt our data plaintext. We use XChaCha20 then BLAKE2b-MAC, backed by libsodium.
3. Return an envelope, which is a tuple containing the data ciphertext, data key ciphertext, and main key ID.

To turn an envelope back into the data plaintext, we:

1. Send our data key ciphertext to KMS along with the main key ID. KMS returns to us the data key plaintext.
2. We use the data key plaintext to decrypt the data ciphertext. We use BLAKE2b-MAC then XChaCha20, backed by libsodium.
3. We return the data plaintext.

### Dependencies

Behind the scenes, we are using `async-aws/kms` to interface with AWS KMS and `paragonie/halite` to perform the encryption, backed by `ext-sodium`.


## Security

If you discover a security vulnerability within this package, please send an email to security@tidelift.com. All security vulnerabilities will be promptly addressed. You may view our full security policy [here](https://github.com/GrahamCampbell/Envelope-Encryption/security/policy).


## License

This package is licensed under [The MIT License (MIT)](LICENSE).


## For Enterprise

Available as part of the Tidelift Subscription

The maintainers of `graham-campbell/envelope-encryption` and thousands of other packages are working with Tidelift to deliver commercial support and maintenance for the open source dependencies you use to build your applications. Save time, reduce risk, and improve code health, while paying the maintainers of the exact dependencies you use. [Learn more.](https://tidelift.com/subscription/pkg/packagist-graham-campbell-envelope-encryption?utm_source=packagist-graham-campbell-envelope-encryption&utm_medium=referral&utm_campaign=enterprise&utm_term=repo)
