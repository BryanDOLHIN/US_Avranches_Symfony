<?php

namespace App\Entity;

use App\Repository\GraphRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GraphRepository::class)]
class Graph
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
