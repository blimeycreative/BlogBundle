<?php

// src/Blogger/BlogBundle/Controller/BlogController.php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Blogger\BlogBundle\Entity\Blog;
use Blogger\BlogBundle\Form\BlogType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Blog controller.
 */
class BlogController extends Controller {
  
  /**
     * @Route("/latest-blogs/summary/{category}/{number_of_posts}/{length}", name="BloggerBlogBundle_blog_summary")
     * @Template()
     */  
  public function latestBlogsSummaryAction($category, $number_of_posts, $length){
    $em = $this->getDoctrine()->getEntityManager();
    $blogs = $em->getRepository('BloggerBlogBundle:Blog')->getLatestBlogsSummary($category, $number_of_posts, $length);
    if($em->getRepository('BloggerBlogBundle:Category')->findOneBySlug($category))
      $category = $em->getRepository('BloggerBlogBundle:Category')->findOneBySlug($category)->getSlug();
    else
      $category = "";
    
    return $this->render('BloggerBlogBundle:Page:latestSummary.html.twig', array(
              'blogs' => $blogs,
              'length' => $length,
              'category' => $category
            ));
  }
  
  /**
     * @Route("/show/{id}", requirements={"id"="\d+"}, name="BloggerBlogBundle_blog_show")
     * @Template()
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
  
  /**
     * @Route("/admin/blog/create", name="BloggerBlogBundle_blog_create")
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
  
  /**
     * @Route("/admin/blog/edit/{id}", name="BloggerBlogBundle_blog_edit")
     * @Template()
     */
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
  
  /**
     * @Route("/{id}/delete", name="BloggerBlogBundle_blog_delete")
     * @Template()
     */
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
  
  /**
     * @Route("/{category}", name="BloggerBlogBundle_blog_category")
     * @Template()
     */
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
     * @Route("/{date}", name="BloggerBlogBundle_blog_date", requirements={"date"="[0-9]{4}\/[a-zA-Z]+"})
     * @Template()
     */
  public function blogsByDateAction($date) {
    $em = $this->getDoctrine()
            ->getEntityManager();

    $blogs = $em->getRepository('BloggerBlogBundle:Blog')
            ->getBlogsByDate($date);

    return $this->render('BloggerBlogBundle:Page:index.html.twig', array(
                'blogs' => $blogs
            ));
  }
    
}