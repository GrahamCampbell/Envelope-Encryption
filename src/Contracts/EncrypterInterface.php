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
use GrahamCampbell\EnvelopeEncryption\Entities\Input;
use GrahamCampbell\EnvelopeEncryption\Exceptions\EncryptionFailedException;

interface EncrypterInterface
{
    /**
     * @throws EncryptionFailedException
     */
    public function encrypt(Input $input): Envelope;
}
