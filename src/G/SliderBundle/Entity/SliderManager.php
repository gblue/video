<?php 
namespace G\SliderBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class SliderManager
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
        $item = new $class();

        return $item;
    }

    public function save(Slider $item, $andFlush = true)
    {
        $this->em->persist($item);
        if ($andFlush) {
            $this->em->flush();
        }
        return $item;
    }

    public function get($id)
    {
        $item = $this->repo->findOneById($id);
        if (!$item->getIsHidden()) {
            return $item;
        }else{

        }
    }

    public function getAll($isHidden = null)
    {
        if ($isHidden)
            $isHidden = 0;
        
        $criteria = array('is_hidden' => $isHidden);
        $slides = $this->repo->findBy($criteria, array('position' => 'ASC'));
        return $slides;
    }

}
