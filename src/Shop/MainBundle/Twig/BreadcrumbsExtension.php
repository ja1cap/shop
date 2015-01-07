<?php
namespace Shop\MainBundle\Twig;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Cache\ArrayCache;

/**
 * Class BreadcrumbsExtension
 * @package Shop\MainBundle\Twig
 */
class BreadcrumbsExtension extends \Twig_Extension implements ContainerAwareInterface {

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \Symfony\Component\Routing\Router
     */
    protected $router;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('page_breadcrumbs', [$this, 'getRouteBreadCrumbs'], ['needs_context' => true, 'needs_environment' => true]),
            new \Twig_SimpleFunction('page_title', [$this, 'getRouteTitle'], ['needs_context' => true, 'needs_environment' => true]),
        ];
    }

    /**
     * @param \Twig_Environment $env
     * @param $context
     * @param $routeName
     * @return null
     */
    public function getRouteTitle(\Twig_Environment $env, $context, $routeName){

        if(!isset($routeName) || (isset($routeName) && !$routeName)){
            $routeName = $this->getRequest()->get('_route');
        }

        $breadcrumb = $this->buildRouteBreadCrumb($routeName, $env, $context);

        if($breadcrumb){
            return $breadcrumb['title'];
        }

        return null;

    }

    /**
     * @param \Twig_Environment $env
     * @param $context
     * @param null $routeName
     * @return array
     */
    public function getRouteBreadCrumbs(\Twig_Environment $env, $context, $routeName = null){

        if(!$routeName){
            $routeName = $this->getRequest()->get('_route');
        }

        $breadcrumbs = [];

        $checkParentRoute = true;
        $currentRouteName = $routeName;

        do {

            $breadcrumb = $this->buildRouteBreadCrumb($currentRouteName, $env, $context);

            if($breadcrumb){

                $breadcrumbs[$currentRouteName] = $breadcrumb;
                $parentRouteName = $breadcrumb['parent'];

                if($parentRouteName){
                    $currentRouteName = $parentRouteName;
                } else {
                    $checkParentRoute = false;
                }

            } else {

                $checkParentRoute = false;

            }

        } while($checkParentRoute);

        $breadcrumbs = array_reverse($breadcrumbs);

        return $breadcrumbs;

    }

    /**
     * @param $routeName
     * @param \Twig_Environment $env
     * @param $context
     * @return array|mixed|null
     */
    protected function buildRouteBreadCrumb($routeName, \Twig_Environment $env, $context){

        $breadcrumb = $this->getCache()->fetch($routeName);
        if($breadcrumb){
            return $breadcrumb;
        }

        $collection = $this->getRouter()->getRouteCollection();
        $route = $routeName ? $collection->get($routeName) : null;

        if($route){

            $title = $route->getOption('title');
            if(!$title){
                return null;
            }

            $resources = [];
            $resourceKey = $route->getOption('resourceKey');

            if($resourceKey){

                if(strpos($resourceKey, '||') !== false){

                    $resourceKeys = explode('||', $resourceKey);
                    foreach($resourceKeys as $_resourceKey){

                        if(isset($context[$_resourceKey])){
                            $_resource = $context[$_resourceKey];
                            $resources[$_resourceKey] = $_resource;
                        }

                    }

                } elseif(isset($context[$resourceKey])){

                    $resources[$resourceKey] = $context[$resourceKey];

                }

            }

            $resources = array_filter($resources);

            $routeParameters = [];
            $pathVariables = $route->compile()->getPathVariables();

            if($pathVariables){

                foreach($pathVariables as $pathVariable){

                    $pathVariableValue = null;
                    $resourcePathVariable = strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $pathVariable));

                    foreach($resources as $_resource){
                        if(isset($_resource[$resourcePathVariable])){
                            $pathVariableValue = $_resource[$resourcePathVariable];
                            break;
                        }
                    }

                    if(!$pathVariableValue){
                        $pathVariableValue = $this->getRequest()->get($pathVariable);
                    }

                    if(!$pathVariableValue && !$route->hasDefault($pathVariable)){
                        return null;
                    }

                    $routeParameters[$pathVariable] = $pathVariableValue;

                }

            }

            if(strpos($title, '{{') !== false  || strpos($title, '{%') !== false){

                $title = $env->render($title, $context);

            } else {

                $translationDomain = $route->getOption('translationDomain');
                if(!$translationDomain){

                    $controller = $route->getDefault('_controller');
                    $controllerParts = explode('\\', $controller);

                    if($controllerParts[1] == 'Bundle'){
                        $translationDomain = $controllerParts[0] . $controllerParts[2];
                    } else {
                        $translationDomain = $controllerParts[0] . $controllerParts[1];
                    }

                }

                $titles = explode('||', $title);

                foreach($titles as $_title){

                    $_titleParameters = [];

                    if($resources){

                        preg_match_all("/%(.*)%/", $_title, $matches);
                        $_titleVars = isset($matches[1]) ? $matches[1] : null;

                        if($_titleVars && is_array($_titleVars)){

                            foreach($_titleVars as $_titleVar){

                                $_formattedTitleVar = strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $_titleVar));

                                foreach($resources as $_resourceKey => $_resource){
                                    if(isset($_resource[$_formattedTitleVar])){
                                        $_titleParameters['%' . $_titleVar . '%'] = $_resource[$_formattedTitleVar];
                                        break;
                                    }
                                }

                            }

                        }


                    }

                    $title = $this->getTranslator()->trans($_title, $_titleParameters, $translationDomain);
                    if($title){
                        break;
                    }

                }

            }

            $breadcrumb = array(
                'title' => $title,
                'routeName' => $routeName,
                'routeParameters' => $routeParameters,
                'parent' => $route->getOption('parent'),
            );

            $this->getCache()->save($routeName, $breadcrumb);

            return $breadcrumb;

        } else {

            return null;

        }

    }

    /**
     * @return \Symfony\Component\Routing\Router
     */
    public function getRouter()
    {
        if(!$this->router){
            $this->router = $this->container->get('router');
        }
        return $this->router;
    }

    /**
     * @return \Symfony\Component\Translation\TranslatorInterface
     */
    public function getTranslator()
    {
        if(!$this->translator){
            $this->translator = $this->container->get('translator');
        }
        return $this->translator;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        if(!$this->request){
            $this->request = $this->container->get('request');
        }
        return $this->request;
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getCache()
    {
        if(!$this->cache){
            $this->cache = new ArrayCache();
        }
        return $this->cache;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'breadcrumbs';
    }

}