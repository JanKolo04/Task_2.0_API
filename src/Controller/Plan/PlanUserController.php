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

    class PlanUserController extends AbstractController
    {
        public $serializer = null;

        public function __construct(SerializerInterface $serializer) {
            $this->serializer = $serializer;
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