<?php

namespace App\Controllers;

use App\Models\Chat;

class HomeController extends BaseController
{
  private function renderChatList(): string
  {
    $chatList = $listItems = '';
    $model = new Chat;
    $rawList = $model->getChatList();
    if ($rawList !== null) {
      foreach ($rawList as $item) {
        $updateTime = timestampToFormattedDate($item['updateTime'], 'Y-m-d h:i A');
        $chatURL    = url('chat') . '?id=' . $item['conversationId'];
        $listItems .= <<<LIST_ITEM
        <li>
          <a class="chat-item" href="{$chatURL}" title="{$item['originalTitle']}">{$item['originalTitle']}</a>
          <small>(Last updated: {$updateTime})</small>
        </li>
        LIST_ITEM;
      }
      
      $chatList = <<<CHAT_LIST
      <ul>
        $listItems
      </ul>
      CHAT_LIST;
    } else {
      $chatList = 'An error has occurred.';
    }
    return $chatList;
  }

  public function index(): void
  {
    if ($this->databaseExists()) {
      $data = ['chatList' => $this->renderChatList()];
      $this->render('home', $data);
    } else {
      $url = BASE_URL . 'import';
      header("Location: {$url}");
      die();
    }
  }
}
