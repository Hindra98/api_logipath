<?php

namespace App\Entity;

use App\Repository\PackageRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: PackageRepository::class)]
#[OA\Schema(description: "Modèle représentant un colis")]
class Package
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[OA\Property(description: 'Identifiant unique du colis')]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[OA\Property(description: 'Numéro de suivi', example: 'PKG-12345')]
  private ?string $trackingNumber = null;

  #[ORM\Column(length: 50)]
  #[OA\Property(description: 'Statut actuel', example: 'En transit')]
  private ?string $status = null;

  #[ORM\Column(length: 255)]
  #[OA\Property(description: 'Nom du client', example: 'John Doe')]
  private ?string $customerName = null;

  #[ORM\Column(length: 255, nullable: true)]
  #[OA\Property(description: 'Destination du colis', example: 'New York')]
  private ?string $destination = null;

  #[ORM\Column]
  #[OA\Property(description: 'Date de livraison estimée', example: '2023-12-31')]
  private ?\DateTime $estimatedDelivery = null;

  #[ORM\Column]
  #[OA\Property(description: 'Date de la dernière mise à jour', example: '2023-10-01T12:00:00Z')]
  private ?\DateTimeImmutable $updatedAt = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getTrackingNumber(): ?string
  {
    return $this->trackingNumber;
  }

  public function setTrackingNumber(string $trackingNumber): static
  {
    $this->trackingNumber = $trackingNumber;

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

  public function getCustomerName(): ?string
  {
    return $this->customerName;
  }

  public function setCustomerName(string $customerName): static
  {
    $this->customerName = $customerName;

    return $this;
  }

  public function getDestination(): ?string
  {
    return $this->destination;
  }

  public function setDestination(?string $destination): static
  {
    $this->destination = $destination;

    return $this;
  }

  public function getEstimatedDelivery(): ?\DateTime
  {
    return $this->estimatedDelivery;
  }

  public function setEstimatedDelivery(\DateTime $estimatedDelivery): static
  {
    $this->estimatedDelivery = $estimatedDelivery;

    return $this;
  }

  public function getUpdatedAt(): ?\DateTimeImmutable
  {
    return $this->updatedAt;
  }

  public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
  {
    $this->updatedAt = $updatedAt;

    return $this;
  }
}
