<?php


namespace App\Controller;

use App\Entity\User;
use App\Entity\UserToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Martin PAUCOT <contact@martin-paucot.fr>
 */
class AuthController extends Controller
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /***
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * AuthController constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param SerializerInterface $serializer
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, SerializerInterface $serializer)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->serializer = $serializer;
    }


    /**
     * Register a new user.
     *
     * @Route(
     *     "register",
     *     methods={"POST"}
     * )
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \HttpRequestException
     */
    public function registerAction(Request $request): Response
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        $firstname = $request->get('firstname');
        $lastname = $request->get('lastname');
        $email = $request->get('email');
        $password = $request->get('password');

        if (null === $firstname) {
            throw new \HttpRequestException();
        }

        if (null === $lastname) {
            throw new \HttpRequestException();
        }

        if (null === $email) {
            throw new \HttpRequestException();
        }

        if (null === $password) {
            throw new \HttpRequestException();
        }

        if ($userRepository->findOneBy(['email' => $email])) {
            return new Response('This e-mail is already used !');
        }

        $user = $userRepository->createUser($firstname, $lastname, $email, $password, $this->passwordEncoder);

        return new Response(
            $this->serializer->serialize(
                $user,
                'json', array('groups' => array('default'))
            )
        );
    }

    /**
     * Login a user.
     *
     * @Route(
     *     "login",
     *     methods={"POST"}
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse|Response
     *
     * @throws \HttpRequestException
     * @throws \Exception
     */
    public function loginAction(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        $email = $request->get('email');
        $password = $request->get('password');

        if (null === $email) {
            throw new \HttpRequestException();
        }

        if (null === $password) {
            throw new \HttpRequestException();
        }

        $user = $userRepository->findOneBy(['email' => $email]);

        if (null === $user || !$this->passwordEncoder->isPasswordValid($user, $password)) {
            throw new AuthenticationCredentialsNotFoundException();
        }

        $userToken = new UserToken($user);
        $entityManager->persist($userToken);
        $entityManager->flush();

        $now = new \DateTime();

        return new JsonResponse(
            [
                'token' => $userToken->getToken(),
                'expiresIn' => $userToken->getExpiresOn()->getTimestamp() - $now->getTimestamp()
            ]
        );
    }

}