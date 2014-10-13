<?php
namespace Shop\MainBundle\Twig;

/**
 * Class BreadcrumbsExtension
 * @package Shop\MainBundle\Twig
 */
class BreadcrumbsExtension extends \Twig_Extension {

    /**
     * @var \Symfony\Component\Routing\Router
     */
    protected $router;

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('page_breadcrumbs', [$this, 'getRouteBreadCrumbs'], ['needs_context' => true]),
        ];
    }

    /**
     * @param $context
     * @param null $routeName
     * @return array
     */
    public function getRouteBreadCrumbs($context, $routeName = null){

        /**
         * @var $app \Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables
         */
        $app = $context['app'];
        $request = $app->getRequest();

        if(!$routeName){
            $routeName = $request->get('_route');
        }

        $breadcrumbs = [];

        $checkParentRoute = true;
        $currentRouteName = $routeName;

        do {

            $breadcrumb = $this->buildRouteBreadCrumb($currentRouteName, $context, $request);

            if($breadcrumb){

                $breadcrumbs[$currentRouteName] = $breadcrumb;

                /**
                 * @var $route \Symfony\Component\Routing\Route
                 */
                $route = $breadcrumb['route'];
                $parentRouteName = $route->getOption('parent');

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
     * @param $context
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array|null
     */
    protected function buildRouteBreadCrumb($routeName, $context, $request){

        $collection = $this->getRouter()->getRouteCollection();
        $route = $routeName ? $collection->get($routeName) : null;

        if($route){

            $title = $route->getOption('title');
            if(!$title){
                return null;
            }

            $translationDomain = $route->getOption('translationDomain');
            if(!$translationDomain){

                $controller = $route->getDefault('_controller');
                $controllerParts = explode('\\', $controller);

                if($controllerParts[1] == 'Bundle'){
                    $translationDomain = $controllerParts[0] . $controllerParts[2];
                } else {
                    $translationDomain = $controllerParts[0] . $controllerParts[1];
                }

                $route->setOption('translationDomain', $translationDomain);

            }

            $resource = null;
            $resourceKey = $route->getOption('resourceKey');
            if($resourceKey && isset($context[$resourceKey])){

                $resource = $context[$resourceKey];

            }

            $routeParameters = [];
            $pathVariables = $route->compile()->getPathVariables();

            if($pathVariables){

                foreach($pathVariables as $pathVariable){

                    $resourcePathVariable = strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $pathVariable));

                    if(isset($resource[$resourcePathVariable])){

                        $pathVariableValue = $resource[$resourcePathVariable];

                    } else {

                        $pathVariableValue = $request->get($pathVariable);

                    }

                    if(!$pathVariableValue && !$route->getDefault($pathVariable)){
                        return null;
                    }

                    $routeParameters[$pathVariable] = $pathVariableValue;

                }

            }

            $titleParameters = [];
            if($resource){

                preg_match_all("/%(.*)%/", $title, $matches);
                $titleVars = isset($matches[1]) ? $matches[1] : null;

                if($titleVars && is_array($titleVars)){

                    foreach($titleVars as $titleVar){

                        $titleVar = strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $titleVar));
                        if(isset($resource[$titleVar])){
                            $titleParameters['%' . $titleVar . '%'] = $resource[$titleVar];
                        }

                    }

                }

            }

            $breadcrumb = array(
                'title' => $title,
                'titleParameters' => $titleParameters,
                'translationDomain' => $route->getOption('translationDomain'),
                'resourceKey' => $route->getOption('resourceKey'),
                'routeName' => $routeName,
                'routeParameters' => $routeParameters,
                'route' => $route,
            );

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
        return $this->router;
    }

    /**
     * @param \Symfony\Component\Routing\Router $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
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