<?php

namespace App\Entity;

use App\Repository\PlanUsersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanUsersRepository::class)]
#[ORM\Table(name: 'user_in_plan')]
class PlanUsers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $plan_id = null;

    #[ORM\Column]
    private ?int $user_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlanId(): ?int
    {
        return $this->plan_id;
    }

    public function setPlanId(int $plan_id): static
    {
        $this->plan_id = $plan_id;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }
}
