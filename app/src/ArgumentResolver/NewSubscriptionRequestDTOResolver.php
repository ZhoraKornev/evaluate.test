<?php


namespace App\ArgumentResolver;


use App\DTO\NewSubscriptionRequestDTO;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Security;


class NewSubscriptionRequestDTOResolver implements ArgumentValueResolverInterface
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
     * @var Security
     */
    private Security $userSecurity;

    /**
     * LiqPayPayment constructor.
     *
     * @param LoggerInterface     $subscriptionLogger
     * @param SerializerInterface $serializer
     * @param Security            $security
     */
    public function __construct(
        LoggerInterface $subscriptionLogger,
        SerializerInterface $serializer,
        Security $security
    )
    {
        $this->logger = $subscriptionLogger;
        $this->serializer = $serializer;
        $this->userSecurity = $security;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return NewSubscriptionRequestDTO::class === $argument->getType() &&
            str_contains($request->getPathInfo(), 'subscription')
            && str_contains($request->getMethod(), 'PATCH');
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        // @see https://docs.fondy.eu/en/docs/page/3/#chapter-2
        /** @var NewSubscriptionRequestDTO $dto */
        $this->logger->info('NEW SUBSCRIPTION FOR USER', ['USER' => $this->userSecurity->getUser()?->getUsername()]);
        $dto = $this->serializer->deserialize($request->getContent(), NewSubscriptionRequestDTO::class, 'json');
        $dto->user = $this->userSecurity->getUser();

        yield $dto;
    }

}
