<?php

namespace App\Controller\Plan;

use App\Entity\Plan;
use App\Repository\PlanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\Type\PlanType;
use Symfony\Component\HttpFoundation\Request;

class PlanController extends AbstractController
{
    public $serializer = null;
    public $entityManager = null;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $entityManager) {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
     * planList() method to show all plans in database
     * 
     * @param PlanRepository $planRepository set of methods to manipulate data in database
     * 
     * @return JsonResponse
     */
    #[Route("/plan/{user_id}", name: "plan_list", methods: ["GET"])]
    public function planList(PlanRepository $planRepository, int $user_id): JsonResponse
    {
        // fetch all plans form database
        $plans = $planRepository->fetchAllPlans($user_id);

        // check whether $plans is empty
        if(!$plans) {
            // if is return error message
            $message = "Any plan doesn't found";
            return new JsonResponse(["message" => $message], 200, [], false);
        }

        // parse data into JSON format
        $jsonData = $this->serializer->serialize($plans, 'json');

        // return data
        return new JsonResponse($jsonData, 200, [], true);
    }

    /**
     * showPlan() method to show plan with id = $plan_id
     * 
     * @param PlanRepository $planRepository set of methods to manipulate data in database
     * @param int $plan_id plan_id
     * 
     * @return JsonResponse
     */
    #[Route("/plan/{plan_id}", name: "show_plan", methods: ["GET"])]
    public function showPlan(PlanRepository $planRepository, int $plan_id): JsonResponse
    {   
        // find plan with id = $plan_id
        $plan = $planRepository->find($plan_id);

        // check whether $plan is empty
        if(!$plan) {
            // return error message when $plan is empty
            $message = "Plan have not found";
            return new JsonResponse(["message" => $message], 200, [], false);
        }

        // parse data into JSON format
        $jsonData = $this->serializer->serialize($plan, 'json');
        // return data
        return new JsonResponse($jsonData, 200, [], true);
    }

    /**
     * createPlan() method to create new plan
     * 
     * @param EntityManagerInterface $entityManager object to saving data into database
     * 
     * @return JsonResponse
     */
    #[Route("/plan", name: "create_plan", methods: ["POST"])]
    public function createPlan(Request $request): JsonResponse
    {
        try {
            $plan = new Plan();

            // create Plan form
            $form = $this->createForm(PlanType::class, $plan);
            // handle incomming request
            $form->handleRequest($request);
            // submit form
            // this function is only for test on postman
            $form->submit($request->request->all());

            // check whether form is submitted and is valid
            if($form->isSubmitted() && $form->isValid()) {
                // tell Doctrine to save Plan
                $this->entityManager->persist($plan);
                // run INSERT method
                $this->entityManager->flush();

                // return plan with new data in JSON format
                $planJsonFormat = $this->serializer->serialize($plan, 'json');
                return new JsonResponse($planJsonFormat, 200, [], true);
            }
        }
        catch(\Exception $e) {
            // return exception message
            $message = $e->getMessage();
            return new JsonResponse(["message" => $message], 200, [], false);
        }
    }

    /**
     * edtiPlan() method to edit plan data
     * 
     * @param PlanRepository $planRepository set of methods to manipulate data on database
     * @param int $plan_id palan_id
     * 
     * @return JsonResponse
     */
    #[Route("/plan/{plan_id}", name: "edit_plan", methods: ["PUT"])]
    public function editPlan(EntityManagerInterface $entityManager, PlanRepository $planRepository, Request $request, int $plan_id): JsonResponse
    {
        // find plan by id
        $plan = $planRepository->find($plan_id);
        
        // check if $plan is empty
        if(!$plan) {
            // if is return message
            $message = "Plan not found with id ".$plan_id;
            return new JsonResponse(["message" => $message], 200, [], false);
        }

        try {
            // create Plan form
            $form = $this->createForm(PlanType::class, $plan);
            // handle incomming request
            $form->handleRequest($request);
            // submit form
            // this function is only for test on postman
            $form->submit($request->request->all());

            // check whether form is submitted and is valid
            if($form->isSubmitted() && $form->isValid()) {
                // tell Doctrine to save Plan
                $entityManager->persist($plan);
                // run INSERT method
                $entityManager->flush();

                // return plan with new data in JSON format
                $planJsonFormat = $this->serializer->serialize($plan, 'json');
                return new JsonResponse($planJsonFormat, 200, [], true);
            }
        }
        catch(\Exception $e) {
            // return exception message
            $message = $e->getMessage();
            return new JsonResponse(["message" => $message], 200, [], false);
        }
    }

    /**
     * deletePlan() method to delete plan by id
     * 
     * @param PlanRepository $planRepository set of methods to manipulate data on database
     * @param int $plan_id palan_id
     * 
     * @return JsonResponse
     */
    #[Route("/plan/{plan_id}", name: "delete_plan", methods: ["DELETE"])]
    public function deletePlan(PlanRepository $planRepository, int $plan_id): JsonResponse
    {   
        // try to find plan by id
        $plan = $planRepository->find($plan_id);
        
        // check whether $plan is empty
        if(!$plan) {
            // if is return error message
            $message = "Plan have not found with id ".$plan_id;
            return new JsonResponse(['message' => $message], 200, [], false);
        }

        // delete plan
        $delete = $planRepository->deletePlan($plan_id);

        // return message
        $message = "Plan deleted correcttly with id ".$plan_id;
        return new JsonResponse(['message' => $message], 200, [], false);
    }
}
