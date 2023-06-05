<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\UsersController;

class UsersController extends AbstractController 
{   
    /**
     * function to fetch all users from database
     * 
     * @param UserRepository $userRepository set of methods to manipulating data in database
     * 
     * @Route("/user", name="users_list", methods={"GET"})
     */
    public function usersList(UserRepository $userRepository): Response {
        // fetch all users from 'user' table
        $users = $userRepository->findAllUsers();
        
        // return data in JSON format
        return $this->json(
            $users,
            headers: ['Content-Type' => 'application/json;charset=UTF-8']);
    }

    /**
     * function to create new user
     * 
     *  @param UserRepository $userRepository set of methods to manipulating data in database
     * 
     * @Route("/user", name="create_user", methods={"POST"})
     */
    public function createUser(EntityManagerInterface $entityManager): Response {
        $user = new User();
        $user->setName('Ola');
        $user->setSurname('Kelner');
        $user->setEmail('ola@gmail.com');
        $user->setPassword('root123');
        $user->setAvatar('#111111');

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'message' => "Saved new user with id ".$user->getUserId(),
            ], 
            headers: ['Content-Type' => 'application/json;charset=UTF-8']
        );
    }
}
