<?php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $blogPosts = $em->getRepository('BloggerBlogBundle:Post')
            ->getLatest(10, 0);
        return $this->render('BloggerBlogBundle:Default:index.html.twig',
            ['blogposts' => $blogPosts]);
    }

}
