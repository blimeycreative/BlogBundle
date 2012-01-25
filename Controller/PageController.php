<?php

namespace Oxygen\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Oxygen\BlogBundle\Entity\Enquiry;
use Oxygen\BlogBundle\Form\EnquiryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PageController extends Controller {

  /**
   * @Route("/", name="OxygenBlogBundle_homepage")
   * @Template()
   */
  public function indexAction() {
    $em = $this->getDoctrine()
            ->getEntityManager();

    $blogs = $em->getRepository('OxygenBlogBundle:Blog')
            ->getLatestBlogs();

    return $this->render('OxygenBlogBundle:Page:index.html.twig', array(
                'blogs' => $blogs
            ));
  }

  /**
   * @Route("/about", name="OxygenBlogBundle_about")
   * @Template()
   */
  public function aboutAction() {
    return $this->render('OxygenBlogBundle:Page:about.html.twig');
  }

  /**
   * @Route("/test")
   * @Template()
   */
  public function testAction() {
    $em = $this->getDoctrine()->getEntityManager();
    $em->getRepository('blog')->getArchiveList();
  }

  public function sidebarAction() {
    $em = $this->getDoctrine()
            ->getEntityManager();

    $tags = $em->getRepository('OxygenBlogBundle:Blog')
            ->getTags();

    $tagWeights = $em->getRepository('OxygenBlogBundle:Blog')
            ->getTagWeights($tags);

    $commentLimit = $this->container
            ->getParameter('oxygen_blog.comments.latest_comment_limit');
    $latestComments = $em->getRepository('OxygenBlogBundle:Comment')
            ->getLatestComments($commentLimit);

    $categories = $em->getRepository('OxygenBlogBundle:Category')->findAll();

    $archive_dates = $em->getRepository('OxygenBlogBundle:Blog')->getArchiveList();

    return $this->render('OxygenBlogBundle:Page:sidebar.html.twig', array(
                'latestComments' => $latestComments,
                'tags' => $tagWeights,
                'categories' => $categories,
                'archive_dates' => $archive_dates
            ));
  }

}