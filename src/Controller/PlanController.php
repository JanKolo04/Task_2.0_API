<?php

namespace App\Controller;

use App\Entity\Plan;
use App\Repository\PlanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;

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

        // check whether $plan is empty
        if(!$plans) {
            // if is return error message
            $message = "Any plan doesn't found";
            return new JsonResponse(["message" => $message], 200, [], false);
        }

        // convert data into JSON format
        $jsonData = $this->serializer->serialize($plans, 'json');

        // return data
        return new JsonResponse($jsonData, 200, [], true);
    }

}
