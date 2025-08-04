<?php

namespace App\Controllers;

use App\Models\DTO\Mapping;
use App\Models\DTO\Message;
use Throwable;

class IterateController
{
  static public function iterateTree(
    string    $id,
    array     $map,
    ?string   $conversationId  = null,
    ?callable $mappingFunction = null,
    ?callable $messageFunction = null
  ): void {
    // Return if ID isn't available
    if (!isset($map[$id])) return;

    // Assign node
    $node = $map[$id];

    // Create mapping DTO
    $mapping = new Mapping(
      mappingId: $node->id,
      conversationId: $conversationId
    );
    $mapping->parent   = $node->parent   ?? null;
    $mapping->children = $node->children ?? null;

    // Create message DTO
    $rawMessage = $node->message;
    $message    = null;
    if ($rawMessage !== null) {
      $message = new Message(
        messageId: $rawMessage->id,
        mappingId: $node->id,
        content: $rawMessage->content->parts
      );
      $message->authorRole  = $rawMessage->author->role ?? null;
      $message->authorName  = $rawMessage->author->name ?? null;
      $message->createTime  = $rawMessage->create_time ? (int) floor($rawMessage->create_time) : null;
      $message->updateTime  = $rawMessage->update_time ? (int) floor($rawMessage->update_time) : null;
      $message->contentType = $rawMessage->content->type ?? null;
    }

    // Execute functions
    try {
      $mappingFunction($mapping);
      if ($message !== null) $messageFunction($message);
    } catch (Throwable $e) {
      logErrorWithTimestamp($e, __DIR__);
    }

    // If there is a next node, execute recursively
    if (is_array($node->children)) {
      foreach ($node->children as $childrenId) {
        self::iterateTree($childrenId, $map, $conversationId, $mappingFunction, $messageFunction);
      }
    }
  }
}
