<?php


namespace App\Http\ApiControllers\ItemTag;

use App\Http\ApiControllers\Controller;
use App\Repositories\ItemTag\ItemTagRepositoryInterface;
use Illuminate\Http\Request;
use App\Utils\Json\ResponseTrait;


class ItemTagController extends Controller
{
    private $itemTag;
    use ResponseTrait;

    public function __construct(ItemTagRepositoryInterface $itemTag)
    {
        $this->itemTag = $itemTag;
    }

    public function index()
    {
        // Todo

    }

    public function show()
    {
        // Todo

    }

}