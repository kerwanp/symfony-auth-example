<?php


namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Martin PAUCOT <contact@martin-paucot.fr>
 */
class UserController extends Controller
{

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Returns the current user.
     *
     * @Route(
     *     "user",
     *     methods={"GET"}
     * )
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @return Response
     */
    public function getUserAction(): Response
    {
        return new Response(
            $this->serializer->serialize(
                $this->getUser(),
                'json', array('groups' => array('default'))
            )
        );
    }

}