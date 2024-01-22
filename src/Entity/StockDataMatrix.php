<?php

namespace App\Entity;

use App\Repository\StockDataMatrixRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockDataMatrixRepository::class)]
class StockDataMatrix
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $SKU = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $productFamily = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reference = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $size1Name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $size1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $size1Unit = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $size2Name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $size2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $size2Unit = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $surface = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSKU(): ?string
    {
        return $this->SKU;
    }

    public function setSKU(?string $SKU): static
    {
        $this->SKU = $SKU;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getProductFamily(): ?string
    {
        return $this->productFamily;
    }

    public function setProductFamily(?string $productFamily): static
    {
        $this->productFamily = $productFamily;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getSize1Name(): ?string
    {
        return $this->size1Name;
    }

    public function setSize1Name(?string $size1Name): static
    {
        $this->size1Name = $size1Name;

        return $this;
    }

    public function getSize1(): ?string
    {
        return $this->size1;
    }

    public function setSize1(?string $size1): static
    {
        $this->size1 = $size1;

        return $this;
    }

    public function getSize1Unit(): ?string
    {
        return $this->size1Unit;
    }

    public function setSize1Unit(?string $size1Unit): static
    {
        $this->size1Unit = $size1Unit;

        return $this;
    }

    public function getSize2Name(): ?string
    {
        return $this->size2Name;
    }

    public function setSize2Name(?string $size2Name): static
    {
        $this->size2Name = $size2Name;

        return $this;
    }

    public function getSize2(): ?string
    {
        return $this->size2;
    }

    public function setSize2(?string $size2): static
    {
        $this->size2 = $size2;

        return $this;
    }

    public function getSize2Unit(): ?string
    {
        return $this->size2Unit;
    }

    public function setSize2Unit(?string $size2Unit): static
    {
        $this->size2Unit = $size2Unit;

        return $this;
    }

    public function getSurface(): ?string
    {
        return $this->surface;
    }

    public function setSurface(?string $surface): static
    {
        $this->surface = $surface;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
