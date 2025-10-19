<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $name = null;

    /**
     * @var Collection<int, Fruit>
     */
    #[ORM\OneToMany(targetEntity: Fruit::class, mappedBy: 'category')]
    private Collection $fruits;

    /**
     * @var Collection<int, Legume>
     */
    #[ORM\OneToMany(targetEntity: Legume::class, mappedBy: 'category')]
    private Collection $legumes;

    public function __construct()
    {
        $this->fruits = new ArrayCollection();
        $this->legumes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Fruit>
     */
    public function getFruits(): Collection
    {
        return $this->fruits;
    }

    public function addFruit(Fruit $fruit): static
    {
        if (!$this->fruits->contains($fruit)) {
            $this->fruits->add($fruit);
            $fruit->setCategory($this);
        }

        return $this;
    }

    public function removeFruit(Fruit $fruit): static
    {
        if ($this->fruits->removeElement($fruit)) {
            // set the owning side to null (unless already changed)
            if ($fruit->getCategory() === $this) {
                $fruit->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Legume>
     */
    public function getLegumes(): Collection
    {
        return $this->legumes;
    }

    public function addLegume(Legume $legume): static
    {
        if (!$this->legumes->contains($legume)) {
            $this->legumes->add($legume);
            $legume->setCategory($this);
        }

        return $this;
    }

    public function removeLegume(Legume $legume): static
    {
        if ($this->legumes->removeElement($legume)) {
            // set the owning side to null (unless already changed)
            if ($legume->getCategory() === $this) {
                $legume->setCategory(null);
            }
        }

        return $this;
    }
}
