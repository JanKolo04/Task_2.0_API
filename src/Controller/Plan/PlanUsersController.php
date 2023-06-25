<?php

    namespace App\Controller\Plan;

    use App\Service\Plan\PlanUsersHelper;
    use App\Repository\PlanUsersRepository;
    use App\Repository\PlanRepository;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Serializer\SerializerInterface;
    use Symfony\Component\HttpFoundation\Request;

    class PlanUsersController extends AbstractController
    {
        public $serializer = null;

        public function __construct(SerializerInterface $serializer) {
            $this->serializer = $serializer;
        }

        /**
         * usersList() method to get all users in plan
         * 
         * @param PlanUsersRepository $planUsersRepository set of methods for database
         * @param int $plan_id plan id
         * 
         * @return JsonResponse
         * 
         * @Route("/plan/{plan_id}/users", name="list_of_users_in_plan", methods={"GET"})
         */
        public function usersList(PlanUsersRepository $planUsersRepository, int $plan_id): JsonResponse
        {
            // fetch all plans form database
            $usersInPlan = $planUsersRepository->fetchAllUsers($plan_id);

            // check whether $plans is empty
            if(!$usersInPlan) {
                // if is return error message
                $message = "Any user in plan with id {$plan_id} not found";
                return new JsonResponse(["message" => $message], 200, [], false);
            }

            // parse usersInPlanJsonFormat into JSON format
            $usersInPlanJsonFormat = $this->serializer->serialize($usersInPlan, 'json');

            // return data
            return new JsonResponse($usersInPlanJsonFormat, 200, [], true);
        }

        /**
         * showUser() method to show one user in plan
         * 
         * @param PlanUsersHelper set of methods to make main code clean
         * @param int $plan_id plan id
         * @param int $user_id user id
         * 
         * @return JsonResponse
         * 
         * @Route("/plan/{plan_id}/users/{user_id}", name="show_user_in_plan", methods={"GET"})
         */
        public function showUser(PlanUsersHelper $planUsersHelper, int $plan_id, int $user_id): JsonResponse
        {   
            // try to find plan with id $plan_id
            $plan = $planUsersHelper->checkPlanExist($plan_id);
            // try to find user with id $user_id
            $user = $planUsersHelper->checkUserExistInPlan($plan_id, $user_id);

            // if an function return string return error message
            if(gettype($plan) == 'string') {
                return new JsonResponse(['message' => $plan], 200, [], false);
            }
            else if(gettype($user) == 'string') {
                return new JsonResponse(['message' => $user], 200, [], false);
            }

            // parse $user object into JSON format
            $userInJsonFormat = $this->serializer->serialize($user, 'json');
            // return data
            return new JsonResponse($userInJsonFormat, 200, [], true);             
        }


        /**
         * addUser() method to add user into plan
         * 
         * @param PlanUsersHelper $planUsersHelper set of methods to make main controller smaller
         * @param EntityManagerInterface $entityManager set of methods for entity
         * @param int $plan_id plan id
         * @param int $user_id user id
         * 
         * @return JsonResponse
         * 
         * @Route("/plan/{plan_id}/users/{user_id}", name="create_user_plan", methods={"POST"})
         */
        public function addUser(PlanUsersHelper $planUsersHelper, EntityManagerInterface $entityManager, int $plan_id, int $user_id): JsonResponse
        {
            // run methods to check exist plan and user
            $checkPlanExist = $planUsersHelper->checkPlanExist($plan_id);
            $checkUserExist = $planUsersHelper->checkUserExist($user_id);

            // check whether methods above not returns null
            if(gettype($checkPlanExist) == 'string') {
                return new JsonResponse(['message' => $checkPlanExist], 200, [], false);
            }
            else if(gettype($checkUserExist) == 'string') {
                return new JsonResponse(['message' => $checkUserExist], 200, [], false);
            }

            // add user into plan
            $planUsers = $planUsersHelper->addUserIntoPlan($entityManager, $user_id, $plan_id);

            // parse PlanUsers object into JSON format
            $planUsersJson = $this->serializer->serialize($planUsers, 'json');
            // return data
            return new JsonResponse($planUsersJson, 200, [], true);   
        }

        /**
         * deleteUser() method to delete user from plan
         * 
         * @param PlanUsersHelper $planUsersHelper set of methods to make main code cleaner
         * @param PlaUsersRepository $planUsersRepository set of methods to action with database
         * @param int $plan_id plan id
         * @param int $user_id user id
         * 
         * @return JsonResponse
         * 
         * @Route("/plan/{plan_id}/users/{user_id}", name="delete_user_from_plan", methods={"DELETE"})
         */
        public function deleteUser(PlanUsersHelper $planUsersHelper, PlanUsersRepository $planUsersRepository, int $plan_id, int $user_id): JsonResponse
        {
            // try to find plan with id $plan_id
            $plan = $planUsersHelper->checkPlanExist($plan_id);
            // try to find user with id $user_id
            $user = $planUsersHelper->checkUserExistInPlan($plan_id, $user_id);

            // if an function return string return error message
            if(gettype($plan) == 'string') {
                return new JsonResponse(['message' => $plan], 200, [], false);
            }
            else if(gettype($user) == 'string') {
                return new JsonResponse(['message' => $user], 200, [], false);
            }

            // delete user from plan
            $planUsersRepository->deleteUserFromPlan($plan_id, $user_id);

            // send message that user deleted correctlly
            $message = "User deleted correctlly with id ".$user_id." from plan with id ".$plan_id;
            return new JsonResponse(['message' => $message], 200, [], false);
        }
    }
?>