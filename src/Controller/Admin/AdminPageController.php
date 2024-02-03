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
        $id = $formData['id'];
        $title = $formData['title'];
        $slug = $formData['slug'];
        $content = $formData['content'];
        $date = $formData['date'];
        $tags = explode(',', $formData['tags']);
        $categories = explode(',', $formData['categories']);
        $draft = isset($formData['draft']); 
        $layout = $formData['layout'];
        $author = $formData['author'];
        $excerpt = $formData['excerpt'];

        $frontmatter = new Frontmatter($title, $date, $slug, $tags, $categories, $draft, $layout, $author, $excerpt);

        $slug = $this->managePageService->save($frontmatter, $content);

        // if id (current slug) is set, we are editing an existing page so we need to delete the old file
        if ($id && $id !== $slug) {
            $this->managePageService->delete($id);
        }

        return $this->index(['message' => 'Post created!', 'url' => "/$slug"]);
    }

    public function edit($slug, $args = []) {
        $page = $this->managePageService->getPage($slug);
        $args['page'] = $page;
        $args['id'] = $slug;

        return $this->index($args);
    }

}