<?php

namespace Cheese\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Cheese\Bundle\MainBundle\Form\Type\UserType;
use Cheese\Bundle\MainBundle\Entity\User;

class IndexController extends Controller
{
    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, defaults={"id" = null})
     * @Template()
     */
    public function indexAction($id, Request $request)
    {
        if (!empty($id)) {
            $user = $this->getDoctrine()
            ->getRepository('CheeseMainBundle:User')
            ->find($id);
        } else {
            $user = new User();
        }
  
        $form = $this->createForm(new UserType(), $user);
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            return $this->redirect($this->generateUrl(
                'cheese_main_index_user', 
                ['id' => $user->getId()]
            ));
        }
        
        return array('form' => $form->createView());
    }
    
    
    /**
     * @Route("/user/{id}")
     * @Template()
     */
    public function userAction($id)
    {
        $user = $this->getDoctrine()
            ->getRepository('CheeseMainBundle:User')
            ->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'Not found!'
            );
        }
        
        $risk = $this->get('risk_calculator');
        $risk->setAge($user->getDateOfBirth());
            
        return [
            'user' => $user,
            'risk' => $risk->getRisk(),
            'mortgageRisk' => $risk->getMortgageRisk(),
            'uri' => $this->generateUrl(
                'cheese_main_index_index', 
                ['id' => $user->getId()]
            )
        ];
    }
        
}
