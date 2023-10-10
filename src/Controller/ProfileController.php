<?php

namespace App\Controller;

use App\Entity\Shop;
use App\Form\AddShopFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route(path: 'user/profile', name: '_profile')]
    public function profile(): Response
    {
        return $this->render('profile/profile.html.twig', ['user' => $this->getUser()]);
    }

    #[Route(path: 'user/addShop', name: '_profile_addShop')]
    public function addShop(): Response
    {
        $shop = new Shop();
        $form = $this->createForm(AddShopFormType::class, $shop);
        return $this->render('profile/addShop.html.twig', ['form' => $form->createView()]);
    }
}
