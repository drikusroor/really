<?php
namespace Ainab\Really\Controller\Admin;

use Ainab\Really\Controller\BaseController;
use Ainab\Really\Model\Frontmatter;
use Ainab\Really\Service\ManagePageService;

class AdminPageController extends BaseController {

    public function __construct(private ManagePageService $managePageService) {
        parent::__construct();
    }

    public function index() {
        echo $this->twig->render('admin/pages/index.html.twig');
    }

    public function post() {
        $formData = $_POST;
        $title = $formData['title'];
        $content = $formData['content'];

        $frontmatter = new Frontmatter($title);
        $slug = $this->managePageService->save($frontmatter, $content);

        echo $this->twig->render('admin/pages/index.html.twig', ['message' => 'Post created!', 'url' => "/$slug"]);
    }
}