<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nxcore/
 * @link      https://gitlab.com/nx-core/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */

namespace App\Service;


use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class PkiManager
 *
 * @package App\Service
 */
class PkiManager
{

    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var KernelInterface $appKernel */
    private $appKernel;

    /** @var string $keyStoragePath */
    private $keyStoragePath;

    /** @var string $pkiClassVersion */
    private $pkiClassVersion;

    /**
     * PkiManager constructor.
     *
     * @param KernelInterface $appKernel
     */
    public function __construct(KernelInterface $appKernel)
    {
        $this->pkiClassVersion = "1.0.0";
        $this->filesystem = new Filesystem();
        $this->keyStoragePath = $appKernel->getProjectDir() . '/var/private/server_comms_key.pem';
    }

    /**
     * @return false|resource
     */
    private function getBothKey()
    {
        return openssl_pkey_get_private('file://' . $this->keyStoragePath);
    }

    /**
     * @return mixed
     */
    private function getPrivateKey()
    {
        openssl_pkey_export($this->getBothKey(), $privKey);

        return $privKey;
    }

    /**
     * @return bool
     */
    public function createNewKey()
    {
        if (!$this->isKeyAvailable()) {
            $config = [
                "digest_alg" => "sha512",
                "private_key_bits" => 4096,
                "private_key_type" => OPENSSL_KEYTYPE_RSA,
            ];

            $res = openssl_pkey_new($config);
            openssl_pkey_export_to_file($res, $this->keyStoragePath);

            return $this->isKeyAvailable();
        } else {
            return true;
        }
    }

    /**
     * @param string $data
     *
     * @return mixed
     */
    public function decryptData(string $data)
    {
        // Decrypt the data using the private key
        openssl_private_decrypt(base64_decode($data), $decryptedData, $this->getPrivateKey());

        // Return decrypted data
        return $decryptedData;
    }

    /**
     * @param string $data
     *
     * @return string
     */
    public function encryptData(string $data)
    {
        // Encrypt the data using the public key
        openssl_public_encrypt($data, $encryptedData, $this->getPublicKey());

        // Return encrypted data
        return base64_encode($encryptedData);
    }

    /**
     * @return string
     */
    public function getClassVersion(): string
    {
        return $this->pkiClassVersion;
    }

    /**
     * @return mixed
     */
    public function getPublicKey()
    {
        $res = openssl_pkey_get_private('file://' . $this->keyStoragePath);
        return openssl_pkey_get_details($res)['key'];
    }

    /**
     * @return bool
     */
    public function isKeyAvailable()
    {
        return $this->filesystem->exists($this->keyStoragePath);
    }

    /**
     * @param string $data
     * @param string $signed
     * @param string $getPublicKey
     *
     * @return bool
     */
    public function isSigVerifiable(string $data, string $signed, string $getPublicKey)
    {
        $signed = base64_decode($signed);
        $signResults = openssl_verify($data, $signed, $getPublicKey);
        if ($signResults == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $sign
     *
     * @return string
     */
    public function signData(string $sign)
    {
        openssl_sign($sign, $signature, $this->getPrivateKey());
        return base64_encode($signature);
    }

}
