<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Produit;
use App\Entity\Categorie;
use App\Entity\Vendeur;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use App\Controller\sessioninterface;
use App\Form\ProduitType;
class ArticleController extends AbstractController {

/**
	*@Route("/about",name="about")
    */
    public function about(){
       
        return $this->render('About.html.twig');

    }

    /**
	*@Route("/",name="home")
    */
    public function home(){
       $em= $this->getDoctrine()->getManager();
       $produits=$em->getRepository(Produit::class)->findAll();
       $categories=$em->getRepository(Categorie::class)->findAll();
       $vendeurs=$em->getRepository(Vendeur::class)->findAll();

        return $this->render('home.html.twig',[
            'produits'=>$produits,
            'categories' => $categories,
            'vendeurs'=>$vendeurs
        ]);
    }
/**
	*@Route("/contact",name="contact")
    */
    public function contact(Request $request,\Swift_Mailer $mailer){
        $email=$request->request->get('email'); 
        $name=$request->request->get('name'); 
        $message=$request->request->get('texte'); 
        $message = (new \Swift_Message('Hello Email'))
        ->setFrom('ssfaxi@gmail.com')
        ->setTo('ssfaxi66@gmail.com')
        ->setBody('Test Email');
        $mailer->send($message);
        return $this->render('contact.html.twig');   
    }

    /**
	*@Route("/produit/{id}",name="produit")
    */
   public function produit($id){
        $em= $this->getDoctrine()->getManager();
       $produits=$em->getRepository(Produit::class)->find($id);
       $categories=$em->getRepository(Categorie::class)->findAll($id);
        return $this->render('produit.html.twig',
        ['produits'=>$produits,
        'categories'=>$categories]);
    }

        /**
	*@Route("/category/{id}",name="category")
    */
   public function category($id){
    $em= $this->getDoctrine()->getManager();

   $categorie=$em->getRepository(Categorie::class)->find($id);
   $categories=$em->getRepository(Categorie::class)->findAll($id);

   $produits=$categorie->getProduits();

    return $this->render('categorie.html.twig',
    [
        'produits'=>$produits,
        'categories' => $categories,
        'actual_categorie'=> $categorie
    ]);
}

        /**
	*@Route("/add_to_cart/{id}",name="add_to_cart")
    */
        public function addtocart (Request $request,$id){
            $session=$request->getsession();
            if(!$session->has('panier')){
               $session -> set ('panier',array());
             }
                $panier=$session ->get('panier');
            if(array_key_exists($id,$panier)){
               $panier[$id]+=1;
            }else {
              $panier[$id]=1;
            }
            $session->set('panier',$panier);
            return $this->redirect($this->generateURL('cart'));

        }
        /**
	*@Route("/cart",name="cart")
    */
    public function cart (Request $request){ 
        $session=$request->getsession(); 
        if(!$session->has('panier')){
            $session->set('panier',array());
          }
        $panier=$session ->get('panier');
        $em= $this->getDoctrine()->getManager();
        $produits=$em->getRepository(Produit::class)->findBy(['id'=>array_keys($panier)]);
        $vendeurs=$em->getRepository(Vendeur::class)->findBy(['id'=>array_keys($panier)]);

       return $this->render('cart.html.twig',array(
            'produits' => $produits,
            'panier' => $panier,

       ));
    }
      /**
	*@Route("/admin",name="admin")
    */
    public function testrole(){
       $this->denyAccessUnlessGranted('ROLE_ADMIN');
       $em= $this->getDoctrine()->getManager();
       $produits=$em->getRepository(Produit::class)->findAll();
       $categories=$em->getRepository(Categorie::class)->findAll();
        return $this->render('testrole.html.twig',[
            'produits'=>$produits,
            'categories'=>$categories
        ]);


    }
     


 
/**
	*@Route("/remove/{id}",name="remove")
    */
        public function remove (request $request,$id){
            $session=$request->getsession();

                $panier=$session ->get('panier',[]);
            if(!empty($panier[$id])){
              unset( $panier[$id]);
            }
            $session->set('panier',$panier);
            return $this->redirect($this->generateURL('cart'));

        }

        
      /**
	*@Route("/admin/listearticle",name="liste_articles")
    */
    public function tistearticle(){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em= $this->getDoctrine()->getManager();
        $produits=$em->getRepository(Produit::class)->findAll();
        $categories=$em->getRepository(Categorie::class)->findAll();
         return $this->render('affichearticle.html.twig',[
             'produits'=>$produits,
             'categories'=>$categories
         ]);
 
 
     }
        
      /**
	*@Route("/admin/listecategorie",name="liste_categorie")
    */
    public function tistecategories(){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em= $this->getDoctrine()->getManager();
        $categories=$em->getRepository(Categorie::class)->findAll();
         return $this->render('affichecategorie.html.twig',[
            
             'categories'=>$categories
         ]);
 
 
     }
       
      /**
	*@Route("/admin/listeuser",name="liste_user")
    */
    public function tisteuser(){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em= $this->getDoctrine()->getManager();
        $users=$em->getRepository(User::class)->findAll();
         return $this->render('afficheuser.html.twig',[
            
             'users'=>$users
         ]);
 
 
     }
     /**
     * @Route("admin/delateuser/{id}", name="deleteuser")
     */
    public function deleteuser(Request $request,$id): Response
    {
            $entityManager = $this->getDoctrine()->getManager();
            $user=$entityManager->getRepository(User::class)->find($id);
            $entityManager->remove($user);
            $entityManager->flush();

            return $this->redirect($this->generateURL('liste_user'));
    }

    /**
	*@Route("/admin/enable/{id}",name="enable")
    */
    public function enable($id){
       
        $entityManager = $this->getDoctrine()->getManager();
            $user=$entityManager->getRepository(User::class)->find($id);
            $user->setEnabled(1);
            $entityManager->persist($user);
            $entityManager->flush();
        return $this->redirect($this->generateURL('liste_user'));

    }  
    /**
	*@Route("/admin/disable/{id}",name="disable")
    */
    public function disable($id){
        $entityManager = $this->getDoctrine()->getManager();

        $user=$entityManager->getRepository(User::class)->find($id);
        $user->setEnabled(0);
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirect($this->generateURL('liste_user'));

    }  
/**
	*@Route("/vendeur/{id}",name="vendeur")
    */
    public function vendeur(Request $request,$id){
      
        $em= $this->getDoctrine()->getManager();
        
        $users=$em->getRepository(User::class)->find($id);
        $vendeurs=$em->getRepository(Vendeur::class)->findAll();
        $categories=$em->getRepository(Categorie::class)->findAll();

         return $this->render('vendeur.html.twig',[
             'vendeurs'=>$vendeurs,
             'users'=>$users,
             'categories'=>$categories
         ]);
 
 
     }
 
       /**
	*@Route("/prod/{id}",name="vend")
    */
   public function vend($id){
    $em= $this->getDoctrine()->getManager();
   $vendeurs=$em->getRepository(Vendeur::class)->find($id);
   $categories=$em->getRepository(Categorie::class)->findAll($id);
    return $this->render('prod.html.twig',
    [ 'vendeurs'=>$vendeurs,
    'categories'=>$categories]);
}     

     

   
}




 

