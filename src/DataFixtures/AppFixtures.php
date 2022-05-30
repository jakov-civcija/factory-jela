<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Ingredients;
use App\Entity\Languages;
use App\Entity\Meals;
use App\Entity\Tags;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;
use Gedmo\Translatable\Entity\Translation;

class AppFixtures extends Fixture
{

    /** @var ObjectManager */
    private $manager;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $fakerEn = Factory::create('en_US');
        $fakerDe = Factory::create('de_DE');
        $fakerFr = Factory::create('fr_FR');
        $fakerHr = Factory::create('hr_HR');
        $repository = $manager->getRepository('Gedmo\Translatable\Entity\Translation');

        $language = new Languages();
        $language->setLanguage('en');
        $manager->persist($language);

        $language1 = new Languages();
        $language1->setLanguage('de');
        $manager->persist($language1);

        $language2 = new Languages();
        $language2->setLanguage('fr');
        $manager->persist($language2);

        $language3 = new Languages();
        $language3->setLanguage('hr');
        $manager->persist($language3);

        for ($i = 1; $i < 10; $i++) {
            $meal = new Meals();
            $meal->setStatus('created');
            $meal->setTitle($fakerEn->title);
            $meal->setDescription($fakerEn->text);
            $repository->translate($meal, 'title', 'de', $fakerDe->title)
                ->translate($meal, 'title', 'fr', $fakerFr->title)
                ->translate($meal, 'title', 'hr', $fakerHr->title)
                ->translate($meal, 'description', 'de', $fakerDe->text)
                ->translate($meal, 'description', 'fr', $fakerFr->text)
                ->translate($meal, 'description', 'hr', $fakerHr->text)
            ;



            $tag = new Tags();
            $tag->setTitle($fakerEn->title);
            $tag->setSlug("tag_".$i);
            $repository->translate($tag, 'title', 'de', $fakerDe->title)
                ->translate($tag, 'title', 'fr', $fakerFr->title)
                ->translate($tag, 'title', 'hr', $fakerHr->title)
            ;

            $category = new Category();
            $category->setTitle($fakerEn->title);
            $category->setSlug("category_".$i);
            $repository->translate($category, 'title', 'de', $fakerDe->title)
                ->translate($category, 'title', 'fr', $fakerFr->title)
                ->translate($category, 'title', 'hr', $fakerHr->title)
            ;


            $ingredient = new Ingredients();
            $ingredient->setTitle($fakerEn->title);
            $ingredient->setSlug("ingredient_".$i);
            $repository->translate($ingredient, 'title', 'de', $fakerDe->title)
                ->translate($ingredient, 'title', 'fr', $fakerFr->title)
                ->translate($ingredient, 'title', 'hr', $fakerHr->title)
            ;


            $meal->setCategory($category);
            $meal->addTag($tag);
            $meal->addIngredient($ingredient);
@
            $manager->persist($tag);
            $manager->persist($ingredient);
            $manager->persist($category);
            $manager->persist($meal);

        }

        $manager->flush();
    }
}
