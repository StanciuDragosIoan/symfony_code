<?
/*

    App structure:

        'public' is the document root for all public assets
            index.php is the front controller (entry point for the app)

        'config' holds configuration files

        'src' contains the source code



    Some cmds used:

        composer require server 

        ./bin/console/server:run (starts server)   -> this works because the project has a 'bin' 
        directory with a console file inside


    ### Routes/Controllers/Pages

        route = configuration that defines the url for a page

        controller = a function that we write that builds the content for that page

        the main routing file is in config/routes.yaml

        a controller must return a symfony response object

            I created a first controller and a homepage

                in the Controller directory created an ArticleController class
                    the class uses the namespace App\Controller and the 
                    Symfony\Component\HttpFoundation\Response namespace and it
                    has a single method home() which returns a symfony Response()
                
                    
                in the config/routes.yaml file I added the route for the page:
                        path: /
                        controller: App\Controller\ArticleController::homepage()

                # ran composer requier annotations

                commented out the .yaml route

                defined the route through an annotation in the ArticleController
                    imported the namespace:
                        use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
                    added the route annotation (symfony allows for routes to be defined
                    either in the .yaml file or directly in the controller through an annotation)



    ### Symfony Flex and Aliases

        
        # I ran composer require sec-checker --dev

        Flex  (symfony/flex) is a composer plug-in that does 2 things:
                    
                    1. the Alias system (https://flex.symfony.com/)
                        recipes are sets of instructions ran in the terminal through a shortuct
                        command

                    2. recipes (sets of instructions)
                        if when installing a package, it has a recipe, the recipe will be ran
                        recipes can modify configuration/files/create directories/update the
                        .gitignore file, etc..

        symfony.lock keeps track of which recipes have been installed

        ran ./bin/console security:check (to check if any package has a security vulnerability)

        

    ###  The Twig Recipe
    
        the symfony response can render templates/make API requests/DB queries, etc... 
        ran composer requier twig
         
        this modified bundles.php among other files (bundles.php keeps track of all 3rd party
        bundles)

    
    ###  Twig
        
        opened ArticleController.php and made it extend AbstractController + added namespace
        removed the  return new Response() and replaced with 
        return $this->render();

        twig has 3 types of syntax:

        1. {{ variable }}  (prints the variable)

        2. {%%}   (does something/performs logic)

        3. {##}  (comments syntax)

        passed a template to render and an array with elements of the template

        opened: http://localhost:8000/news/why-asteroids-taste-like-bacon

        added a $comments var inside show() method of controler

        passed $comments to a 'comments' variable in the template (array param of render() )

        looped over comments in twig inside {%%} and displayed them on page:

         {% for comment in comments %}
            <li>{{comment}}</li>
        {% endfor %}

        added a 'filter' to the Comments title
        {{comments|length}}   (note the pipe character that prints the number of comments)

        made the template extend the base template:
        {% extends 'base.html.twig' %}

        wrapped the content in blocks:

        {% block body %} {% endblock %}
        {% block title %} {% endblock %}

    ###  Web Debug Toolbar and the profiler

        ran composer require profiler --dev

        this added a debugger toolbar at the top of the page with many features
        note the dump() method that prints variables nicely coloured on the screen
         //dump($slug, $this);

        use dump like this in twig templates: {{ dump() }} -no param


    ### Debugging and packs
        
        ran composer require debug --dev (to get all the debugging tools)

        packs are bundled packages installed together with a single command (e.g. 
        installing 6 packages for debug with a single cmd)

        ran composer unpack debug (to split the packages in the composer.json)

        instead of having a single debug pack containing all the  composing packages:
        "symfony/debug-pack": "^1.0",

        we now have each individual package in the composer.json file:

        "easycorp/easy-log-handler": "^1.0.7",
        "sensiolabs/security-checker": "^6.0",
        "symfony/debug-bundle": "*",
        "symfony/monolog-bundle": "^3.0",
        "symfony/profiler-pack": "*",
        "symfony/var-dumper": "*"

    
    ### Assets CSS & JS

        replaced the base.html.twig from the templates with the 1 from the tutorial directory
        ran rm -rf var/cache/dev* to clear the cache (symfony does that automatically)
            might be required when we copy into a template file without actually modifiying 
            the file
        
        copied the css,fonts and images directory from tutorial to public
        referenced CSS and images in the base.html.twig

        ran composer require asset and wrapped the path to .css in asset() in twig block

        images can also be used with asset()
        
        <link rel="stylesheet" href="{{ asset('/css/font-awesome.css') }}">
        <img src="{{ asset('images/astronaut-profile.png') }}">
        
        copied the contents from article.html.twig from tutorial to the show.html.twig template

        replaced the title and comments (hard coded ) with dynamic data from controller


    
    ### Generating URLs

        configured the route for the homepage:
        ran ./bin/console debug:router to see all routes in the app
            note this shows us the routes names:
                for homepage its:
                    app_article_homepage 
                we can reference these names
        
        copied the name for homepage route and added it to the anchor tag on the nav in the
        base.html.twig:
            href="{{ path('app_article_homepage')}}"
        
        now the homepage route works

        changed the homepage annotation (added a 2nd param - the route name):

         
         *@Route("/", name="app_homepage")
         
    
        ran ./bin/console debug:router and now the homepage route name changed from 
        app_article_homepage to app_homepage;

        changed the route in the show.html.twig route to the new name (and it works)

        added an html template for the homepage in the home() in the controller:
            return $this->render('article/homepage.html.twig');
        
        created the template and added a route to it:
            gave the route a name:
            *@Route("/news/{slug}", name="article_show")
            added the variable as associative array in the twig template:
                {slug: 'why-asteroids-taste-like-bacon'}
            now the route works


    ### JS and page specific assets
            
        created a 'js' directory in the public directory
        created article_show.js inside 'js'
        added some JS code 

        included the js file in the show.html.twig template
            we could add it in base.html.twig (but we want this js file only on article_show
            page)

            if we add it in the body block of the article.show.twig, it will appear too early
            (as the JS block is after the body)

            we have to overwrite the JS block in show.html.twig.show:
            
            
            {% block javascripts %}
                {{ parent() }}
            <script src="{{asset('js/article_show.js')}}"></script> 
            {% endblock %}

            *note how we append the parent() js block (with JQ and all dependencies) before
            our file

    
    ### JSON API endpoint

        when clicking the heart icon we will send an ajax request to the server that will
        update the DB and show our like

        the API endpoint needs to return the new number of hearts to show on the page

        created a new method in the ArticleController.php (note the route and the 
        fact that method can only be POST)

        **
         * @Route("/news/{slug}/heart", name="article_toggle_heart", methods={"POST"});
         *
        public function toggleArticleHeart($slug)
        {
            //TODO - actually heart/unheart the article

            return new JsonResponse(['hearts' => rand(5,100)]);
        }

        in the show.html.twig, modified the href attribute of the heart icon:
            href="{{ path('article_toggle_heart', {slug: 'slug'})}}"
        modified the show() meth in ArticleController.php (added the slug param):
            
              public function show($slug)
        {   

            $comments = [
                'Comment One for post One',
                'Comment Two for post Two',
                'Comment 3 for Post 3'
            ];


        // dump($slug, $this);

            return $this->render('article/show.html.twig', [
                'title'=> ucwords(str_replace('-', ' ', $slug)),
                'slug' => $slug,  //added the slug as variable
                'comments' => $comments
            ]);
        }

        added te ajax call in the artcile_show.js file:

        $.ajax({
            method: 'POST',
            url: $link.attr('href')
        }).done((data)=>{
            $('.js-like-article-count').html(data.hearts);
        });

        now in the browser, when we click the heart, it makes a post request and it returns the
        number of hearts and sets it as the actual number of hearts in the html template


    ### SERVICES
        
        symfony = a bunch of useful objects that do stuff
            router object = object that matches routes and generates URLs
            twig object = object that renders templates
            log object = object that symfony uses to store logs in var/log/dev.log

        these objects are also called services (a service is an object that 'does wor' like
        generating URLs, sending emails, saving things to a database)

        ran tail -f var/log/dev.log (displays the logs)

        in order to use a service, we pass it as argument to the method (and we type-hint it)
            e.g. 
        
                public function toggleArticleHeart($slug, LoggerInterface $logger)
                {
                    //TODO - actually heart/unheart the article

                    return new JsonResponse(['hearts' => rand(5,100)]);
                }
        
        in symfony anything is a service (an object with methods that does something)

*/

