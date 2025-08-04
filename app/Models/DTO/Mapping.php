<?php

namespace App\Models\DTO;

class Mapping
{
  public function __construct(
    public string  $mappingId,
    public string  $conversationId,
    public ?string $parent   = null,
    public ?array  $children = null
  ) {}

  public function getChildrenAsString(): ?string
  {
    return implode(', ', $this->children) ?? null;
  }
}
