<?php

namespace Ainab\Really\Controller\Admin;

use Ainab\Really\Controller\BaseController;
use Ainab\Really\Model\PageCollection;
use Ainab\Really\Model\PostInput;
use Ainab\Really\Service\ManagePageService;

class AdminPageController extends BaseController
{
    public function __construct(private ManagePageService $managePageService)
    {
        parent::__construct();
    }

    public function index($args = [])
    {
        $pages = $this->managePageService->getPagesList();
        $args['pages'] = (new PageCollection($pages))->toArray();

        echo $this->twig->render('admin/pages/index.html.twig', $args);
    }

    public function save()
    {
        $formData = $_POST;
        $id = $formData['id'] ?? null;
        $postInput = PostInput::fromArray($formData);
        $slug = $this->managePageService->save($postInput);

        if ($id && $id !== $slug) {
            $this->managePageService->delete($id);
        }

        return $this->index(['message' => 'Post created!', 'url' => "/$slug"]);
    }

    public function edit($slug, $args = [])
    {
        $page = $this->managePageService->getPage($slug);
        $args['page'] = $page;
        $args['id'] = $slug;

        return $this->index($args);
    }

    public function delete($slug)
    {
        $this->managePageService->delete($slug);
        return $this->index(['message' => 'Post deleted!']);
    }

    public function rebuild()
    {
        try {
            $this->managePageService->rebuild();
        } catch (\Exception $e) {
            return $this->index(['error' => 'Error rebuilding index: ' . $e->getMessage()]);
        }
        return $this->index(['message' => 'Index rebuilt!']);
    }

    public function preview()
    {
        $formData = $_POST;
        $id = $formData['id'] ?? null;
        $postInput = PostInput::fromArray($formData);

        $html = $this->managePageService->preview($postInput);

        echo $html;
    }
}
