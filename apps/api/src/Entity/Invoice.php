<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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
        'Increment' => [
            "method" => "POST",
            "path" => "/v1/invoices/{id}/increment",
            "controller" => "App\Controller\InvoiceIncrementationController",
            'openapi_context' => [
                'summary'     => "Increment invoice's chrono by +1",
                // 'description' => "# Pop a great rabbit picture by color!\n\n![A great rabbit](https://rabbit.org/graphics/fun/netbunnies/jellybean1-brennan1.jpg)",
                'description' => "This controller with Increment by once a invoice's chrono",
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema'  => [
                                'type'       => 'object',
                                'properties' =>
                                [
                                    'yoooo'        => ['type' => 'string'],
                                    'description' => ['type' => 'string'],
                                ],
                            ],
                            'example' => [
                                "Send an empty body"
                            ],
                        ],
                    ],
                ],
            ],
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

    denormalizationContext: [
        "disable_type_enforcement" => true
    ],
    subresourceOperations: [
        'api_customers_invoices_get_subresource' => [
            'method' => 'GET',
            'normalization_context' => [
                'groups' => ['invoice:subresource:normalization:read'],
            ],


        ],
    ],
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
        "custumer:normalization:read",
        "invoice:subresource:normalization:read"
    ])]
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    #[
        Groups([
            "invoice:normalization:read",
            "custumer:normalization:read",
            "invoice:subresource:normalization:read"

        ]),
        Assert\NotBlank(
            message: "Le montant de la facture est obligatoire"
        ),
        Assert\Type(
            type: "numeric",
            message: "Le montant doit être numérique."
        )
    ]
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     */
    #[
        Groups([
            "invoice:normalization:read",
            "custumer:normalization:read",
            "invoice:subresource:normalization:read"

        ]),
        Assert\Type(
            type: "\DateTime",
            message: "La date doir être au format YYYY-MM-DD"
        ),
        Assert\NotBlank(
            message: "La date doit être renseignée"
        )
    ]
    private $sentAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Groups([
            "invoice:normalization:read",
            "custumer:normalization:read",
            "invoice:subresource:normalization:read"

        ]),
        Assert\NotBlank(message: "Le status de la facture est obligatoire"),
        Assert\Choice(
            ['SEND', 'PAID', 'CANCELLED'],
            message: "Les choix possibles sont SEND, PAID ou CANCELLED"
        )
    ]

    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="invoices")
     * @ORM\JoinColumn(nullable=false)
     */
    #[
        Groups([
            "invoice:normalization:read",
        ]),
        Assert\NotBlank(
            message: "Veuillez renseigner un customer pour cette facture"
        )
    ]
    private $customer;

    /**
     * @ORM\Column(type="integer")
     */
    #[
        Groups([
            "invoice:normalization:read",
            "custumer:normalization:read",
            "invoice:subresource:normalization:read"

        ]),
        Assert\NotBlank(
            message: "Il faut définir un chrono"
        ),
        Assert\Type(
            type: "integer",
            message: "Le chrono doit être un nombre !"
        )
    ]
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
        "invoice:subresource:normalization:read"

    ])]
    public function getUser(): User
    {
        return $this->customer->getUser();
    }
}
