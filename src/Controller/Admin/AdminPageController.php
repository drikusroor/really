<?php
namespace Ainab\Really\Controller\Admin;

use Ainab\Really\Controller\BaseController;
use Ainab\Really\Model\Frontmatter;
use Ainab\Really\Service\ManagePageService;

class AdminPageController extends BaseController {

    public function __construct(private ManagePageService $managePageService) {
        parent::__construct();
    }

    public function index($args = []) {

        $pages = $this->managePageService->getPagesList();
        $args['pages'] = $pages;

        echo $this->twig->render('admin/pages/index.html.twig', $args);
    }

    public function save() {
        $formData = $_POST;
        $title = $formData['title'];
        $content = $formData['content'];

        $frontmatter = new Frontmatter($title);
        $slug = $this->managePageService->save($frontmatter, $content);

        return $this->index(['message' => 'Post created!', 'url' => "/$slug"]);
    }

    public function edit($slug, $args = []) {
        $page = $this->managePageService->getPage($slug);
        $args['page'] = $page;

        return $this->index($args);
    }



}