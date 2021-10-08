<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Controller\RedisController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends RedisController
{
    /**
     * Click house connection
     */
    private $clickHouse;

    public function __construct()
    {
        parent::__construct();
        $connectionParams = [
            'host' => 'localhost',
            'port' => 8123,
            'user' => 'default',
            'password' => '',
            'dbname' => 'hezzl',
            'driverClass' => 'FOD\DBALClickHouse\Driver',
            'wrapperClass' => 'FOD\DBALClickHouse\Connection',
            'driverOptions' => [
                'extremes' => false,
                'readonly' => true,
                'max_execution_time' => 30,
                'enable_http_compression' => 0,
                'https' => false,
            ],
        ];
        $this->clickHouse = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, new \Doctrine\DBAL\Configuration());
    }

    /**
     * Creates a user
     * 
     * @param Request $request
     * @param ValidatorInterface $validator
     * 
     * @Route("/api/user/create", methods={"POST"})
     * 
     * @return Response
     */
    public function create(Request $request, ValidatorInterface $validator) : Response
    {
        $manager = $this->getDoctrine()->getManager();
        $username = $request->request->get('username');

        $user = new User();
        $user->setUsername($username);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json([
                'errors' => (string)$errors
            ]);
        }

        $manager->persist($user);
        $manager->flush();

        $this->clickHouse->insert('user_logs', [
            'username' => $username
        ]);

        return $this->json([
            'status' => 'success',
            'object' => $user
        ]);
    }

    /**
     * Delete a user
     * 
     * @Route("/api/user/delete/{user}", methods={"DELETE"})
     * 
     * @return Response
     */
    public function delete(User $user) : Response
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($user);
        $manager->flush();
        
        return $this->json([
            'status' => 'success'
        ]);
    }

    /**
     * Returns all users
     * 
     * @Route("/api/user/all", methods={"GET"})
     * 
     * @return Response
     */
    public function all() : Response
    {
        $users = $this->redis->get('users');
        if ($users == false) {
            $serializer = $this->getSerializer();
            $json = $serializer->serialize($this->getDoctrine()->getRepository(User::class)->findAll(), 'json');
            $this->redis->setEx('users', 60, $json);
            return $this->json([
                'data' => $json
            ]);            
        }
        return $this->json([
            'data' => $users
        ]);
    }
}