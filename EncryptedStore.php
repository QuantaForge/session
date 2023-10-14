<?php

namespace QuantaForge\Session;

use QuantaForge\Contracts\Encryption\DecryptException;
use QuantaForge\Contracts\Encryption\Encrypter as EncrypterContract;
use SessionHandlerInterface;

class EncryptedStore extends Store
{
    /**
     * The encrypter instance.
     *
     * @var \QuantaForge\Contracts\Encryption\Encrypter
     */
    protected $encrypter;

    /**
     * Create a new session instance.
     *
     * @param  string  $name
     * @param  \SessionHandlerInterface  $handler
     * @param  \QuantaForge\Contracts\Encryption\Encrypter  $encrypter
     * @param  string|null  $id
     * @param  string  $serialization
     * @return void
     */
    public function __construct($name, SessionHandlerInterface $handler, EncrypterContract $encrypter, $id = null, $serialization = 'php')
    {
        $this->encrypter = $encrypter;

        parent::__construct($name, $handler, $id, $serialization);
    }

    /**
     * Prepare the raw string data from the session for unserialization.
     *
     * @param  string  $data
     * @return string
     */
    protected function prepareForUnserialize($data)
    {
        try {
            return $this->encrypter->decrypt($data);
        } catch (DecryptException) {
            return $this->serialization === 'json' ? json_encode([]) : serialize([]);
        }
    }

    /**
     * Prepare the serialized session data for storage.
     *
     * @param  string  $data
     * @return string
     */
    protected function prepareForStorage($data)
    {
        return $this->encrypter->encrypt($data);
    }

    /**
     * Get the encrypter instance.
     *
     * @return \QuantaForge\Contracts\Encryption\Encrypter
     */
    public function getEncrypter()
    {
        return $this->encrypter;
    }
}
