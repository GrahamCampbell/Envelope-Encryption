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

namespace GrahamCampbell\EnvelopeEncryption\Contracts;

use GrahamCampbell\EnvelopeEncryption\Entities\Envelope;
use GrahamCampbell\EnvelopeEncryption\Exceptions\DecryptionFailedException;
use ParagonIE\HiddenString\HiddenString;

interface DecrypterInterface
{
    /**
     * @throws DecryptionFailedException
     */
    public function decrypt(Envelope $envelope): HiddenString;
}
