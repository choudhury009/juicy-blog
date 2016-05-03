<?php
namespace Blogger\ApiBundle\Controller;

use Blogger\BlogBundle\Entity\Post;
use Blogger\BlogBundle\Form\PostType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
class BlogController extends FOSRestController
{
    public function getBlogpostsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entries = $em->getRepository('BloggerBlogBundle:Post')->findAll();
        return $this->handleView($this->view($entries));
    }

    public function getBlogpostAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entry = $em->getRepository('BloggerBlogBundle:Post')->find($id);
        if(!$entry) {
            // no blog entry is found, so we set the view
            // to no content and set the status code to 404
            $view = $this->view(null, 404);
        } else {
            // the blog entry exists, so we pass it to the view
            // and the status code defaults to 200 "OK"
            $view = $this->view($entry);
        }

        return $this->handleView($view);
    }

    public function postBlogpostAction(Request $request)
    {
        // prepare the form and remove the submit button
        $blogEntry = new Post();
        $form = $this->createForm(new PostType(), $blogEntry);
        // Point 1 of list above
        if($request->getContentType() != 'json') {
            return $this->handleView($this->view(null, 400));
        }
        // json_decode the request content and pass it to the form
        $form->submit(json_decode($request->getContent(), true));
        // Point 2 of list above
        if($form->isValid()) {
            // Point 4 of list above
            $em = $this->getDoctrine()->getManager();
            $blogEntry->setAuthor($this->getUser());
            $blogEntry->setTimestamp(new \DateTime());
            $em->persist($blogEntry);
            $em->flush();
            // set status code to 201 and set the Location header
            // to the URL to retrieve the blog entry - Point 5
            return $this->handleView($this->view(null, 201)
                ->setLocation(
                    $this->generateUrl('api_blog_get_blogpost',
                        ['id' => $blogEntry->getId()]
                    )
                )
            );
        } else {
            // the form isn't valid so return the form
            // along with a 400 status code
            return $this->handleView($this->view($form, 400));
        }
    }

    public function putBlogpostAction(Request $request)
    {

    }

    public function deleteBlogpostAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $blogPost = $em->getRepository('BloggerBlogBundle:Post')->find($id);
        if(!$blogPost) {
            // no blog entry is found, so we set the view
            // to no content and set the status code to 404
            $view = $this->view(null, 404);
        } else {
            // the blog entry exists, so we pass it to the view
            // and the status code defaults to 200 "OK"
            $em->remove($blogPost);
            $em->flush();

            $view = $this->view(null,200);
        }

        return $this->handleView($view);
    }
}