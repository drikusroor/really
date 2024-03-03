<?php

namespace Ainab\Really\Controller\Admin;

use Ainab\Really\Controller\BaseController;
use Ainab\Really\Model\PageCollection;
use Ainab\Really\Model\ContentInput;
use Ainab\Really\Service\ManageContentService;

class AdminContentController extends BaseController
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
        $prevFilepath = $formData['prevFilepath'] ?? null;
        $filepath = $formData['filepath'] ?? null;
        $contentInput = ContentInput::fromArray($formData);
        $slug = $this->manageContentService->save($contentInput);

        if ($prevFilepath && $prevFilepath !== $filepath) {
            $this->manageContentService->delete($prevFilepath);
        }

        return $this->index(['message' => 'Post created!', 'url' => "/$slug"]);
    }

    public function edit($args = [])
    {
        $fullPath = $_GET['fullPath'] ?? null;

        if (!$fullPath) {
            return $this->index(['error' => 'No full path provided']);
        }

        $page = $this->manageContentService->getContentItem($fullPath);
        $args['page'] = $page;
        $args['filepath'] = $fullPath;

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
