<?php

namespace App\Models\DTO;

class Message
{
  public function __construct(
    public string  $messageId,
    public string  $mappingId,
    public ?array  $content,
    public ?string $authorRole  = null,
    public ?string $authorName  = null,
    public ?int    $createTime  = null,
    public ?int    $updateTime  = null,
    public ?string $contentType = null
  ) {}

  public function getContentAsString(): ?string
  {
    return json_encode($this->content) ?? null;
  }
}
