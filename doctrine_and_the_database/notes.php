<?php

/*

    ## DOCTRINE AND THE DATABASE

        1. Installing Doctrine
        
            Symfony has no database access layer so we will be using an external library
            called 'Doctrine'

            installed doctrine 
                composer require doctrine   
                    this donwloads a pack with a few libraries (doctrine, make bundle
                    + a migration library to help manage database changes on production)

            configured root user and password + db name in the .env file (terminal prompts that 
            after installing doctrine)

                named database the_spacebar

            in config/packages/doctrine.yaml we can see the configuration for the database
                e.g. the way the database url is resolved to the .env var we just set

            created database
                ./bin/console doctrine:database:create



        2. Creating an entity class

            Doctrine is an ORM (object relational mapper)
                this means that each table in our DB will have a corresponding class in
                our code
                    e.g an article table => we need an article class

                    the class can be created manually or we can use the cmd below:
                    ./bin/console make:entity 

                        note! -> an entity = a normal PHP class that I can save to the DB

            note the Doctrine ORM has its own types (not varchar like mysql for instance)
            that map to mySQL types (when prompted to choose data type, hit ? to see all the
            available types)

            each successive cmd will prompt me in the terminal (through the >) to add:
                a name for the class, a table property for it, data type for the property,
                etc...
            
                        added the 'title' field
                        added type for data string
                        added length 255
                        choose no for 'can this filed be null in the DB'


                        added the slug field
                        added data type string
                        choose length 100
                        choose no for 'can this filed be null in the DB'

                        added the content field
                        added data type text
                        choose yes for 'can this filled be null in the DB'
                            *maybe articles can be drafted without content at first

                        added a publishedAt field
                        choose  data type datetime
                        choose yes for 'can this filled be null in the DB'

                Hit enter to finish creating the table

                This created an Article.php class in the Entity directory and an 
                ArticleRepository class in the Repository

            The Article class has annotation that tell doctrine that the class is a DB table
            and how to store each table column (data type, default value, etc..) - table
            columns are properties of the entity class


        3. Database migrations

            The Article entity is ready and Doctrine knows how to save its data in  an
            article table in the DB

            We will tell doctrine to create the corresponding table for the entity class with
            migrations 
            
            ran ./bin/console make:migration
                the make:migration cmd looked at our DB and at our entity classes (only 1 for
                now) and generated the SQL required to updated the DB to match our entities

            ran ./bin/console doctrine:migrations:migrate (to execute the migration)
                the migration was executed (if we try to re-run the cmd, nothing happens
                and we get 'no migrations to execute' msg in the terminal)

            ran ./bin/console doctrine:migrations:status (shows status)
                inside the DB the migrations system automatically creates a table called 
                migration_versions
                    the 1st time we run doctrine:migrations:migrate it executed the migration
                    and inserted a new row in the table with the migrations version number
                    (which is a date in the className 20191102140259)
                        when we ran the cmd a 2nd time it opened  the migration class and looked
                        up that version in the migration_versions table (because it was already
                        there, it knew that this migration had already been executed and it did
                        not try to run it again)

            when making a DB change we do 2 steps:
                1. generate migration with make:migration
                2. run migration doctrine:migrations:migrate
                    we will commit the migrations to the git repo
                        on deploy, we run doctrine:migrations:migrate (the prod DB
                        will have its own migration_versions table so all migrations that
                        have not been run in production will be so automatically)

            made the $slug private property in the Entity/Article class unique:
                  
            private $slug;

                     
                     * @ORM\Column(type="text", nullable=true, unique=true)
                     
            this tells Doctrine that it should create a unique index for this column in the 
            DB

            we need to make and run a new migration
                     
            ran ./bin/console make:migration

                This creates a 2nd Version class in the Migrations directory
                    the make:migration cmd looked at the entity and the DB, determined 
                    the difference between the 2 and generated the SQL 
                    necessary to update the DB

            ran ./bin/console doctrine:migrations:migrate (to execute the new migration)

        
        4. Saving Entities to the DB (entities are database items = articles in this case)

            created an ArticleAdminController class in the 'Controller' directory
                this will be used to add new articles
            
            created teh $article item inside the class and set the title,slug,
                note the fact that the setter methods in the Article entity class return
                $this means that we can chain calls onto the Article instance objects
                    ->setTitle()
                        ->setSlug()
                            ...setContent()
                             ...we set data onto the object..
            
            ran ./bin/console debug:autowiring
                we can see that the entity manager service can be typehintd as:
                     Doctrine\ORM\EntityManagerInterface 
            
            typehinted EntityManagerInterface $em to the show() method in the admin
            controller

                note adding an entity to the DB has 2 steps
                       //persist data
                        $em->persist($article);
                        //insert row to DB, get new ID and assign it to article
                        $em->flush();

            tested to see if the data has been persisted to the DB:
                ./bin/console doctrine:query:sql 'SELECT * from article'


            
*/ 

?>