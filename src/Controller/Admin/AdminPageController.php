<?php

namespace Ainab\Really\Controller\Admin;

use Ainab\Really\Controller\BaseController;
use Ainab\Really\Model\PageCollection;
use Ainab\Really\Model\ContentInput;
use Ainab\Really\Service\ManageContentService;

class AdminPageController extends BaseController
{
    public function __construct(private ManageContentService $manageContentService)
    {
        parent::__construct();
    }

    public function index($args = [])
    {
        $pages = $this->manageContentService->getContentList();
        $args['pages'] = (new PageCollection($pages))->toArray();

        echo $this->twig->render('admin/pages/index.html.twig', $args);
    }

    public function save()
    {
        $formData = $_POST;
        $id = $formData['id'] ?? null;
        $contentInput = ContentInput::fromArray($formData);
        $slug = $this->manageContentService->save($contentInput);

        if ($id && $id !== $slug) {
            $this->manageContentService->delete($id);
        }

        return $this->index(['message' => 'Post created!', 'url' => "/$slug"]);
    }

    public function edit($slug, $args = [])
    {
        $page = $this->manageContentService->getContentItem($slug);
        $args['page'] = $page;
        $args['id'] = $slug;

        return $this->index($args);
    }

    public function delete($slug)
    {
        $this->manageContentService->delete($slug);
        return $this->index(['message' => 'Post deleted!']);
    }

    public function rebuild()
    {
        try {
            $this->manageContentService->rebuild();
        } catch (\Exception $e) {
            return $this->index(['error' => 'Error rebuilding index: ' . $e->getMessage()]);
        }
        return $this->index(['message' => 'Index rebuilt!']);
    }

    public function preview()
    {
        $formData = $_POST;
        $id = $formData['id'] ?? null;
        $contentInput = ContentInput::fromArray($formData);

        $html = $this->manageContentService->preview($contentInput);

        echo $html;
    }
}
