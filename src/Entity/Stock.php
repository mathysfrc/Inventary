<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
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

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $surface = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shape = null;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public static function getStockFromTracking(Tracking $tracking): Stock
    {

        $stock = new Stock();
        $stock->setSKU($tracking->getSKU());
        $stock->setDescription($tracking->getDescription());
        $stock->setSize1($tracking->getSize1());
        $stock->setSize2($tracking->getSize2());
        $stock->setSize1Unit($tracking->getSize1Unit());
        $stock->setSize2Unit($tracking->getSize2Unit());
        $stock->setSize1Name($tracking->getSize1Name());
        $stock->setSize2Name($tracking->getSize2Name());
        $stock->setPrice($tracking->getPrice());
        $stock->setProductFamily($tracking->getProductFamily());
        $stock->setSurface($tracking->getSurface());
        $stock->setShape($tracking->getShape());
        $stock->setReference($tracking->getReference());
        $stock->setFree($tracking->getFree());
        $stock->setComment($tracking->getComment());
        $stock->setStatus($tracking->getStatus());

        return $stock;
    }


public static function generateSKU(Stock $stock, EntityManagerInterface $entityManager)
{
    // Récupérez le dernier SKU de la base de données
    $latestSKU = $entityManager
        ->getRepository(Stock::class)
        ->createQueryBuilder('s')
        ->select('MAX(s.SKU)')
        ->getQuery()
        ->getSingleScalarResult();

    if ($latestSKU === null) {
        $latestSKU = 0;
    }

    // Incrémentez le SKU
    $nextSKU = intval(substr($latestSKU, 5)) + 1;

    // Formatez le SKU avec des zéros à gauche
    $formattedSKU = sprintf("(000)%018d", $nextSKU);

    // Définissez le SKU généré dans l'entité $stock
    $stock->setSKU($formattedSKU);

    // Retournez le SKU généré
    return $formattedSKU;
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

    public function getShape(): ?string
    {
        return $this->shape;
    }

    public function setShape(?string $shape): static
    {
        $this->shape = $shape;

        return $this;
    }
}
