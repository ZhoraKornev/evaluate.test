<?php


namespace App\ArgumentResolver;


use App\DTO\FondyPaymentDTO;
use App\Service\FondySignatureVerification;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;


class FondyPaymentDTOResolver implements ArgumentValueResolverInterface
{

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;
    /**
     * @var FondySignatureVerification
     */
    private FondySignatureVerification $signatureVerifications;

    /**
     * LiqPayPayment constructor.
     *
     * @param LoggerInterface     $logger
     * @param SerializerInterface $serializer
     */
    public function __construct(LoggerInterface $logger,SerializerInterface $serializer,FondySignatureVerification $signatureVerification)
    {
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->signatureVerifications = $signatureVerification;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return FondyPaymentDTO::class === $argument->getType() && preg_match('/^\/subscription\/pay/', $request->getPathInfo());
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        // @see https://docs.fondy.eu/en/docs/page/3/#chapter-2
        /** @var FondyPaymentDTO $dto */
        $dto = $this->serializer->deserialize($request->getContent(), FondyPaymentDTO::class, 'json');
        if (!$this->signatureVerifications->verify($dto)){
            $this->logger->critical(
                'FONDY PAYMENT Signature invalid',
                [
                    'verification_status' => $dto->verification_status,
                    'order_status' => $dto->order_status,
                    'response_description' => $dto->response_description,
                ]
            );
            throw new InvalidArgumentException('Signature invalid');
        }

        yield $dto;
    }

}
