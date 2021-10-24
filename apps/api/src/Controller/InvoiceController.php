<?php

namespace App\Controller;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InvoiceController extends AbstractController
{
    protected $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    public function __invoke(Invoice $data): void
    {
        dd("data");
        dd($data);
        $data->setChrono($data->getChrono() + 1);
        $this->manager->persist($data);
        $this->manager->flush($data);
    }
}
