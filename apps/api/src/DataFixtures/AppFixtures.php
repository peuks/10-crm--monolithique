<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{

    protected $slugger, $passwordHasher, $manager;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->passwordHasher = $passwordHasher;

        /**
         * @var Faker
         */
        $this->faker = Factory::create("fr_FR");
    }

    public function load(ObjectManager $manager)
    {
        // Création d'un admin
        $admin = new User();
        $admin->setEmail("admin@admin.com")
            ->setFirstName("Admin")
            ->setLastName("Admin")
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->passwordHasher->hashPassword($admin, 'password'));

        // Persist Admin
        $manager->persist($admin);
        /**
         * Création des Users
         */
        for ($i = 0; $i < mt_rand(20, 50); $i++) {
            /** @var User */
            $user = new User();
            $user->setFirstName($this->faker->firstName())
                ->setEmail($this->faker->email())
                ->setLastName($this->faker->lastName())
                ->setPassword($this->passwordHasher->hashPassword($admin, 'password'));;
            $manager->persist($user);

            /**
             * Création des Clients
             */
            for ($i = 0; $i < mt_rand(1, 50); $i++) {
                $customer = new Customer;
                $customer->setFirstName($this->faker->firstName())
                    ->setLastName($this->faker->lastName())
                    ->setCompany($this->faker->company())
                    ->setEmail($this->faker->email())
                    ->setUser($user);

                $manager->persist($customer);

                /**
                 * Création des factures
                 */
                for ($i = 0; $i < mt_rand(2, 50); $i++) {
                    $invoce = new Invoice;
                    $invoce
                        ->setAmount($this->faker->randomFloat(2, 53, 5000))
                        ->setSentAt($this->faker->dateTimeBetween('- 12 months'))
                        ->setStatus($this->faker->randomElement(['SENT', 'PAID', 'CANCELLED']))
                        ->setCustomer($customer)
                        ->setChrono($i + 1);

                    $manager->persist($invoce);
                }
            }
        }
        $manager->flush();
    }
}
