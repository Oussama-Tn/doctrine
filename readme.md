# Doctrine by example


* `composer create-project symfony/website-skeleton doctrine`

* `php bin/console doctrine:database:create`

* `php bin/console make:entity EntityName`

* `php bin/console make:migration`

* `php bin/console doctrine:migrations:migrate`

* `composer require orm-fixtures --dev`

* `composer require fzaninotto/faker`

* `php bin/console make:fixture User`
  
  * Fixture dependencies [read more](https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html)
  
    ``` 
    namespace App\DataFixtures;
    // ...
    use App\DataFixtures\UserFixtures;
    use Doctrine\Common\DataFixtures\DependentFixtureInterface;
    
    class GroupFixtures extends Fixture implements DependentFixtureInterface
    {
        // .....
        
        public function getDependencies()
        {
            return [
                UserFixtures::class,
            ];
        }
    }
    ```

* Make generic repository `BaseRepository` 
    * Read this: https://stackoverflow.com/questions/53747651/how-to-make-a-generic-repository-in-symfony-4
        - 1 - First way: make `BaseRepository` an `abstract class`
        - 2 - Second way: Inside `services.yaml`: prevent symfony from reading the file as a service! 
        ```
            # makes classes in src/ available to be used as services
            # this creates a service per class whose id is the fully-qualified class name
            App\:
                resource: '../src/*'
                exclude: '../src/{DependencyInjection,Entity,Migrations,Tests,Kernel.php,Repository/BaseRepository.php}'
        ```
    
    
