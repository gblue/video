<?php 
namespace G\ContentBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentManager
{
    /**
     * Holds the Doctrine entity manager for database interaction
     * @var EntityManager 
     */
    protected $em;

    /**
     * Entity-specific repo, useful for finding entities, for example
     * @var EntityRepository
     */
    protected $repo;


    protected $andFlush;

    /**
     * The Fully-Qualified Class Name for our entity
     * @var string
     */
    protected $class;

    public function __construct(EntityManager $em, $class)
    {
        // Even though we have three properties, we only need two constructor arguments...
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
        // ... because we can find the repo using those two
    }
    
    public function getRepository()
    {
        return $this->repo;
    }


    /**
     * @return Content
     */
    public function create()
    {
        $class = $this->class;
        $content = new $class();

        return $content;
    }

    public function save(Content $content, $andFlush = true)
    {
        $this->em->persist($content);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    public function delete(Content $content)
    {
        $this->repo->removeFromTree($content);
        if ($andFlush) {
            $this->em->flush();
        }
        return true;
    }

    public function get($id)
    {
        $content = $this->repo->findOneById($id);
        if (!$content->getIsHidden()) {
            return $content;
        }else{
            throw new NotFoundHttpException("Текстовата страница не е намерена!");
        }
    }




    public function getByLevel($level = 0)
    {
        $criteria = array('isHidden' => 0,'isVisible' => 1, 'lvl' => $level);
        $contentPages = $this->repo->findBy($criteria);
        return $contentPages;
    }

    public function getBySlug($slug)
    {
        $content = $this->repo->findOneBySlug($slug);
        if (!$content->getIsHidden()) {
            return $content;
        }else{
            throw new NotFoundHttpException("Текстовата страница не е намерена!");
        }
    }

    /**
     * Get list of leaf nodes of the tree
     *
     * @param object $root        - root node in case of root tree is required
     * @param string $sortByField - field name to sort by
     * @param string $direction   - sort direction : "ASC" or "DESC"
     *
     * @return array
     */
    public function getLeafs(Content $root, $sortByField = null, $direction = 'ASC')
    {
        return $this->repo->getLeafsQuery($root, $sortByField, $direction)->getResult();
    }

    /**
     * Get list of leaf nodes of the tree
     *
     * @param object $root        - root node in case of root tree is required
     * @param boolean $direct     - direct chilren or not 
     * @param string $sortByField - field name to sort by
     * @param string $direction   - sort direction : "ASC" or "DESC"
     * @param boolean $includeNode- return with or without root
     *
     * @return array
     */
    public function getActiveChildren(Content $root, $direct = true, $sortByField = null, $direction = 'ASC', $includeNode = false)
    {
        $criteria = array('isHidden' => 0);
        $children = $repo->getChildren($root, $direct, $sortByField, $direction, $includeNode);
        return $children;
    }

    public function getAll($criteria = array(), $orderBy = array('rank' => 'ASC'), $limit = null, $offset = null)
    {
        $baseCriteria = array('isHidden' => 0, 'isVisible' => 1);
        $finalCriteria = array_merge($baseCriteria, $criteria);
        $accents = $this->repo->findBy($finalCriteria, $orderBy, $limit, $offset);
        return $accents;
    }

}
