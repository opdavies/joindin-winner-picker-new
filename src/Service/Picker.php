<?php

namespace App\Service;

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
     */
    public function setComments(Collection $comments)
    {
        $this->comments = $comments->flatten(1)->values();
    }
}
