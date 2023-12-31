<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Shop;
use App\Entity\User;
use App\Form\AddAddressFormType;
use App\Form\AddShopFormType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
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

    #[Route(path: 'user/listShops', name: '_profile_listShops')]
    public function listShops(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        return $this->render('profile/listShops.html.twig', ['shops' => $user->getShops()]);
    }

    #[Route(path: 'user/shopInfo/{id<\d+>}', name: '_profile_shopInfo')]
    public function shopInfo(): Response
    {
        $shop = new Shop();

        return $this->render('profile/shopInfo.html.twig', ['shop' => $shop]);
    }

    #[Route(path: '/user/addShop', name: '_profile_addShop')]
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
                'form' => $form->createView()
            ]);
    }

    #[Route(path: 'user/editShop/{id<\d+>}', name: '_profile_editShop')]
    public function editShop(Request $request, EntityManagerInterface $entityManager, Shop $shop): Response
    {
        $form = $this->createForm(AddShopFormType::class, $shop);

        $form->handleRequest($request);

        $user = $this->getUser();

        if ($user instanceof User && $form->isSubmitted() && $form->isValid()) {
            $shop->addUser($user);
            $shop->setStatus($shop::STATUS_NEW);

            $entityManager->persist($shop);
            $entityManager->flush();

            $this->addFlash(self::FLASH_INFO, 'Магазин обновлен успешно');
            return $this->redirectToRoute('_profile');
        }

        return $this->render('profile/editShop.html.twig',
            [
                'form' => $form->createView()
            ]);
    }

    #[Route('/user/deleteShop/{id<\d+>}', name: '_profile_deleteShop')]
    public function deleteShop(Shop $shop, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($shop);
        $entityManager->flush();

        return $this->redirectToRoute('_profile');
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

        return $this->render('profile/addAddress.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
