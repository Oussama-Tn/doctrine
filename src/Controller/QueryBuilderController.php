<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Entity\PurchaseOrder;
use App\Entity\PurchaseOrderProduct;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class QueryBuilderController
 * @package App\Controller
 * @Route("/query-builder")
 */
class QueryBuilderController extends AbstractController
{

    // https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/query-builder.html
    // https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/dql-doctrine-query-language.html

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="qb")
     */
    public function index()
    {
        return $this->json(['QueryBuilderController@index']);
    }

    /**
     * Example: /query-builder/like?likeString=/1
     * @Route("/like", name="qb_like")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function like(Request $request)
    {
        $likeString = $request->query->get('likeString') ?? "/4";

        $qb = $this->entityManager->createQueryBuilder();

        $query = $qb->select('p.id, p.name')
            ->from(Product::class, 'p')
            ->andWhere('p.name like :val')
            ->setParameter('val', "%$likeString%")
            ->getQuery();

        return $this->json([
            'URL example' => '/query-builder/like?likeString=/1',
            'query' => $query->getSQL(),
            'parameter:likeString' => $likeString,
            'result' => $query->getResult(),
        ]);
    }

    /**
     * @Route("/order-by", name="qb_order_by")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function orderBy()
    {
        $qb = $this->entityManager->createQueryBuilder();

        $query = $qb->select('p.id', 'p.description', 'p.isAvailable')
            ->from(Product::class, 'p')
            ->addOrderBy('p.isAvailable', 'DESC')
            ->addOrderBy('p.description', 'ASC')
            ->getQuery();

        return $this->json([
            'NOTE:' => '-> orderBy() overrides all previously set ordering conditions. When having multiple ->orderBy() => use ->addOrderBy()',
            'query' => $query->getSQL(),
            'result' => $query->getResult(),
        ]);
    }

    /**
     * @Route("/limit", name="qb_limit")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function limit(Request $request)
    {
        $maxResults = $request->query->get('maxResults') ?? "2";

        $qb = $this->entityManager->createQueryBuilder();

        $query = $qb->select('p')
            ->from(Product::class, 'p')
            ->setMaxResults($maxResults)
            ->getQuery();

        $data = [
            'URL example' => '/limit?maxResults=2',
            'query' => $query->getSQL(),
            'parameter:maxResults' => $maxResults,
            'result' => $query->getResult(),
        ];

        return $this->json($data, 200, [], ['groups' => 'products:read']);
    }

    /**
     * @Route("/max", name="qb_max")
     */
    public function max()
    {
        // https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/query-builder.html
        $qb = $this->entityManager->createQueryBuilder();

        $query = $qb->select("MAX(p.price) AS MAX_PRICE")
            ->from(Product::class, 'p')
            ->getQuery();

        $qb = $this->entityManager->createQueryBuilder();

        $query_expr = $qb
            ->select($qb->expr()->max('pr.price') . " AS MAX_PRICE")
            ->from(Product::class, 'pr')
            ->getQuery();

        return $this->json([
            $query->getSQL(),
            $query->getResult(),
            $query_expr->getSQL(),
            $query_expr->getResult(),
        ]);
    }

    /**
     * @Route("/having", name="qb_having")
     */
    public function having(Request $request)
    {
        $minNumber = $request->query->get('minNumber') ?? 2;

        $qb = $this->entityManager->createQueryBuilder();

        $query = $qb->select('pc.name', "COUNT(p) AS PRODUCT_COUNT")
            ->from(Product::class, 'p')
            ->leftJoin('p.productCategory', 'pc')
            ->groupBy('pc.name')
            ->having("PRODUCT_COUNT > $minNumber")
            ->getQuery();

        // Important! Create another query builder for the second query!
        $qb = $this->entityManager->createQueryBuilder();

        $query_expr = $qb->select('pct.name', $qb->expr()->count('pr') . 'AS PRODUCT_COUNT')
            ->from(Product::class, 'pr')
            ->leftJoin('pr.productCategory', 'pct')
            ->groupBy('pct.name')
            ->having($qb->expr()->gt('PRODUCT_COUNT', $minNumber))
            ->getQuery();

        $data = [
            "info" => [
                'The HAVING clause was added to SQL because the WHERE keyword could not be used with aggregate functions.',
                'The GROUP BY statement groups rows that have the same values into summary rows, like "find the number of customers in each country".
                 The GROUP BY statement is often used with aggregate functions (COUNT, MAX, MIN, SUM, AVG) to group the result-set by one or more columns.',
            ],
            'explain query' => "Product categories having more than {$minNumber} products",
            'URL example' => '/query-builder/having?minNumber=1',
            "query" => [
                $query->getSQL(),
                $query->getResult(),
            ],
            "query_expr" => [
                $query_expr->getSQL(),
                $query_expr->getResult(),
            ],
        ];

        return $this->json($data);
    }


    /**
     * @Route("/inner-join", name="qb_inner_join")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function innerJoin()
    {
        $qb = $this->entityManager->createQueryBuilder();

        $query = $qb->select('u.id', 'u.email')
            ->from(User::class, 'u')
            ->innerJoin('u.purchaseOrders', 'po')
            ->getQuery();

        $result = $query->getResult();

        $data = [
            'info' => 'The INNER JOIN keyword selects records that have matching values in both tables.',
            'query' => $query->getSQL(),
            'result' => $result,
        ];

        return $this->json($data, 200, [], ['groups' => 'users:read']);
    }

    /**
     * @Route("/inner-join-with-group-by", name="qb_inner_join_with_group_by")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function innerJoinWithGroupBy()
    {
        // Important: other ways to do this: using [exists()] or [in()]

        $qb = $this->entityManager->createQueryBuilder();

        // Users having at least one order
        $query = $qb->select('u.id', 'u.email')
            ->from(User::class, 'u')
            ->innerJoin('u.purchaseOrders', 'po')
            ->groupBy('u.id')
            ->getQuery();


        $data = [
            'info' => [
                'The INNER JOIN keyword selects records that have matching values in both tables.',
                'The GROUP BY statement groups rows that have the same values into summary rows, like "find the number of customers in each country".
                 The GROUP BY statement is often used with aggregate functions (COUNT, MAX, MIN, SUM, AVG) to group the result-set by one or more columns.',
            ],
            'explain query' => 'Users having at least one order',
            'query' => $query->getSQL(),
            'result' => $query->getResult(),
        ];

        return $this->json($data, 200, [], ['groups' => 'users:read']);
    }

    /**
     * @Route("/left-join", name="qb_left_join")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function leftJoin()
    {
        $qb = $this->entityManager->createQueryBuilder();

        $productCategoryName = 'Prd Categ 3';

        $query = $qb->select('u.id', 'u.email')
            ->from(User::class, 'u')
            ->leftJoin('u.purchaseOrders', 'po')
            ->leftJoin('po.purchaseOrderProducts', 'pop')
            ->leftJoin('pop.product', 'p')
            ->leftJoin('p.productCategory', 'pc')
            ->where('pc.name like :pc_name')
            ->setParameter(':pc_name', $productCategoryName)
            ->groupBy('u.id')
            ->getQuery();

        $data = [
            'info' => [
                "The LEFT JOIN keyword returns all records from the left table (table1), and the matched records from the right table (table2). The result is NULL from the right side, if there is no match.",
                'The GROUP BY statement groups rows that have the same values into summary rows, like "find the number of customers in each country".
                 The GROUP BY statement is often used with aggregate functions (COUNT, MAX, MIN, SUM, AVG) to group the result-set by one or more columns.',
            ],
            'explain query' => "Users who bought products that belongs to category '{$productCategoryName}'",
            'query' => $query->getSQL(),
            'result' => $query->getResult(),
        ];

        return $this->json($data, 200, [], ['groups' => 'users:read']);
    }

    /**
     * @Route("/not-exists", name="qb_not_exists")
     */
    public function notExists()
    {
        $sqb = $this->entityManager->createQueryBuilder();

        $subQuery = $sqb->select('u')
            ->from(PurchaseOrder::class, 'po')
            ->where('po.user = u.id');

        $qb = $this->entityManager->createQueryBuilder();

        $query = $qb->select("u.id")
            ->from(User::class, "u")
            ->where(
                $qb->expr()->not(
                    $qb->expr()->exists($subQuery)
                )
            )
            ->orderBy("u.id")
            ->getQuery();

        $data = [
            'info' => 'The EXISTS operator is used to test for the existence of any record in a subquery.
                        The EXISTS operator returns true if the subquery returns one or more records.',
            'query' => $query->getSQL(),
            'result' => $query->getResult(),
        ];

        return $this->json($data);
    }

    /**
     * @Route("/exists", name="qb_exists")
     */
    public function exists()
    {
        // Users having at least one order
        $sqb = $this->entityManager->createQueryBuilder();

        $subQuery = $sqb->select('u')
            ->from(PurchaseOrder::class, 'po')
            ->where('po.user = u.id');

        $qb = $this->entityManager->createQueryBuilder();

        $query = $qb->select("u.id")
            ->from(User::class, "u")
            ->where(
                $qb->expr()->exists($subQuery)
            )
            ->orderBy("u.id")
            ->getQuery();

        $data = [
            'info' => 'The EXISTS operator is used to test for the existence of any record in a subquery.
                        The EXISTS operator returns true if the subquery returns one or more records.',
            'query' => $query->getSQL(),
            'result' => $query->getResult(),
        ];

        return $this->json($data);
    }


    /**
     * @Route("/sum", name="qb_sum")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function sum()
    {
        $qb = $this->entityManager->createQueryBuilder();

        $query = $qb->select("SUM(pop.quantity) as TOTAL_PURCHASED_PRODUCT_QUANTITY")
            ->from(PurchaseOrderProduct::class, 'pop')
            ->getQuery();

        $data = [
            'info' => [
                "The SUM() function returns the total sum of a numeric column.",
                "The AVG() function returns the average value of a numeric column.",
                "The COUNT() function returns the number of rows that matches a specified criteria.",
            ],
            'query' => $query->getSQL(),
            'result' => $query->getResult(),
        ];

        return $this->json($data);
    }

    /**
     * @Route("/avg", name="qb_avg")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function avg()
    {
        $qb = $this->entityManager->createQueryBuilder();

        $query = $qb->select("pc.name as CATEGORY_NAME", "AVG(p.price) AS AVERAGE_PRICE")
            ->from(ProductCategory::class, 'pc')
            ->leftJoin('pc.products', 'p')
            ->groupBy('pc.name')
            ->getQuery();

        $data = [
            'info' => [
                "The AVG() function returns the average value of a numeric column.",
                "The SUM() function returns the total sum of a numeric column.",
                "The COUNT() function returns the number of rows that matches a specified criteria.",
            ],
            'query' => $query->getSQL(),
            'result' => $query->getResult(),
        ];

        return $this->json($data);
    }

    /**
     * @Route("/count", name="qb_count")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function count()
    {
        $qb = $this->entityManager->createQueryBuilder();

        $query = $qb->select("u.id as USER_ID", "count(po.id) AS COUNT_PURCHASE_ORDER")
            ->from(User::class, 'u')
            ->leftJoin('u.purchaseOrders', 'po')
            ->groupBy('u.id')
            ->getQuery();

        $data = [
            'info' => [
                "The COUNT() function returns the number of rows that matches a specified criteria.",
                "The AVG() function returns the average value of a numeric column.",
                "The SUM() function returns the total sum of a numeric column.",
            ],
            'query' => $query->getSQL(),
            'result' => $query->getResult(),
        ];

        return $this->json($data);
    }
}
