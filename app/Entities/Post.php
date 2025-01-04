<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Post extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];

    protected $attributes = [
        'title' => null,
        'content' => null,
        'status' => null,
        'slug' => null,
    ];

    public function setTitle(string $title): self
    {
        $this->attributes['title'] = strtoupper($title);
        return $this;
    }

    public function setContent(string $content): self
    {
        $this->attributes['content'] = $content;
        return $this;
    }

    public function setStatus(string $status): self
    {
        $this->attributes['status'] = $status;
        return $this;
    }

    public function setSlug(string $title): self
    {
        // $this->attributes['slug'] = url_title(strtolower($title)); // not work
        $this->attributes['slug'] = url_title(strtolower($this->attributes['title']));
        return $this;
    }

    // public function getSlug(): self
    // {
    //     $this->attributes['slug'] = url_title(strtolower($this->attributes['title']));
    //     return $this;
    // }
}
