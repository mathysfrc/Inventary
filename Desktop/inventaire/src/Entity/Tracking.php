<?php

namespace App\Entity;

use App\Repository\TrackingRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrackingRepository::class)]
class Tracking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $SKU = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $size1 = null;

    #[ORM\Column]
    private ?float $size2 = null;

    #[ORM\Column(length: 255)]
    private ?string $size1Unit = null;

    #[ORM\Column(length: 255)]
    private ?string $size2Unit = null;

    #[ORM\Column(length: 255)]
    private ?string $size1Name = null;

    #[ORM\Column(length: 255)]
    private ?string $size2Name = null;

    #[ORM\Column(length: 255)]
    private ?string $resultUnit = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    private ?string $productFamily = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reference = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $free = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timestamp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $movementType = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSKU(): ?string
    {
        return $this->SKU;
    }

    public function setSKU(string $SKU): static
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

    public function getSize1(): ?float
    {
        return $this->size1;
    }

    public function setSize1(float $size1): static
    {
        $this->size1 = $size1;

        return $this;
    }

    public function getSize2(): ?float
    {
        return $this->size2;
    }

    public function setSize2(float $size2): static
    {
        $this->size2 = $size2;

        return $this;
    }

    public function getSize1Unit(): ?string
    {
        return $this->size1Unit;
    }

    public function setSize1Unit(string $size1Unit): static
    {
        $this->size1Unit = $size1Unit;

        return $this;
    }

    public function getSize2Unit(): ?string
    {
        return $this->size2Unit;
    }

    public function setSize2Unit(string $size2Unit): static
    {
        $this->size2Unit = $size2Unit;

        return $this;
    }

    public function getSize1Name(): ?string
    {
        return $this->size1Name;
    }

    public function setSize1Name(string $size1Name): static
    {
        $this->size1Name = $size1Name;

        return $this;
    }

    public function getSize2Name(): ?string
    {
        return $this->size2Name;
    }

    public function setSize2Name(string $size2Name): static
    {
        $this->size2Name = $size2Name;

        return $this;
    }

    public function getResultUnit(): ?string
    {
        return $this->resultUnit;
    }

    public function setResultUnit(string $resultUnit): static
    {
        $this->resultUnit = $resultUnit;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getProductFamily(): ?string
    {
        return $this->productFamily;
    }

    public function setProductFamily(string $productFamily): static
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

    public function getFree(): ?string
    {
        return $this->free;
    }

    public function setFree(?string $free): static
    {
        $this->free = $free;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp( ?\DateTimeInterface $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getMovementType(): ?string
    {
        return $this->movementType;
    }

    public function setMovementType(?string $movementType): static
    {
        $this->movementType = $movementType;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public static function getTrackingFromStock(Stock $stock, string $movementType, DateTime $timestamp) : Tracking {
        $tracking = new Tracking();
        $tracking->setSKU($stock->getSKU());
        $tracking->setDescription($stock->getDescription());
        $tracking->setSize1($stock->getSize1());
        $tracking->setSize2($stock->getSize2());
        $tracking->setSize1Unit($stock->getSize1Unit());
        $tracking->setSize2Unit($stock->getSize2Unit());
        $tracking->setSize1Name($stock->getSize1Name());
        $tracking->setSize2Name($stock->getSize2Name());
        $tracking->setResultUnit($stock->getResultUnit());
        $tracking->setPrice($stock->getPrice());
        $tracking->setProductFamily($stock->getProductFamily());
        $tracking->setReference($stock->getReference());
        $tracking->setFree($stock->getFree());
        $tracking->setComment($stock->getComment());
        $tracking->setStatus($stock->getStatus());
        $tracking->setMovementType($movementType);
        $tracking->setTimestamp($timestamp);

        return $tracking;
    }
}
