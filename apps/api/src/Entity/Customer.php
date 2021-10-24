<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 */

#[ApiResource(
    // GET , POST
    collectionOperations: [
        'GET' => [
            'path' => "/v1/customers/"
        ],
        'POST' => [
            'path' => "/v1/customers/"
        ],
    ],
    // GET , PUT, DELETE, PATCH
    itemOperations: [
        'GET' => [
            "path" => "/v1/customers/{id}"
        ],
        'PUT' => [
            "path" => "/v1/customers/{id}"
        ],
        'DELETE' => [
            "path" => "/v1/customers/{id}"
        ],
        'PATCH' => [
            "path" => "/v1/customers/{id}"
        ],
    ],
    subresourceOperations: [
        'invoices_get_subresource' => [
            'method' => 'GET',
            'path' => '/v1/customers/{id}/invoices',
            // 'security' => "is_granted('ROLE_AUTHENTICATED')",

        ],
    ],
    normalizationContext: [
        'groups' => ['custumer:normalization:read']
    ],

    // denormalizationContext: ['groups' => ['write']],
    attributes: [
        'pagination_enabled' => true,
        'pagination_items_per_page' => 10
    ]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'firstName' => 'partial',
        'lastName' => 'partial',
        'company' => 'partial'
    ]
)]
#[
    ApiFilter(
        OrderFilter::class,
        // properties: ['id', 'amount', 'chrono'],
        // arguments: ['orderParameterName' => 'order']
    )
]
class Customer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(
        [
            "custumer:normalization:read",
            "invoice:normalization:read"
        ]
    )]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Groups(
            [
                "custumer:normalization:read",
                "invoice:normalization:read"
            ]
        ),
        Assert\NotBlank(
            message: "Le prénom du client est obligatoire"
        ),
        Assert\Length(
            min: 3,
            minMessage: "Le prénom doit faire plus de {{ limit }} caractères",
            max: 50,
            maxMessage: "Le prénom doit contenir moins de {{ limit }} caractères"
        )
    ]
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Groups(
            [
                "custumer:normalization:read",
                "invoice:normalization:read"
            ]
        ),
        Assert\NotBlank(
            message: "Le nom de famille du client est obligatoire"
        ),
        Assert\Length(
            min: 3,
            minMessage: "Le nom de famille doit faire plus de {{ limit }} caractères",
            max: 50,
            maxMessage: "Le nom de famille doit contenir moins de {{ limit }} caractères"
        )
    ]
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     */
    #[
        Groups(
            [
                "custumer:normalization:read",
                "invoice:normalization:read"
            ]
        ),
        Assert\NotBlank(
            message: "L'adresse email du customer est obligatoire"
        ),
        Assert\Email(
            message: "Le format de l'adresse email est incorrecte"
        )
    ]
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(
        [
            "custumer:normalization:read",
            "invoice:normalization:read"
        ]
    )]
    private $company;

    /**
     * @ORM\OneToMany(targetEntity=Invoice::class, mappedBy="customer")
     */
    #[
        Groups(
            [
                "custumer:normalization:read",
            ]
        ),
        ApiSubresource()
    ]
    private $invoices;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="customers")
     */
    #[
        Groups(
            [
                "custumer:normalization:read",
            ]
        ),
        Assert\NotBlank(
            message: "L'utilisateur est obligatoire"
        ),
    ]
    private $user;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection|Invoice[]
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices[] = $invoice;
            $invoice->setCustomer($this);
        }

        return $this;
    }

    /**
     * Get total invoices
     * @return float
     */
    #[Groups(
        [
            "custumer:normalization:read",
        ]
    )]
    public function getTotalAmount(): float
    {
        return array_reduce(
            $this->invoices->toArray(),

            function ($total, $invoice) {
                return $total + $invoice->getAmount();
            },
            0
        );
    }

    /**
     * Return the total amount of unpaid invoices
     * @return float
     */
    #[Groups(
        [
            "custumer:normalization:read",
        ]
    )]
    public function getTotalUnpaid(): float
    {
        // Transform arraycollection to array
        return array_reduce($this->invoices->toArray(), function ($total, $invoce) {
            // Get total of invoices unpaid
            return $total +
                ($invoce->getStatus() === "PAID" || $invoce->getStatus() === "CANCELLED") ? 0 : $invoce->getAmount();
        }, 0);
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getCustomer() === $this) {
                $invoice->setCustomer(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
