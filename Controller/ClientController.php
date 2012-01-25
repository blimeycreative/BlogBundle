<?php

// src/Oxygen/BlogBundle/Controller/BlogController.php

namespace Oxygen\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Oxygen\BlogBundle\Entity\Blog;
use Oxygen\BlogBundle\Form\BlogType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/news")
 */
class ClientController extends Controller {

  /**
   * @Route("/latest-blogs/summary/{category}/{number_of_posts}/{length}", name="OxygenBlogBundle_blog_summary")
   * @Template()
   */
  public function latestBlogsSummaryAction($category, $number_of_posts, $length) {
    $em = $this->getDoctrine()->getEntityManager();
    $blogs = $em->getRepository('OxygenBlogBundle:Blog')->getLatestBlogsSummary($category, $number_of_posts, $length);
    if ($em->getRepository('OxygenBlogBundle:Category')->findOneBySlug($category))
      $category = $em->getRepository('OxygenBlogBundle:Category')->findOneBySlug($category)->getSlug();
    else
      $category = "";

    return $this->render('OxygenBlogBundle:Page:latestSummary.html.twig', array(
                'blogs' => $blogs,
                'length' => $length,
                'category' => $category
            ));
  }

  /**
   * @Route("/show/{id}", requirements={"id"="\d+"}, name="OxygenBlogBundle_blog_show")
   * @Template()
   */
  public function showAction($id) {
    $em = $this->getDoctrine()->getEntityManager();

    $blog = $em->getRepository('OxygenBlogBundle:Blog')->find($id);

    if (!$blog) {
      throw $this->createNotFoundException('Unable to find Blog post.');
    }

    $comments = $em->getRepository('OxygenBlogBundle:Comment')
            ->getCommentsForBlog($blog->getId());

    return $this->render('OxygenBlogBundle:Blog:show.html.twig', array(
                'blog' => $blog,
                'comments' => $comments
            ));
  }

  /**
   * @Route("/{category}", name="OxygenBlogBundle_blog_category")
   * @Template()
   */
  public function blogsByCategoryAction($category) {
    $em = $this->getDoctrine()
            ->getEntityManager();

    $blogs = $em->getRepository('OxygenBlogBundle:Blog')
            ->getLatestBlogsForCategory($category);

    return $this->render('OxygenBlogBundle:Page:index.html.twig', array(
                'blogs' => $blogs
            ));
  }

  /**
   * @Route("/{date}", name="OxygenBlogBundle_blog_date", requirements={"date"="[0-9]{4}\/[a-zA-Z]+"})
   * @Template()
   */
  public function blogsByDateAction($date) {
    $em = $this->getDoctrine()
            ->getEntityManager();

    $blogs = $em->getRepository('OxygenBlogBundle:Blog')
            ->getBlogsByDate($date);

    return $this->render('OxygenBlogBundle:Page:index.html.twig', array(
                'blogs' => $blogs
            ));
  }

}