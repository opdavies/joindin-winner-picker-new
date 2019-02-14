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
     * The URI for the comment.
     *
     * @var string
     */
    private $uri;

    /**
     * The title of the talk that was commented on.
     *
     * @var string
     */
    private $talkTitle;

    /**
     * Set the comment text.
     *
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * Set the user's display name.
     *
     * @param string $name
     */
    public function setUserDisplayName(string $name): void
    {
        $this->userDisplayName = $name;
    }

    /**
     * Set the URI for the comment.
     *
     * @param string $uri
     */
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * Set the talk title.
     *
     * @param string $talkTitle
     */
    public function setTalkTitle(string $talkTitle): void
    {
        $this->talkTitle = $talkTitle;
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

    /**
     * Get the URI for the comment.
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Get the talk title.
     *
     * @return string
     */
    public function getTalkTitle(): string
    {
        return $this->talkTitle;
    }
}
