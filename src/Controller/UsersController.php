<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\UsersController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class UsersController extends AbstractController 
{   
    public $serializer = null;
    public function __construct(SerializerInterface $serializer) {
        $this->serializer = $serializer;
    }

    /**
     * function to fetch all users from database
     * 
     * @param UserRepository $userRepository set of methods to manipulating data in database
     * 
     * @Route("/user", name="users_list", methods={"GET"})
     */
    public function usersList(UserRepository $userRepository): JsonResponse 
    {
        // fetch all users from 'user' table
        $users = $userRepository->findAllUsers();
        
        // return data in JSON format
        $jsonData = $this->serializer->serialize($users, 'json');

        return new JsonResponse($jsonData, 200, [], true);
    }

    /**
     * 
     * @Route("/user/{id}", name="show_user", methods={"GET"})
     */
    public function showUser(UserRepository $userRepository, int $id): JsonResponse
    {
        $findUser = $userRepository->find($id);

        // check whether user was found 
        if(!$findUser) {
            // if not return error message
            $message = "User not found with id ".$id;
            return new JsonResponse(["message" => $message], 200, [], false);
        }

        // return data in JSON format
        $jsonData = $this->serializer->serialize($findUser, 'json');

        return new JsonResponse($jsonData, 200, [], true);
    }

    /**
     * function to create new user
     * 
     * @param EntityManagerInterface $entityManager object to saving data into database
     * 
     * @Route("/user", name="create_user", methods={"POST"})
     */
    public function createUser(EntityManagerInterface $entityManager): JsonResponse 
    {   
        // create object 'User' and save new user data
        $user = new User();
        $user->setName('Bolek');
        $user->setSurname('Kelner');
        $user->setEmail('bolek@gmail.com');
        $user->setPassword('admin123');
        $user->setAvatar('#765912');

        // this tell Doctrine to manage with 'User' object
        $entityManager->persist($user);
        // method to execute 'INSERT' method to save data into database
        $entityManager->flush();

        // return data in JSON format
        $message = "User added correctlly with id ".$user->getUserId();
        return new JsonResponse(["message" => $message], 200, [], false);
    }

    /**
     * function to delete user
     * 
     * @param UserRepository $userRepository set of methods to manipulating data in database
     * @param int $id user_id which is passed in url /user/7
     * 
     * @Route("/user/{id}", name="delete_user", methods={"DELETE"})
     */
    public function deleteUser(UserRepository $userRepository, int $id): JsonResponse 
    {
        $findUser = $userRepository->find($id);

        // check whether user was found 
        if(!$findUser) {
            // if not return error message
            $message = "User not found with id ".$id;
            return new JsonResponse(["message" => $message], 200, [], false);
        }

        // delete user
        $userRepository->deleteUser($id);

        // return data in JSON format
        $message = "User have been delete with id ".$id;
        return new JsonResponse(["message" => $message], 200, [], false);
    }
}
