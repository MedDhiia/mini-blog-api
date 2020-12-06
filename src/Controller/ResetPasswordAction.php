<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordAction
{
    private $validator ;
    private $tokenManager ;
    private $entityManager ;
    private $userPasswordEncoder ;

    public function __construct(
        ValidatorInterface $validator,
        JWTTokenManagerInterface $tokenManager,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->validator = $validator;
        $this->tokenManager = $tokenManager;
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function __invoke(User $data)
    {
        $this->validator->validate($data);

        $data->setPassword(
            $this->userPasswordEncoder->encodePassword(
                $data,
                $data->getNewPassword()
            )
        );
        $data->setPasswordChangeDate(time());

        $this->entityManager->flush();

        $token = $this->tokenManager->create($data);

        return new JsonResponse(['token' => $token]);
    }
}
