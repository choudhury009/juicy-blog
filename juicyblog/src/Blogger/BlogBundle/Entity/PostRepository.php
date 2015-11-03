<?php
/**
 * Created by PhpStorm.
 * User: jannatul
 * Date: 03/11/15
 * Time: 22:11
 */

namespace Blogger\BlogBundle\Entity;
use Doctrine\ORM\EntityRepository;


class PostRepository extends EntityRepository
{

    public function getLatest($limit, $offset)
    {
        $queryBuilder = $this->createQueryBuilder('post');
        $queryBuilder->orderBy('post.timestamp', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }
}