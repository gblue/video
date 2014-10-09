<?php 
namespace G\AccentsBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AccentManager
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


    /**
     * @return Comment
     */
    public function create()
    {
        $class = $this->class;
        $comment = new $class();

        return $comment;
    }

    public function save(Accent $accent, $andFlush = true)
    {
        $this->em->persist($accent);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    public function delete(Accent $accent, $andFlush = true)
    {
        $this->em->remove($accent);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    public function get($id)
    {
        $accent = $this->repo->findOneById($id);
        if (!$accent->getIsHidden()) {
            return $accent;
        }else{
            throw new NotFoundHttpException("Акцентът не намерен!");
        }
    }

    public function getAll($criteria = array(), $orderBy = null, $limit = null, $offset = null)
    {
        $baseCriteria = array('isHidden' => 0, 'isVisible' => 1);
        $finalCriteria = array_merge($baseCriteria, $criteria);
        $accents = $this->repo->findBy($finalCriteria, $orderBy, $limit, $offset);
        return $accents;
    }
}
