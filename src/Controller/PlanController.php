<?php

namespace App\Controller;

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

    public function __construct(SerializerInterface $serializer) {
        $this->serializer = $serializer;
    }

    /**
     * planList() method to show all plans in database
     * 
     * @param PlanRepository $planRepository set of methods to manipulate data in database
     * 
     * @Route("/plan", name="plan_list", methods={"GET"})
     */
    public function planList(PlanRepository $planRepository): JsonResponse
    {
        // fetch all plans form database
        $plans = $planRepository->fetchAllPlans();

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
     * showPlan() method to show plan with id = $id
     * 
     * @param PlanRepository $planRepository set of methods to manipulate data in database
     * @param int $id plan_id
     * 
     * @Route("/plan/{id}", name="shiow_plan", methods={"GET"})
     */
    public function showPlan(PlanRepository $planRepository, int $id): JsonResponse
    {   
        // find plan with id = $id
        $plan = $planRepository->find($id);

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
     * @Route("/plan", name="create_plan", methods={"POST"})
     */
    public function createPlan(EntityManagerInterface $entityManager): JsonResponse
    {
        // create object 'Plan' add data
        $plan = new Plan();
        $plan->setName('Plan 2');
        $plan->setBgColor('#FFFFFF');

        // manage 'Plan' object
        $entityManager->persist($plan);
        // execute 'INSERT' method
        $entityManager->flush();

        // return message
        $message = "Plan added correcttly with id ".$plan->getPlanId();
        return new JsonResponse(['message' => $message], 200, [], false);
    }

    /**
     * edtiPlan() method to edit plan data
     * 
     * @param PlanRepository $planRepository set of methods to manipulate data on database
     * @param int $id palan_id
     * @return JsonResponse
     * 
     * @Route("/plan/{id}", name="edit_plan", methods={"PATCH"})
     */
    public function editPlan(EntityManagerInterface $entityManager, PlanRepository $planRepository, Request $request, int $id): JsonResponse
    {
        // find plan by id
        $plan = $planRepository->find($id);
        
        // check if $plan is empty
        if(!$plan) {
            // if is return message
            $message = "Plan not found with id ".$id;
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
     * @param int $id palan_id
     * 
     * @Route("/plan/{$id}", name="delete_plan", methods={"DELETE"})
     */
    public function deletePlan(PlanRepository $planRepository, int $id): JsonResponse
    {   
        // try to find plan by id
        $plan = $planRepository->find($id);
        
        // check whether $plan is empty
        if(!$plan) {
            // if is return error message
            $message = "Plan have not found with id ".$id;
            return new JsonResponse(['message' => $message], 200, [], false);
        }

        // delete plan
        $delete = $planRepository->deletePlan($id);

        // return message
        $message = "Plan deleted correcttly with id ".$id;
        return new JsonResponse(['message' => $message], 200, [], false);
    }
}