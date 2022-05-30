<?php

namespace App\Controller;

use App\Entity\Meals;
use App\Repository\MealsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;



class MealController extends AbstractController
{
    /**
     * @Route("/meal", name="app_meal")
     */
    public function index(Request $request, MealsRepository $mealsRepository, ValidatorInterface $validator): Response
    {
        $perPage = $request->query->get('per_page', 5);
        $tags = $request->query->get('tags', []);
        $lang = $request->query->get('lang');
        $with = $request->query->get('with', null);
        $diffTime = $request->query->get('diff_time', null);
        $page = $request->query->get('page', 1);
        $category = $request->query->get('category', -1);
        $categoryId = null;
        $hasToHaveCategory = null;

        $errors = [];
        $languages = ['en', 'fr', 'ch', 'hr', 'de'];

        if (!$lang)
        {
            array_push($errors, 'Parameter "lang" must be specified');
        }
        if (!in_array($lang, $languages))
        {
            array_push($errors, "Please select a valid language: en, fr, ch, hr, de");
        }

        if($category !== -1)
        {
            if ($category === '!NULL')
            {
                $hasToHaveCategory = true;
            }elseif ($category === 'NULL' || $category === null)
            {
                $hasToHaveCategory = false;
            }else{
                if (!is_numeric($category))
                {
                    array_push($errors, "Category must be a number or value !NULL or NULL");
                }else{
                    $categoryId = (int)$category;
                }
            }
        }

        if (!is_numeric($perPage) || !is_int((int)$perPage))
        {
            array_push($errors, "Per page must be a number");
        }

        if ($diffTime !== null && !is_numeric($diffTime) || !is_int((int)$diffTime))
        {
            array_push($errors, "Diff time must be a number");
        }

        if (!is_numeric($page) || !is_int((int)$page))
        {
            array_push($errors, "Page must be a number");
        }

        if ($tags !== null)
        {
            if (is_string($tags))
            {
                $tagsAsString = explode(',', str_replace(' ', '', $tags));
                $tags = [];
                foreach ($tagsAsString as $tag)
                {
                    if (is_numeric($tag))
                    {
                        $tags[] = (int)$tag;
                    }else{
                        array_push($errors, "Tag is not a number");
                    }
                }
            }
        }

        $serializeCategory = false;
        $serializeTags = false;
        $serializeIngredients = false;
        if ($with !== null)
        {

            if (is_string($with))
            {
                $withAsString = explode(',', str_replace(' ', '', $with));
                foreach ($withAsString as $item)
                {
                    if ($item !== 'category' && $item !== 'tags' && $item !== 'ingredients')
                    {
                        array_push($errors, 'With must have values: "category" or "tags" or "ingredients"');
                    }


                    if ($item === 'category')
                    {
                        $serializeCategory = true;
                    }
                    if ($item === 'tags')
                    {
                        $serializeTags = true;
                    }
                    if ($item === 'ingredients')
                    {
                        $serializeIngredients = true;
                    }
                }
            }
        }

        if (count($errors) > 0) {
            return $this->json($errors);
        }




        $products = $mealsRepository->fetchByParams($categoryId, $hasToHaveCategory, $tags, $lang, $with, $diffTime, $page, $perPage);

        return $this->json(
            [
                'product' => array_map(fn(Meals $meal) => $meal->jsonSerialize($serializeCategory,$serializeTags,$serializeIngredients),$products)
            ]
        );
    }
}
