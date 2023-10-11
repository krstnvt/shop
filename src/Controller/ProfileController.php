<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Shop;
use App\Entity\User;
use App\Form\AddAddressFormType;
use App\Form\AddShopFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    public const FLASH_INFO = 'info';
    #[Route(path: 'user/profile', name: '_profile')]
    public function profile(): Response
    {
        return $this->render('profile/profile.html.twig', ['user' => $this->getUser()]);
    }

    #[Route(path: 'user/addShop', name: '_profile_addShop')]
    public function addShop(Request $request, EntityManagerInterface $entityManager): Response
    {
        $shop = new Shop();
        $form = $this->createForm(AddShopFormType::class, $shop);

        $form->handleRequest($request);

        $user = $this->getUser();

        if ($user instanceof User && $form->isSubmitted() && $form->isValid()) {
            $shop->addUser($user);
            $shop->setStatus($shop::STATUS_NEW);

            $entityManager->persist($shop);
            $entityManager->flush();

            $this->addFlash(self::FLASH_INFO, 'Магазин добавлен успешно');
            return $this->redirectToRoute('_profile');
        }

        return $this->render('profile/addShop.html.twig',
            [
                'user' => $user,
                'form' => $form->createView()
            ]);
    }

    #[Route(path: 'user/addAddress', name: '_profile_addAddress')]
    public function addAddress(Request $request, EntityManagerInterface $entityManager): Response
    {
        $address = new Address();
        $form = $this->createForm(AddAddressFormType::class, $address);

        $form->handleRequest($request);

        $user = $this->getUser();

        if ($user instanceof User && $form->isSubmitted() && $form->isValid()) {
            $address->addUser($user);

            $entityManager->persist($address);
            $entityManager->flush();

            $this->addFlash(self::FLASH_INFO, 'Адрес добавлен успешно');
            return $this->redirectToRoute('_profile');
        }

        return $this->render('profile/addAddress.html.twig', ['form' => $form->createView()]);
    }
}
