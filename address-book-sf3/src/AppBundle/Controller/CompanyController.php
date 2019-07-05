<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Company;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends Controller
{
    /**
     * @Route("/company/")
     */
    public function listAction()
    {
        $repo = $this->getDoctrine()->getRepository(Company::class);
        $companies = $repo->findAll();

        return $this->render('AppBundle:Company:list.html.twig', [
            'companies' => $companies,
        ]);
    }

    /**
     * @Route("/company/{id}")
     */
    public function showAction($id)
    {
        $repo = $this->getDoctrine()->getRepository(Company::class);
        $company = $repo->find($id);

        return $this->render('AppBundle:Company:show.html.twig', [
            'company' => $company
        ]);
    }

}
