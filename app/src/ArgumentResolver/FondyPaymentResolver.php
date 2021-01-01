<?php


namespace App\ArgumentResolver;


use App\DTO\FondyPaymentsDTO;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;


class FondyPaymentResolver implements ArgumentValueResolverInterface
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
     * LiqPayPayment constructor.
     *
     * @param LoggerInterface               $logger
     */
    public function __construct(LoggerInterface $logger,SerializerInterface $serializer)
    {
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return FondyPaymentsDTO::class === $argument->getType() && preg_match('/^\/api/i', $request->getPathInfo());
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     * @throws
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {

//        $arToLog = array(
//            'userOntraportID' => $contact_id,
//            'paymentId' => '',
//            'currency' => 'USD',                # Валюта выставленого счёта
//            'recipientAmount' => $productPrice, # Сумма выставленного счёта
//            'currencyID' => '0',
//            'paymentCurrency' => 'USD',       # Валюта оплаты
//            'paymentAmount' => $productPrice, # Сумма оплаты
//            'userName' => '',
//            'userEmail' => $contactEmail,
//            'paymentData' => date('Y-m-d H:i:s'),
//            'paymentDateStr' => strtotime('now'),
//            'hash' => '',
//            'invoiceId' => $data['payment_id'],
//            'merchantPaymentAmount' => '',
//            'paymentStatus' => '5',
//            'recurringpaymentid' => '0',
//            'productID' => $productId,
//            'paymentSystem' => '42', //42 ид оплаты это фонди
//        );

        try {
            /** @var FondyPaymentsDTO $dto */
            $dto = $this->serializer->deserialize($request->getContent(), FondyPaymentsDTO::class, 'json');
        } catch (\ErrorException $error) {
            throw new InvalidArgumentException($error->getMessage());
        }
        yield $dto;
    }

}
