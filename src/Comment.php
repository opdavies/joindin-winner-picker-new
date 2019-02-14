<?php

namespace App;

class Comment
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $userDisplayName;

    /**
     * Set the comment text.
     *
     * @param string $text
     */
    public function setText(string $text)
    {
        $this->text = $text;
    }

    /**
     * Set the user's display name.
     *
     * @param string $name
     */
    public function setUserDisplayName(string $name)
    {
        $this->userDisplayName = $name;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return mixed
     */
    public function getUserDisplayName(): string
    {
        return $this->userDisplayName;
    }
}
