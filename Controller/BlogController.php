<?php

// src/Blogger/BlogBundle/Controller/BlogController.php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Blogger\BlogBundle\Entity\Blog;
use Blogger\BlogBundle\Form\BlogType;

/**
 * Blog controller.
 */
class BlogController extends Controller {

  public function blogsByCategoryAction($category) {
    $em = $this->getDoctrine()
            ->getEntityManager();

    $blogs = $em->getRepository('BloggerBlogBundle:Blog')
            ->getLatestBlogsForCategory($category);

    return $this->render('BloggerBlogBundle:Page:index.html.twig', array(
                'blogs' => $blogs
            ));
  }

  /**
   * Show a blog entry
   */
  public function showAction($id) {
    $em = $this->getDoctrine()->getEntityManager();

    $blog = $em->getRepository('BloggerBlogBundle:Blog')->find($id);

    if (!$blog) {
      throw $this->createNotFoundException('Unable to find Blog post.');
    }

    $comments = $em->getRepository('BloggerBlogBundle:Comment')
            ->getCommentsForBlog($blog->getId());

    return $this->render('BloggerBlogBundle:Blog:show.html.twig', array(
                'blog' => $blog,
                'comments' => $comments
            ));
  }
  
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

        return $this->redirect($this->generateUrl('BloggerBlogBundle_blog_show', array(
                            'id' => $blog->getId()))
        );
      }
    }
    return $this->render('BloggerBlogBundle:Blog:create.html.twig', array(
                'comment' => $blog,
                'form' => $form->createView()
            ));
  }

  public function editAction($id) {
    if ($this->getRequest()->getMethod() == "POST") {
      $em = $this->getDoctrine()->getEntityManager();
      $entity = $em->getRepository('BloggerBlogBundle:Blog')->find($id);

      if (!$entity) {
        throw $this->createNotFoundException('Unable to find Blog entity.');
      }

      $editForm = $this->createForm(new BlogType(), $entity);


      $request = $this->getRequest();

      $editForm->bindRequest($request);

      if ($editForm->isValid()) {
        $em->persist($entity);
        $em->flush();

        $comments = $em->getRepository('BloggerBlogBundle:Comment')
                ->getCommentsForBlog($entity->getId());

        return $this->render('BloggerBlogBundle:Blog:show.html.twig', array(
                    'blog' => $entity,
                    'comments' => $comments
                ));
      }

      return $this->render('BloggerBlogBundle:Blog:edit.html.twig', array(
                  'form' => $editForm->createView()
              ));
    } else {
      $blog = $this->getDoctrine()->getRepository('BloggerBlogBundle:Blog')->find($id);
      if ($blog) {
        $form = $this->createForm(new BlogType(), $blog);
        $deleteForm = $this->createDeleteForm($id);
      } else {
        throw $this->createNotFoundException('Sorry this blog cannot be found');
      }
      return $this->render('BloggerBlogBundle:Blog:edit.html.twig', array(
                  'form' => $form->createView(),
                  'blog' => $blog,
                  'delete_form' => $deleteForm->createView()
              ));
    }
  }

  public function deleteAction($id) {
    $form = $this->createDeleteForm($id);
    $request = $this->getRequest();

    $form->bindRequest($request);

    if ($form->isValid()) {
      $em = $this->getDoctrine()->getEntityManager();
      $entity = $em->getRepository('BloggerBlogBundle:Blog')->find($id);

      if (!$entity) {
        throw $this->createNotFoundException('Unable to find Blog entity.');
      }

      $em->remove($entity);
      $em->flush();
    }

    return $this->redirect($this->generateUrl('BloggerBlogBundle_homepage'));
  }

  private function createDeleteForm($id) {
    return $this->createFormBuilder(array('id' => $id))
                    ->add('id', 'hidden')
                    ->getForm()
    ;
  }

}