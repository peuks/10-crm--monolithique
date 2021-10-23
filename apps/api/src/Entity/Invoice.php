<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=InvoiceRepository::class)
 */
#[ApiResource(
    // GET , POST
    collectionOperations: [
        'GET' => [
            'path' => "/v1/invoices/"
        ],
        'POST' => [
            'path' => "/v1/invoices/"
        ],
    ],
    // GET , PUT, DELETE, PATCH
    itemOperations: [
        'GET' => [
            "path" => "/v1/invoices/{id}"
        ],
        'PUT' => [
            "path" => "/v1/invoices/{id}"
        ],
        'DELETE' => [
            "path" => "/v1/invoices/{id}"
        ],
        'PATCH' => [
            "path" => "/v1/invoices/{id}"
        ],
    ],
    normalizationContext: [
        'groups' => ['invoice:normalization:read']
    ],

    // denormalizationContext: ['groups' => ['write']],
    attributes: [
        'order' => ["sentAt" => "DESC"], //ASC
    ]
)]
#[
    ApiFilter(
        OrderFilter::class,
        properties: ['id', 'amount', 'chrono'],
        arguments: ['orderParameterName' => 'order']
    )
]
class Invoice
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups([
        "invoice:normalization:read",
        "custumer:normalization:read"
    ])]
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    #[Groups([
        "invoice:normalization:read",
        "custumer:normalization:read"
    ])]
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups([
        "invoice:normalization:read",
        "custumer:normalization:read"
    ])]
    private $sentAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups([
        "invoice:normalization:read",
        "custumer:normalization:read"
    ])]
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="invoices")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Groups([
        "invoice:normalization:read",
    ])]
    private $customer;

    /**
     * @ORM\Column(type="integer")
     */
    #[Groups([
        "invoice:normalization:read",
        "custumer:normalization:read"
    ])]
    private $chrono;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSentAt(): ?\DateTime
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTime $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getChrono(): ?int
    {
        return $this->chrono;
    }

    public function setChrono(int $chrono): self
    {
        $this->chrono = $chrono;

        return $this;
    }
    /**
     * Permet fde récupérer le User à qui apaprtient la facture
     * @return User|null
     */

    #[Groups([
        "invoice:normalization:read",
    ])]
    public function getUser(): User
    {
        return $this->customer->getUser();
    }
}
