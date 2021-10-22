<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Invoice;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;
use Utils;

class AppFixtures extends Fixture
{

    protected $slugger, $passwordHasher, $manager;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create("fr_FR");
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        // Création d'un admin
        // $admin = new User();
        // $admin->setEmail("admin@admin.com")
        //     ->setFullName("Admin")
        //     ->setRoles(['ROLE_ADMIN'])
        //     ->setPassword($this->passwordHasher->hashPassword($admin, 'password'));

        // Persist Admin
        // $manager->persist($admin);

        // $users = [];
        // Création des utilisateurs
        // for ($i = 0; $i < 10; $i++) {
        //     $user = new User;
        //     $user->setEmail("user$i@gmail.com")
        //         ->setFullName($faker->name())
        //         // ->setPassword($this->encoder->encodePassword($user, 'password'));
        //         ->setPassword($this->passwordHasher->hashPassword($admin, 'password'));

        //     $users[] = $user;
        //     // persis User
        //     $manager->persist($user);
        // }


        for ($i = 0; $i < mt_rand(1, 50); $i++) {
            $customer = new Customer;
            $customer->setFirstName($this->faker->firstName())
                ->setLastName($this->faker->lastName())
                ->setCompany($this->faker->company())
                ->setEmail($this->faker->email())
                // ->setUser($user)
            ;

            $manager->persist($customer);

            /**
             * Generate Invoices
             */
            for ($i = 0; $i < mt_rand(2, 50); $i++) {
                $invoce = new Invoice;
                $invoce
                    ->setAmount($this->faker->randomFloat(2, 53, 5000))
                    ->setSentAt(Utils::randomDate())
                    ->setStatus($this->faker->randomElement(['SENT', 'PAID', 'CANCELLED']))
                    ->setCustomer($customer);

                $manager->persist($invoce);
            }
        }
        $manager->flush();
    }
}
