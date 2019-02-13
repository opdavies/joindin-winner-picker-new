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
     * Picker constructor.
     */
    public function __construct()
    {
        $this->hosts = collect();
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
}
