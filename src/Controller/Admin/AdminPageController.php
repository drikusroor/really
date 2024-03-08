<?php

namespace Ainab\Really\Controller\Admin;

use Ainab\Really\Model\ContentType;
use Ainab\Really\Service\ManageContentService;

class AdminPageController extends AdminContentController
{
    public function __construct(private ManageContentService $manageContentService)
    {
        parent::__construct($manageContentService);
    }
}
