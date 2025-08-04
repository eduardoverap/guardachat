<?php

namespace App\Models;

use App\Models\Base;
use App\Models\Traits\MessageProcessing;
use PDO;
use PDOException;

class Chat extends Base
{
  use MessageProcessing;

  public function __construct()
  {
    parent::__construct();
  }

  public function getChatList(): ?array
  {
    try {
      $stmt = $this->conn->query('
        SELECT conversationId, originalTitle, updateTime
        FROM conversations
        ORDER BY updateTime DESC;
      ');
      $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $list;
    } catch (PDOException $e) {
      logErrorWithTimestamp($e, __FILE__);
      return null;
    }
  }

  public function getChatContent(string $conversationId): ?string
  {
    try {
      $stmt = $this->conn->prepare('
        SELECT mappingId, parent, children
        FROM mappings
        WHERE conversationId = :cid
      ');
      $stmt->execute(
        ['cid' => $conversationId]
      );
      $rawMappings = $stmt->fetchAll(PDO::FETCH_ASSOC);
      
      $mappings = [];
      foreach ($rawMappings as $item) {
        $children = !is_null($item['children']) ? explode(', ', $item['children']) : null;
        $mappings[$item['mappingId']] = [
          'parent'   => $item['parent'],
          'children' => $children
        ];
      }

      // Prepare an ordered list
      $iterate = function (array $map, ?string $currentId = null) use (&$iterate) {
        static $list = [];

        if ($currentId !== null && !isset($map[$currentId])) return;

        // Go to first node
        if ($list === [] && $currentId === null) {
          foreach ($map as $key => $item) {
            if ($item['parent'] === null || $item['parent'] === 'client-created-root') {
              $currentId = $key;
              break;
            }
          }
        }
        
        // Add current ID
        $list[] = $currentId;
        if (is_array($map[$currentId]['children'])) {
          foreach ($map[$currentId]['children'] as $child) {
            $iterate($map, $child);
          }
        }

        return $list;
      };

      $list = $iterate($mappings);

      $messageFeed = '';

      foreach ($list as $mapid) {
        // Use message Id to get each message from DB
        $stmt = $this->conn->prepare('
          SELECT authorRole, createdTime, contentParts
          FROM messages
          WHERE mappingId = :mapid
          LIMIT 1
        ');
        $stmt->execute(['mapid' => $mapid]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results !== []) {
          $messageFeed .= $this->createMessageBlock($results[0]);
        }
      }

      return $messageFeed;
    } catch (PDOException $e) {
      logErrorWithTimestamp($e, __FILE__);
      return null;
    }
  }

  public function __destruct()
  {
    parent::__destruct();
  }
}
