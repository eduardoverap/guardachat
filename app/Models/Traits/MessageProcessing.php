<?php

namespace App\Models\Traits;

use FastVolt\Helper\Markdown;
use Throwable;

trait MessageProcessing
{
  private array  $roles  = [
    'assistant' => 'ChatGPT',
    'user'      => 'User'
  ];

  public function processMessage(array $message): ?array
  {
    if (isset($message['contentParts'])) {
      try {
        // Consider only messages from the user and ChatGPT
        $currRole = $message['authorRole'] ?? null;
        if (isset($this->roles[$currRole])) {
          $msgCreated = timestampToFormattedDate($message['createdTime']);

          // Message meta: author, created and updated
          $msgMeta = "{$this->roles[$currRole]} @ $msgCreated";

          // Message body: Markdown to HTML
          $msgPart = (string) json_decode($message['contentParts'])[0] ?? '';
          $markdown = new Markdown();
          $markdown->setContent($msgPart);
          $msgBody = $markdown->toHtml();
          unset($markdown);

          return [
            'id'     => $message['messageId'] ?? null,
            'cid'    => $message['conversationId'] ?? null,
            'meta'   => $msgMeta,
            'body'   => $msgBody,
            'author' => $currRole
          ];
        }
      } catch (Throwable $e) {
        logErrorWithTimestamp($e, __FILE__);
        return null;
      }
    }
    return null;
  }

  public function createMessageBlock(array $results, bool $includeGoToBtn = false): string
  {
    $msgMeta = $msgBody = null;
    $userClass = $messageBlock = '';
    $raw = $this->processMessage($results);

    if ($raw !== null) {
      $msgID     = $raw['id'];
      $msgChatID = $raw['cid'] ?? null;
      $msgMeta   = $raw['meta'] ?? null;
      $msgBody   = $raw['body'] ?? null;
      $chatLink  = url('chat') . '?id=' . $msgChatID;
      $userClass = ($raw['author'] === 'assistant') ? ' chat-response' : '';
      $btnGoTo   = $includeGoToBtn ? "<div class=\"go-to-btn\" title=\"View full chat\"><a class=\"chat-link\" href=\"{$chatLink}\">View full chat >></a></div>" : '';
      $messageBlock .= <<<CURR_MESSAGE
      <div id="{$msgID}" class="msg-body{$userClass}">
        <p>
        <small>
        {$msgMeta}
        </small>
        </p>
        <div>{$msgBody}</div>{$btnGoTo}
      </div>
      CURR_MESSAGE;
    }

    return $messageBlock;
  }
}
