<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\Type\UserType;
use Symfony\Component\HttpFoundation\Request;
use App\Service\UsersHelper;

class UsersController extends AbstractController 
{
    /**
     * function to fetch all users from database
     * 
     * @return JsonResponse
     */
    #[Route("/user", name: "users_list", methods: ["GET"])]
    public function usersList(UsersHelper $usersHelper): JsonResponse 
    {
        // fetch all users from 'user' table
        $users = $usersHelper->userRepository->findAllUsers();
        
        // parse data into JSON format
        $jsonData = $usersHelper->serializer->serialize($users, 'json');
        // return data
        return new JsonResponse($jsonData, 200, [], true);
    }

    /**
     * showUser() method to show user by id
     * 
     * @param int $user_id user_id
     * 
     * @return JsonResponse
     */
    #[Route("/user/{user_id}", name: "show_user", methods: ["GET"])]
    public function showUser(UsersHelper $usersHelper, int $user_id): JsonResponse
    {   
        // try to find user
        $findUser = $usersHelper->userRepository->find($user_id);

        // check whether user was found 
        if(!$findUser) {
            // if not return error message
            $message = "User not found with id ".$user_id;
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
     * @param EntityManagerInterface $entityManager set of methods to operate on object (saving data into database)
     * @param Request $request set of method for request
     * 
     * @return JsonResponse
     */
    #[Route("/user", name: "create_user", methods: ["POST"])]
    public function createUser(UsersHelper $usersHelper, Request $request): JsonResponse 
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

            // check whether user exist with entered email
            $findUser = $usersHelper->checkUserExistByEmail($form);
            if($findUser) {
                return new JsonResponse(["message" => $findUser], 200, [], false);
            }

            // check if form have correct data
            if($form->isSubmitted() && $form->isValid()) {
                // this tell Doctrine to manage with 'User' object
                $this->entityManager->persist($user);
                // method to execute 'INSERT' method to save data into database
                $this->entityManager->flush();

                // parse User object into JSON format
                $jsonData = $usersHelper->serializer->serialize($user, 'json');
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
     * @param EntityManagerInterface $entityManager set of methods to operate on object (saving data into database)
     * @param Request $request set of method for request
     * @param int $user_id user_id
     * 
     * @return JsonResponse
     */
    #[Route("/user/{user_id}", name: "edit_user", methods: ["PUT"])]
    public function editUser(UsersHelper $usersHelper, Request $request, int $user_id): JsonResponse
    {   
        // try to find user by id
        $user = $usersHelper->userRepository->find($user_id);

        // check whether user was found 
        if(!$user) {
            // if not return error message
            $message = "User not found with id ".$user_id;
            return new JsonResponse(["message" => $message], 200, [], false);
        }

        try {
            // create form with User
            $form = $this->createForm(UserType::class, $user);
            // handle incomming request
            $form->handleRequest($request);
            // submit and proccess data form
            // force submit and this is only for dev and testing on postman
            $form->submit($request->request->all()); 

            // check whether user exist with entered email
            $findUser = $usersHelper->checkUserExistByEmailForEdit($form, $user_id);
            if($findUser) {
                return new JsonResponse(["message" => $findUser], 200, [], false);
            }

            // check if is submitted and valid
            if($form->isSubmitted() && $form->isValid()) {                
                // this tell Doctrine to manage with 'User' object
                $usersHelper->entityManager->persist($user);
                // method to execute 'INSERT' method to save data into database
                $usersHelper->entityManager->flush();

                // parse User object into JSON format
                $jsonData = $usersHelper->serializer->serialize($user, 'json');
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
     * function to delete user
     * 
     * @param UserRepository $userRepository set of methods to manipulating data in database
     * @param int $user_id user_id which is passed in url /user/7
     * 
     * @return JsonResponse
     */
    #[Route("/user/{user_id}", name: "delete_user", methods: ["DELETE"])]
    public function deleteUser(UsersHelper $usersHelper, int $user_id): JsonResponse 
    {   
        // try to find user by id
        $findUser = $usersHelper->userRepository->find($user_id);

        // check whether user was found 
        if(!$findUser) {
            // if not return error message
            $message = "User not found with id ".$user_id;
            return new JsonResponse(["message" => $message], 200, [], false);
        }

        // delete user
        $usersHelper->userRepository->deleteUser($user_id);

        // return data in JSON format
        $message = "User have been delete with id ".$user_id;
        return new JsonResponse(["message" => $message], 200, [], false);
    }
}
