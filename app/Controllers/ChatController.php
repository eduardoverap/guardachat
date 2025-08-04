<?php

namespace App\Controllers;

use App\Models\Chat;
use Throwable;

class ChatController extends BaseController
{
  // Get chat content from the model
  private function getChat($conversationId): ?string
  {
    $model = new Chat();
    $content = $model->getChatContent($conversationId);
    return $content;
  }

  public function index(): void
  {
    if (isset($_GET['id']) && preg_match(CID_REGEX, $_GET['id'])) {
      $content = $this->getChat($_GET['id']);
      if (is_null($content)) {
        http_response_code(403);
        goHome();
      } else {
        $data = ['chatContent' => $content];
        $this->render('chat', $data);
      }
    } else {
      http_response_code(404);
      goHome();
    }
  }
}
