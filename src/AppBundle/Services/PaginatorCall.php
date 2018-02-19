<?php

namespace AppBundle\Services;

use Symfony\Component\HttpFoundation\Request;

class PaginatorCall
{
    private $paginator;

    

    public function __construct($paginator)
    {
        $this->paginator = $paginator;
    }

    public function paginatorCreator(Request $request, $list, $limit = 4)
    {
        return $this->paginator->paginate(
            $list,
            $request->query->getInt('page', 1)/*page number*/,
            $limit/*limit per page*/
        );
    }
}
