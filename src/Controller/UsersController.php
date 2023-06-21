<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use App\Form\Type\UserType;
use Symfony\Component\HttpFoundation\Request;


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
        
        // parse data into JSON format
        $jsonData = $this->serializer->serialize($users, 'json');
        // return data
        return new JsonResponse($jsonData, 200, [], true);
    }

    /**
     * showUser() method to show user by id
     * 
     * @param UserRepository $userRepository set of methods to manipulating data in database 
     * @param int $id user_id
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

        // parse data into JSON format
        $jsonData = $this->serializer->serialize($findUser, 'json');
        // return data
        return new JsonResponse($jsonData, 200, [], true);
    }

    /**
     * function to create new user
     * 
     * @param EntityManagerInterface $entityManager object to saving data into database
     * 
     * @Route("/user", name="create_user", methods={"POST"})
     */
    public function createUser(EntityManagerInterface $entityManager, Request $request): JsonResponse 
    {   
        // create try to avoid any exeptions
        try {
            // create object 'User' and save new user data
            $user = new User();

            // create form with User
            $form = $this->createForm(UserType::class, $user);
            // handle incomming request
            $form->handleRequest($request);
            // submit and proccess data form
            // force submit and this is only for dev and testing on postman
            $form->submit($request->request->all());

            // check if form have correct data
            if($form->isSubmitted() && $form->isValid()) {
                // this tell Doctrine to manage with 'User' object
                $entityManager->persist($user);
                // method to execute 'INSERT' method to save data into database
                $entityManager->flush();

                // parse User object into JSON format
                $jsonData = $this->serializer->serialize($user, 'json');
                // return data
                return new JsonResponse($jsonData, 200, [], true);
            }
        }
        catch(\Exception $e) {
            // return error message
            $message = $e->getMessage();
            return new JsonResponse(["message" => $message], 200, [], false);
        }
    }

    /**
     * edituser() metod to edit user data
     * 
     * @param UserRepository $userRepository set of methods which operate on database
     * @param int $id user_id
     * @return JsonResponse
     * 
     * @Route("/user/{id}", name="edit_user", methods={"PATCH"})
     */
    public function editUser(UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request, int $id): JsonResponse
    {
        // try to find user by id
        $user = $userRepository->find($id);

        if(!$user) {
            $message = "User not found with id ".$id;
            return new JsonResponse(['message' => $message], 200, [], false);
        }

        // create form with User
        $form = $this->createForm(UserType::class, $user);
        // handle incomming request
        $form->handleRequest($request);
        // submit and proccess data form
        // force submit and this is only for dev and testing on postman
        $form->submit($request->request->all()); 

        // check if is submitted and valid
        if($form->isSubmitted() && $form->isValid()) {                
            // this tell Doctrine to manage with 'User' object
            $entityManager->persist($user);
            // method to execute 'INSERT' method to save data into database
            $entityManager->flush();

            // parse User object into JSON format
            $jsonData = $this->serializer->serialize($user, 'json');
            // return data
            return new JsonResponse($jsonData, 200, [], true);
        }

        return new JsonResponse(['message' => 'Nothing'], 200, [], false);
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
