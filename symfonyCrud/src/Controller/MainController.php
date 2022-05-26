<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Crud;
use App\Form\CrudType;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;


class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(PersistenceManagerRegistry $doctrine): Response
    {
        $data = $doctrine -> getRepository(Crud::class)->findAll();
        return $this->render('main/index.html.twig', [
            'list' => $data,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, PersistenceManagerRegistry $doctrine) {
        $crud = new Crud();
        $form = $this -> createForm(CrudType::class, $crud);
        $form -> handleRequest($request);

        if($form->isSubmitted() && $form -> isValid()) {
            $em = $doctrine -> getManager();
            $em -> persist($crud);
            $em -> flush();

            $this -> addFlash('note', 'Zatwierdzono pomyślnie!');

            return $this -> redirectToRoute('app_main');
        }


        return $this->render('main/create.html.twig', [
            'form' => $form -> createView()
        ]);

    }

    #[Route('/update/{id}', name: 'update')]
    public function update(Request $request, PersistenceManagerRegistry $doctrine, $id) {
        
        $crud = $doctrine -> getRepository(Crud::class)->find($id);
        $form = $this -> createForm(CrudType::class, $crud);
        $form -> handleRequest($request);

        if($form->isSubmitted() && $form -> isValid()) {
            $em = $doctrine -> getManager();
            $em -> persist($crud);
            $em -> flush();

            $this -> addFlash('note', 'Zaktualizowano pomyślnie!');

            return $this -> redirectToRoute('app_main');
        }

        return $this->render('main/update.html.twig', [
            'form' => $form -> createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id, PersistenceManagerRegistry $doctrine) {
        $data = $doctrine -> getRepository(Crud::class) -> find($id);
        $em = $doctrine -> getManager();
        $em -> remove($data);
        $em -> flush();

        $this -> addFlash('note', "Usunięto pomyślnie!");

        return $this -> redirectToRoute('app_main');
    }

}
