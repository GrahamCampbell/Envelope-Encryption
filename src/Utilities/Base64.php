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

namespace GrahamCampbell\EnvelopeEncryption\Utilities;

/**
 * @internal
 */
final class Base64
{
    public static function decode(string $str): string
    {
        return sodium_base642bin($str, SODIUM_BASE64_VARIANT_ORIGINAL);
    }

    public static function encode(string $str): string
    {
        return sodium_bin2base64($str, SODIUM_BASE64_VARIANT_ORIGINAL);
    }
}
