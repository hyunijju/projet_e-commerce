<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Form\CommandeType;
use App\Form\CommandeFrontType;
use App\Form\InscriptionFormType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/home')]
class HomeController extends AbstractController
{
    
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'home_index')]
    public function index (Request $request , ManagerRegistry $doctrine):Response{
       
        $produits = $doctrine->getRepository(Produit::class)->findAll();
        
        return $this->render("home.html.twig");
    }

    #[Route('/items', name: 'home_items')]
    public function item (Request $request , ManagerRegistry $doctrine):Response{
       
        $produits = $doctrine->getRepository(Produit::class)->findAll();

        return $this->render("home/items.html.twig", [ "produits" => $produits ]);
    }

    #[Route('/login', name: 'home_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
    
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('home/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    

            return $this->redirectToRoute("home_commande");
    }

    #[Route('/inscription', name: 'home_inscription', methods: ['GET', 'POST'])]
    public function inscription(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager , UserRepository $userRepository): Response
    {
        $user = new User();
        $formInscription = $this->createForm(InscriptionFormType::class, $user);

        $formInscription->handleRequest($request);

        if ($formInscription->isSubmitted() && $formInscription->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $formInscription->get('plainPassword')->getData()
                )
            );
            $user->setRoles(["ROLE_USER"]);

            $userRepository->add($user, true);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('home_items', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('home/inscription.html.twig', [
            'user' => $user,
            'formInscription' => $formInscription
        ]);
    }

    #[Route("/add_panier/{id}" , name:"home_add_panier")]
 public function add_panier($id , SessionInterface $session ){

   $panier = $session->get('panier' , []);
   
   if(!empty($panier[$id])){
      $panier[$id]++ ;
   }else {
      $panier[$id] = 1; 
   }

   $session->set('panier' , $panier); 

   return $this->redirectToRoute("home_panier");

    $this->addFlash("message" , "L'article produit numéro $id a été ajouté à votre panier.");
 }

 #[Route("/remove_panier/{id}" , name:"home_remove_panier")]
 public function delete_panier($id , SessionInterface $session ){

   $panier = $session->get('panier' , []);
   
   if(!empty($panier[$id])){
      $panier[$id]-- ;
   }else {
      $panier[$id] = 1; 
   }

   $session->set('panier' , $panier); 

   return $this->redirectToRoute("home_panier");

 }

    #[Route("/panier" , name:"home_panier")]
 public function panier(Request $request , EntityManagerInterface $em ,SessionInterface $session , ProduitRepository $produitRepository , Commande $commande = null){

   $panier = $session->get('panier', []);
   $panierWithData = [];

   foreach($panier as $id => $quantite) {
       $panierWithData[] = [
           'produit' => $produitRepository->find($id),
           'quantite' => $quantite
       ];
   }

   $total = 0;

   foreach($panierWithData as $item) {
       $totalItem = $item['produit']->getPrix() * $item['quantite'];
       $total += $totalItem;
   }

   return $this->render("home/panier.html.twig" , [
       'items' => $panierWithData,
       'total' => $total
   ]);

 }

 #[Route("/delete_panier/{id}" , name:"home_delete_panier")]
 public function remove($id, SessionInterface $session) {
     $panier = $session->get('panier' , []);

     if(!empty($panier[$id])) {
         unset($panier[$id]);
     }

     $session->set('panier' , $panier);

     return $this->redirectToRoute("home_panier");
 }

 #[Route('/commande', name: 'home_commande', methods: ['GET', 'POST'])]
    public function new(Request $request, CommandeRepository $commandeRepository ,SessionInterface $session , ProduitRepository $produitRepository ): Response
    { 
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->remove('montant');
        $form->remove('etat');
        $form->remove('user');
        $form->handleRequest($request);

        $panier = $session->get('panier', []);
        $panierWithData = [];

        foreach($panier as $id => $quantite) {
        $panierWithData[] = [
           'produit' => $produitRepository->find($id),
           'quantite' => $quantite
       ];
   }

   $total = 0;

   foreach($panierWithData as $item) {
       $totalItem = $item['produit']->getPrix() * $item['quantite'];
       $total += $totalItem;
   }

        if ($form->isSubmitted() && $form->isValid()) {
            $commandeRepository->add($commande, true);

            return $this->redirectToRoute('home_commande', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('home/commande.html.twig', [
            'commande' => $commande,
            'form' => $form
        ]);
    }


}

