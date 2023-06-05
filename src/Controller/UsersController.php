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
    public function usersList(UserRepository $userRepository): Response 
    {
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
     * @param EntityManagerInterface $entityManager object to saving data into database
     * 
     * @Route("/user", name="create_user", methods={"POST"})
     */
    public function createUser(EntityManagerInterface $entityManager): Response 
    {   
        // create object 'User' and save new user data
        $user = new User();
        $user->setName('Ola');
        $user->setSurname('Kelner');
        $user->setEmail('ola@gmail.com');
        $user->setPassword('root123');
        $user->setAvatar('#111111');

        // this tell Doctrine to manage with 'User' object
        $entityManager->persist($user);
        // method to execute 'INSERT' method to save data into database
        $entityManager->flush();

        return $this->json([
            'message' => "Saved new user with id ".$user->getUserId(),
            ], 
            headers: ['Content-Type' => 'application/json;charset=UTF-8']
        );
    }

    /**
     * function to delete user
     * 
     * @param UserRepository $userRepository set of methods to manipulating data in database
     * @param int $id user_id which is passed in url /user/7
     * 
     * @Route("/user/{id}", name="delete_user", methods={"DELETE"})
     */
    public function deleteUser(UserRepository $userRepository, int $id): Response 
    {
        $findUser = $userRepository->find($id);

        // check whether user_id was found
        if(!$findUser) {
            throw $this->createNotFoundException('User not found with id '.$id);
        }

        // delete user
        $userRepository->deleteUser($id);

        return $this->json([
            'message' => "User have been found with id ".$id
            ],
            headers: ['Content-Type' => 'application/json;charset=UTF-8']
        );
    }
}
