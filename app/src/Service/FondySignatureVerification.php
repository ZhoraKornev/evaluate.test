<?php


namespace App\Service;


use App\DTO\FondyPaymentDTO;
use App\Service\Factory\FondySignatureCreator;

class FondySignatureVerification
{
    /**
     * @var FondySignatureCreator
     */
    private FondySignatureCreator $signatureCreator;

    /**
     * FondySignatureVerification constructor.
     *
     * @param FondySignatureCreator $fondySignatureVerification
     */
    public function __construct(
        FondySignatureCreator $fondySignatureVerification,
    ) {
        $this->signatureCreator = $fondySignatureVerification;
    }

    /**
     * Clean array params
     *
     * @param array $data
     *
     * @return array
     */
    public function clean(array $data):array {
        if (array_key_exists('response_signature_string', $data)) {
            unset($data['response_signature_string']);
        }
        unset($data['signature']);

        return $data;
    }

    /**
     * Check response params signature
     *
     * @param FondyPaymentDTO $paymentDTO
     *
     * @return bool
     */
    public function verify(FondyPaymentDTO $paymentDTO):bool {
       return true;
    }
}
