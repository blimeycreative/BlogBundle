<?php

// src/Oxygen/BlogBundle/Controller/BlogController.php

namespace Oxygen\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Oxygen\BlogBundle\Entity\Blog;
use Oxygen\BlogBundle\Form\BlogType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/admin")
 */
class AdminController extends Controller {

  /**
   * @Route("/blog/create", name="OxygenBlogBundle_blog_create")
   * @Template()
   */
  public function createAction() {
    $blog = new Blog();
    $request = $this->getRequest();
    $form = $this->createForm(new BlogType(), $blog);
    if ($request->getMethod() == "POST") {
      $form->bindRequest($request);
      if ($form->isValid()) {

        $em = $this->getDoctrine()
                ->getEntityManager();
        $em->persist($blog);
        $em->flush();

        return $this->redirect($this->generateUrl('OxygenBlogBundle_blog_show', array(
                            'id' => $blog->getId()))
        );
      }
    }
    return $this->render('OxygenBlogBundle:Blog:create.html.twig', array(
                'media_form' => $this->get('oxygen.utility.media.factory')->getUploader(),
                'comment' => $blog,
                'form' => $form->createView()
            ));
  }

  /**
   * @Route("/blog/edit/{id}", name="OxygenBlogBundle_blog_edit")
   * @Template()
   */
  public function editAction($id) {
    if ($this->getRequest()->getMethod() == "POST") {
      $em = $this->getDoctrine()->getEntityManager();
      $entity = $em->getRepository('OxygenBlogBundle:Blog')->find($id);

      if (!$entity) {
        throw $this->createNotFoundException('Unable to find Blog entity.');
      }

      $editForm = $this->createForm(new BlogType(), $entity);


      $request = $this->getRequest();

      $editForm->bindRequest($request);

      if ($editForm->isValid()) {
        $em->persist($entity);
        $em->flush();

        $comments = $em->getRepository('OxygenBlogBundle:Comment')
                ->getCommentsForBlog($entity->getId());

        return $this->render('OxygenBlogBundle:Blog:show.html.twig', array(
                    'blog' => $entity,
                    'comments' => $comments
                ));
      }

      return $this->render('OxygenBlogBundle:Blog:edit.html.twig', array(
                  'form' => $editForm->createView()
              ));
    } else {
      $blog = $this->getDoctrine()->getRepository('OxygenBlogBundle:Blog')->find($id);
      if ($blog) {
        $form = $this->createForm(new BlogType(), $blog);
        $deleteForm = $this->createDeleteForm($id);
      } else {
        throw $this->createNotFoundException('Sorry this blog cannot be found');
      }
      return $this->render('OxygenBlogBundle:Blog:edit.html.twig', array(
                  'form' => $form->createView(),
                  'blog' => $blog,
                  'delete_form' => $deleteForm->createView()
              ));
    }
  }

  /**
   * @Route("/{id}/delete", name="OxygenBlogBundle_blog_delete")
   * @Template()
   */
  public function deleteAction($id) {
    $form = $this->createDeleteForm($id);
    $request = $this->getRequest();

    $form->bindRequest($request);

    if ($form->isValid()) {
      $em = $this->getDoctrine()->getEntityManager();
      $entity = $em->getRepository('OxygenBlogBundle:Blog')->find($id);

      if (!$entity) {
        throw $this->createNotFoundException('Unable to find Blog entity.');
      }

      $em->remove($entity);
      $em->flush();
    }

    return $this->redirect($this->generateUrl('OxygenBlogBundle_homepage'));
  }

  private function createDeleteForm($id) {
    return $this->createFormBuilder(array('id' => $id))
                    ->add('id', 'hidden')
                    ->getForm()
    ;
  }

}