<?php

namespace App\Controller;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class InvoiceIncrementationController
{
    protected $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    public function __invoke(Invoice $data): void
    {
        $data->setChrono($data->getChrono() + 1);
        $this->manager->persist($data);
        $this->manager->flush($data);
        dd($data);
    }
}
