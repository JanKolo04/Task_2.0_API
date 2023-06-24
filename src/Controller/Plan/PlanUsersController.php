<?php

    namespace App\Controller\Plan;

    use App\Service\Plan\PlanUsersHelper;
    use App\Repository\PlanUsersRepository;
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
         * @Route("/plan/{plan_id}/users", name="users_in_plan", methods={"GET"})
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
            try {
                // run methods to check exist plan and user
                $planExist = $planUsersHelper->checkPlanExist($plan_id);
                $userExist = $planUsersHelper->checkUserExist($user_id);

                // check whether methods above not returns null
                if($planExist != null) {
                    return new JsonResponse(['message' => $planExist], 200, [], false);
                }
                else if($userExist != null) {
                    return new JsonResponse(['message' => $userExist], 200, [], false);
                }

                // add user into plan
                $planUsers = $planUsersHelper->addUserIntoPlan($entityManager, $user_id, $plan_id);

                // parse PlanUsers object into JSON format
                $planUsersJson = $this->serializer->serialize($planUsers, 'json');
                // return data
                return new JsonResponse($planUsersJson, 200, [], true);

            }
            catch(\Exception $e) {
                // return exception message
                $message = $e->getMessage();
                return new JsonResponse(["message" => $message], 200, [], false);
            }
        }
    }
?>