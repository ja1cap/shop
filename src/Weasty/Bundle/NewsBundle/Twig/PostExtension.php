<?php
namespace Weasty\Bundle\NewsBundle\Twig;

use Weasty\Bundle\NewsBundle\Entity\BasePost;

/**
 * Class PostExtension
 * @package Weasty\Bundle\NewsBundle\Twig
 */
class PostExtension extends \Twig_Extension {

    /**
     * @var \Weasty\Bundle\NewsBundle\Entity\PostRepository
     */
    protected $postRepository;

    function __construct($postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @return array
     */
    public function getFunctions(){
        return array(
            new \Twig_SimpleFunction('weasty_news_post', array($this, 'getPost')),
            new \Twig_SimpleFunction('weasty_news_recent_posts', array($this, 'getRecentPosts')),
        );
    }

    /**
     * @param $identifier
     * @return null|\Weasty\Bundle\NewsBundle\Entity\Post
     */
    public function getPost($identifier){

        $criteria = [];
        if(is_numeric($identifier)){
            $criteria['id'] = (int)$identifier;
        } else {
            $criteria['slug'] = (string)$identifier;
        }

        $orderBy = [
            'createDate' => 'DESC',
        ];

        return $this->postRepository->findOneBy($criteria, $orderBy);

    }

    /**
     * @param int $limit
     * @return \Weasty\Bundle\NewsBundle\Entity\Post[]
     */
    public function getRecentPosts($limit = 3){

        return $this->postRepository->findBy(
            [
                'status' => BasePost::STATUS_ENABLED,
            ],
            [
                'createDate' => 'DESC',
            ],
            (int)$limit
        );

    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'weasty_news_post';
    }

} 