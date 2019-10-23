<?php

/*
    ## SYMFONY FUNDAMENTALS


        1. Bundles give you services

            a service = an object that does work (e.g. logger service, twig service)
            
            run ./bin/console debug:autowiring (to get all services)

            in order to use a service we pass an argument to the function (an argument which is
            typehinted as the service interface e.g LoggerInterface)
                see ArticleController for reference;

            every service is stored in an object called container
            each service has an internal name (just like routes): logger, twig, router, filesystem,
            etc...

            Bunldes (symfony's plugin system) put the services in the container object

            in the config directory in bundles.php we can see all the bundles we have in our app

            symfony is a colelction of services
            budnles prepare the service objects and put them in the container object
            e.g. Monologue Bundle give us the logger system
            add a bundle => get more services




        
        2. KnpMarkdownBundle & its Services

            go to: http://localhost:8000/news/why-asteroids-taste-like-bacon

            we will process the page content as markdown

            google for knpmarkdownbundle

            the repo: https://github.com/KnpLabs/KnpMarkdownBundle will come up

            copied the composer require cmd: composer require knplabs/knp-markdown-bundle

            installed it in the project:
                a bundle contains some PHP classes and some configuration that loads 1 or more 
                services in our app

            this package modifies among other files the bundle.php file and adds the new bundle:

                Knp\Bundle\MarkdownBundle\KnpMarkdownBundle::class => ['all' => true],

            modified ArticleController.php in the show() method (added the content from the
        template -show.html.twig);

                added:

                 
            $articleContet = <<<EOF
                
                    Spicy **jalapeno bacon** ipsum dolor amet veniam shank in dolore. Ham hock nisi 
                    landjaeger cow,lorem proident [beef ribs](https://baconipsum.com/) aute enim veniam ut cillum pork chuck 
                    picanha. Dolore reprehenderit labore minim pork belly spare ribs cupim short 
                    loin in. Elit exercitation eiusmod dolore cow turkey shank eu pork belly meatball 
                    non cupim.Laboris beef ribs fatback fugiat eiusmod jowl kielbasa alcatra dolore velit ea ball tip. Pariatur
                    laboris sunt venison, et laborum dolore minim non meatball. Shankle eu flank aliqua shoulder,
                    capicola biltong frankfurter boudin cupim officia. Exercitation fugiat consectetur ham. Adipisicing
                    picanha shank et filet mignon pork belly ut ullamco. Irure velit turducken ground round doner incididunt
                    occaecat lorem meatball prosciutto quis strip steak.Meatball adipisicing ribeye bacon strip steak eu. Consectetur ham hock pork hamburger enim strip steak
                    mollit quis officia meatloaf tri-tip swine. Cow ut reprehenderit, buffalo incididunt in filet mignon
                    strip steak pork belly aliquip capicola officia. Labore deserunt esse chicken lorem shoulder tail consectetur
                    cow est ribeye adipisicing. Pig hamburger pork belly enim. Do porchetta minim capicola irure pancetta chuck
                    fugiat.Sausage tenderloin officia jerky nostrud. Laborum elit pastrami non, pig kevin buffalo minim ex quis. Pork belly
                    pork chop officia anim. Irure tempor leberkas kevin adipisicing cupidatat qui buffalo ham aliqua pork belly
                    exercitation eiusmod. Exercitation incididunt rump laborum, t-bone short ribs buffalo ut shankle pork chop
                    bresaola shoulder burgdoggen fugiat. Adipisicing nostrud chicken consequat beef ribs, quis filet mignon do.
                    Prosciutto capicola mollit shankle aliquip do dolore hamburger brisket turducken eu.Do mollit deserunt prosciutto laborum. Duis sint tongue quis nisi. Capicola qui beef ribs dolore pariatur.
                    Minim strip steak fugiat nisi est, meatloaf pig aute. Swine rump turducken nulla sausage. Reprehenderit pork
                    belly tongue alcatra, shoulder excepteur in beef bresaola duis ham bacon eiusmod. Doner drumstick short loin,
                adipisicing cow cillum tenderloin.

            EOF;

            addeed **bacon jalapenos** around the words;

            created a link out of beef ribs: [beef ribs](https://baconipsum.com/)

            replaced the actual HTML in the twig template with articleContent variable:
            
            added the MarkdownInterface as param (typehinted it and added namespace) in the
            ArticleController in show();

            public function show($slug, MarkdownInterface $markdown)

            namespace:     use Michelf\MarkdownInterface; (note the 1 hinted in the error
            log did not work)
                
                Symfonyescapes all raw HTML in the twig templates, if we want it not o be escaped
            we can add the 'raw' filter ->  {{ articleContent | raw }}


                recap:
                    everythign in symfony is done by a service
                    bundles give the services
                    installing new bundles = more services





        3. The Cache service

                note in the console the Cache service appears twice (with same alias cache.app)

                    Psr\Cache\CacheItemPoolInterface
                    Symfony\Component\Cache\Adapter\AdapterInterface

                will cache the markdown transformation (so we don't have to do it on each 
                request)

                modified again the show() method (added the interface and the namespace)

                modified the show() method again:

                           //cache markdown conversion (we pass a markdownkey to getItem() )
                            $item = $cache->getItem('markdown_'.md5($articleContent));
                            //check if key is not aready cached
                            if(!$item->isHit()) {
                                    //set the key (and transform the markdown)
                                    $item->set($markdown->transform($articleContent));
                                    //save the key to the cache
                                    $cache->save($item);
                            }

                            // fetch from the cache
                            $articleContent = $item->get();

                        
                        *note how we are caching the conversion, saving, etc... and then 
                        fetching from cache

        
        4. Configuring a bundle


            in order to view the configuration of a bundle in terminal run:

            ./bin/console config:dump KnpMarkdownBundle (bundle name)

            in order to change the configuration we may want to check the docs

                -created a knp_markdown.yaml file inside config/packages
                -added the configuration from the docs:

                    knp_markdown:
                        parser:
                            service: markdown.parser.light
                
                -ran ./bin/console cache:clear to clear the cache (this is required when we
                add a new configuration file - for changes on existing files Symfony clears 
                the cache automatically - tis is a bug for new files)


        
        5. debug:container & Cache Config

        markdown.parser.light see in knp_markdown.yaml file) is the id of a service in the container
        *all services are in a container object;

        there are many services in a container and each service has its own id;

        ./bin/console debug:autowiring (shows most of the services we'll use)

        ./bin/console debug:autowiring --all   (shows all the services in the container)

        in order to configure the cache service we go to config/packages/cache.yaml



        6. Explore environments and config files

            symfony = a set of routes and a set of services
            the files in config/packages configure these services

            who loads these files? / what s with the directories under config/packages


            our app works like a machine that shows articles, lets ppl login, comment, 
            etc..

            the machine does the same work but it needs a configuration in order to do its
            job 
                            config examples:
                                where to write log files?
                                what DB name and password are?
                                log all messages or just errors?
                                show detailed exception (for debugging) or some error msg
                                    for the user
                                
            symfony has more environments (for configuring the app)

            the 2 environments that come out of the box with each app are prod and dev

                in dev symfony uses a development configuration (shows detailed errors for
                debugging, etc...)

                prod environments logs errors (hides them from UI)

                basic configuration for our app is in index.php and bootstrap.php 
                Kernel.php is a core class of symfony which loads bundles.php 
                in bundles.php we can see that some bundles are loaded in 'all' environments
                (while others only in dev)

                Kernel.php also has a configureContainer (for services) method and 
                configureRoutes() 

                both configureContainer and configureRoutes load the configuration files from 
                config and then look for an environment specific directory (in our case 'dev')
                and the configuration in the environment specific directory will overwrite the 
                default configuration in the packages

    
    7. Leveraging the prod environment

            our app is now in 'dev' mode
            we will change it to 'prod'
            open the .env file and change the line: APP_ENV=dev to APP_ENV=prod

            in prod the internal symfony cache is not automatically rebuild (as the prod
            mode is optimised for speed)

            in order to clear the cache we run: ./bin/console cache:clear (when deploying)
                note that if I make even the smallest change in a template, it will not 
                reflect while in 'prod' mode without manually clearing the cache

                run ./bin/console cache:warmup to clear the cache and rebuild all the cache
                files (the cache:clear only rebuilds some of the files)

            changed environment back to dev

            open the cache.yaml in config/packages
                we want to use the apcu for production but in the dev environment we want to
                use the filesystem cache

                apcu = This adapter is a high-performance, shared memory cache. 
                        It can significantly increase an application's performance, 
                        as its cache contents are stored in shared memory, a component 
                        appreciably faster than many others, such as the filesystem.



                    in order to do that we need to overwrite the declaration: app: cache.adapter.apcu
                    in the dev environment

                created a file called framework.yaml in the packages/dev directory:
                    (copied the keys and changed the filesystem value)

                            framework:

                                cache:

                                    app: cache.adapter.filesystem
                
                now if I dump the $cache variable in the ArticleCOntroller (I can see that
                it's using the FileSystem adapter, but if I change to prod, I will see that
                it is using the Apcuadapter)

                 !I might have to clear the cache when changing from dev to prod
    
    8.Creating Services
        
        open ArticleController and find the show() action

        we'll move the markdown/caching logic to a different file
                reasons: make the method shorter, reuse the code, better testability and
                modularity;

        will create a new service (a new Class)

        created a directory 'Service' in src

        created MarkdownHelper.php class in the Service

        created the class body, added namespace and copied the markdown code into a method of
        the class

        ran ./bin/console debug:autowiring --all (and I can see the new class as a service)
                
        
        added a new argument in the show() method of the ArticleController

        public function show($slug, MarkdownInterface $markdown, $cache,  $markdownHelper)

        simplified the markdown logic code (replaced it all with: below)
            $articleContent = $markdownHelper->parse($articleContent);

        this will throw an error for too few arguments (we need to pass the $cache and 
        $markdownHelper as arguments to the parse() call in the controller)

        now the service works

        however, instead of passing the dependencies via method injection, we can put them
        directly in the constructor (in this case dependencies = $cache/AdapterInterface and 
        $markdown/MarkdownHelper)


        created a __construct() method in the MarkdownHelper

        added the $cache and $markdown as private properties to the class and set them in the
        constructor:

            private $cache;
            private $markdown;

            public function __construct(AdapterInterface $cache, MarkdownInterface $markdown)
            {
                $this->cache = $cache;
                $this->markdown = $markdown;
            }   
        
        added namespace for $cache in the MarkdownHelper class

        removed the $cache and $markdown typehinted arguments in the parse() call in the
        controller

        removed namespaces for $cache and $markdown from the ArticleController

        app still works but we configured a modular service that uses constructor injection
        for dependency management


    9. Using Non-Standard Services: Logger Channels

        added the LoggerInterface namespace in MarkdownHelper + the $logger argument in the
        constructor and the private property in the class

        checked for the 'bacon' string and displayed a log in the prase() method in
        MarkdownHelper class

         //check if 'bacon' string is in the markdown $source
        if(strpos($source, 'bacon') !== false){
            $this->logger->info('They are talking about bacon again...');
        }

        will create a markdown channel logger (that will log all the logs in a different file)
        note the logger interace has multipel 'channe's (if run ./bin/console container:log 
        I can see all channels of the logger interface -debug/router/event/etc...)

        Symfony uses the monolog library for logging (monolog has multiple channes so multiple
        logger objects that log to different files)

        in the profiler (debug tools) we can see the channels

        most logs are saved in a file in dev/dev.log but for instance if we check the
        monolog.yaml file (in config/packages) we can see a line like:
            channels: ["!event"]

        this means that the logs for the event channel are not saved in dev.log

        How can I access one of the other logger objects? WHen we typehint the LoggerInterface
        it gives us the main logger

        will create a new channel for the logger (a channel called markdown)
        anything in this channel will log to a different file

            created a monolog.yaml file in config/packages
                usually the monolog.yaml files are only environment specific (dev or prod)
                we'll create a global configuration for monolog that will be available for
                both environments and will overwrite the environment specific ones

            in the new monolog.yaml file added:
                monolog:
                    channels: ['markdown']


        now if we run ./bin/console debug:container log (we will see the new markdown channel
        in the terminal)

        added a 2nd handler in the monolog.yaml (Dev environment specific one)

              markdown_logging:
            type: stream
            path: "%kernel.logs_dir%/markdown.log"
            level: debug
            channels: ["markdown"]

        if we save and refresh the page it still logs with the app logger

        we need to modifiy some configuration in config/services.yaml

        added our service (and specified default argument, in this case our markdown channel
        logger - note how we passed the id from the terminal):

              App\Service\MarkdownHelper:
                arguments:
                    $logger: '@monolog.logger.markdown'
        
        now the logs are put in a markdown.log file

    10. services.yaml & the Amazing bind

        when symfony loads, it needs to figure out all the services that should be in the 
        container

        most services come from external bundles but we can also add our own services like
        the MarkdownHelper

        all of this happens in services.yaml

        this is where we add our own services

        all the configuration in the file (except for the MarkdownHelper) comes from the default
        symfony app

            the _defaults: (this is a key that sets default config values that should be applied
            to all services that are registered in the file)
                autowire:true (all services should have autowiring turned on
                    if we want to set autowire to false for only 1 service, we can do that
                    in the service config in services.yaml to overwrite the defaults)

            the App\: entry makes all classes in 'src' available as services in the container
                if we run ./bin/console autowiring:true --all (we can see all the classes in
                'src' listed as services in the container)
                any future classes will show here automatically

                this command only lists the services (does not instantiate the objects) services
                are not instantiated unless some1 asks for that

                each service in the container is instantiated a max of once per request (even
                if multiple parts of the code require a service, the service is instantiated only
                once and the instance is passed around in the code)

                the exclude: keyword excludes classes that are not supposed to be in the container
                (only for dev environment)

                at the bottom we configure certain services (add the ID of the service at the 
                bottom - just as for the MarkdownHelper class)
                    note the services id have the same name as their class (if we run:
                            ./bin/console debug:container --show-private we can see that)
                
                we can also replace the old config for the MarkdownService in the services.yaml
                (see configuration in video 9) by using bind in the defaults:

                      bind: 
                        $markdownLogger: '@monolog.logger.markdown'

                we also need to replace the $logger argument in the MarkdownHelper with 
                $markdownLogger

                the 'bind' keyword applies to all services (as it is put in the defaults_)
                    instead of configuring services 1 by one we configure them once, globally
                    respecting best practices

                this is a project-wide convention, next time we need the MarkdownLogger, we 
                can use it by passing $markdownLogger argument defined in the configuration
    
    11. Config parameters
        
        container = the object that holds all the services in the app
        the container can also hold a 2nd type of thing (normal config values)

        open services.yaml

        these config values are called parameters

            in packages/config/cache.yaml we configured the cache services to use the apcu
            extension (in the dev environment through frakework.yaml we overwrite that by
            making the app use the filesystem for cache)

            in any yaml file we can add configuration (as all of them are loaded by the same
            system) we can add any parameter keys:

            in config/packages/framework.yaml add a new param with the value 
                'cache.adapter.apcu:' 

                    parameters:
                        cache_adapter: cache.adapter.apcu


            in cache.yaml replace cache.adapter.apcu with '%cache_adapter%' (that's how we
            pass config variables in symfony)

            in dev/framework.yaml overwrite the initial config with:

                parameters:
                    cache_adapter: 'cache.adapter.filesystem'

            in MarkdownHelper dump the $cache var:   dump($this->cache); die;

        moved all parameters from cache.yaml in services.yaml (instead of having them in 
        multiple files)
        
        note that now the dev config is overwritten by services.yaml

        created a services_dev.yaml file in config

        copied the contents from dev/framework.yaml and deleted dev/framework.yaml

            when the app runs it first checks config/packages/...yaml files (in our case the
            cache.yaml file, here the '%cache_adapter%' param is not defined, but we can
            use it)

            next it reads services.yaml and services_dev.yaml
        
        open MarkdownHelper.php service class
            we will disable caching completely when in dev mode

                added a new bool argument in the constructor $isDebug
                set it as private property
                assign it inside constructor method
                add the value in bind: in services.yaml: 
                          $isDebug: true (note it's hardcoded as true)
        
                run ./bin/console debug:container --parameters
                this lists all the parameters 

                we see that the debug argument we set, is referenced in the console as
                kernel.debug

                replace $isDebug line with: $isDebug: '%kernel.debug%' (note the dynamic 
                config parameter)
    
    12. Constructors for your Controller

        Autowiring works in 2 places:

            1st it works for controller actions (arguments can either have the same name as the
            route wild card - that's not autowiring or have a typehint for a service)


            2nd - the __construct method of services (this is the real place where autowiring
            should work)

            symfony's container and its autowiring logic is good at instantiating objects (the 
            right type)

            in services.yaml (in packages/config) we added an $isDebug bind and used it inside 
            MarkdownHelper
            
            tried using the $isDebug boolean bind in the ArticleController (passed it in the
            show() method and type-hinted it)

            we used not to be able to bind non service arguments to controllers but now we can
            (initially we had to pass the non service argument only to the constructor, now
            it works with any method, set it as property, etc...)

            symfony's container instantiates service objects and uses autowiring to pass values
            to their constructors (any other function than controller actions will not have this
            autowiring) we can define (via bind) what kind of values are passed to specific
            argument names (we can go further and control what value should be passed for a specific
            type-hint)
*/  