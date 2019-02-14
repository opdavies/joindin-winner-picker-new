<?php

namespace App\Service;

use App\Comment;
use Tightenco\Collect\Support\Collection;

class Picker
{
    /**
     * The combined hosts for the retrieved events.
     *
     * @var \Tightenco\Collect\Support\Collection
     */
    private $hosts;

    /**
     * @var \Tightenco\Collect\Support\Collection
     */
    private $comments;

    /**
     * Picker constructor.
     */
    public function __construct()
    {
        $this->comments = collect();
        $this->hosts = collect();
    }

    /**
     * Retrieve the combined comments for all events.
     *
     * @return \Tightenco\Collect\Support\Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * Retrieve the event hosts.
     *
     * @return \Tightenco\Collect\Support\Collection
     */
    public function getHosts(): Collection
    {
        return $this->hosts;
    }

    /**
     * Set the hosts for the retrieved events.
     *
     * @param \Tightenco\Collect\Support\Collection $data
     *   The event data.
     *
     * @return self
     */
    public function setHosts(Collection $data): self
    {
        $this->hosts = $data->pluck('hosts.*.host_name')
            ->flatten(1)
            ->unique()
            ->sort();

        return $this;
    }

    /**
     * Set the comments for the events.
     *
     * @param \Tightenco\Collect\Support\Collection $comments
     *   A collection of comments.
     *
     * @return $this
     */
    public function setComments(Collection $comments): self
    {
        $this->comments = $comments
            ->flatten(1)
            ->filter(function (\stdClass $comment) {
                return !$this->isUserAnEventHost($comment->user_display_name);
            })
            ->map(function (\stdClass $original) {
                return tap(new Comment(), function (Comment $comment) use ($original) {
                    $comment->setText($original->comment);
                    $comment->setUserDisplayName($original->user_display_name);
                    $comment->setUri($original->uri);
                    $comment->setTalkTitle($original->talk_title);
                });
            })
            ->values();

        return $this;
    }

    /**
     * Determine whether a commenter is an event host.
     *
     * @param string $user_display_name
     *   The user's display name.
     *
     * @return bool
     */
    private function isUserAnEventHost(string $user_display_name): bool
    {
        return $this->hosts->contains($user_display_name);
    }

    /**
     * Select and return the winners.
     *
     * @param int $count
     *   The number of winners.
     *
     * @return \Tightenco\Collect\Support\Collection
     */
    public function getWinners(int $count): Collection
    {
        return $this->getComments()->random($count);
    }
}
