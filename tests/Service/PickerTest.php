<?php

namespace App\Tests\Service;

use App\Service\Picker;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;

class PickerTest extends TestCase
{
    /** @test */
    public function hosts_for_multiple_events_are_grouped_and_unique()
    {
        $data = [
            [
                'hosts' => [
                    ['host_name' => 'Lee Stone'],
                    ['host_name' => 'Dave Liddament'],
                    ['host_name' => 'Kat Zien'],
                ],
            ],
            [
                'hosts' => [
                    ['host_name' => 'Oliver Davies'],
                    ['host_name' => 'Lee Stone'],
                    ['host_name' => 'Lucia Velasco'],
                    ['host_name' => 'Dave Liddament'],
                ],
            ],
        ];

        $picker = new Picker();
        $picker->setHosts(collect($data));

        $hosts = $picker->getHosts();
        $this->assertInstanceOf(Collection::class, $hosts);
        $this->assertCount(5, $hosts);
    }

    /** @test */
    public function comments_for_multiple_events_are_flattened_and_combined()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function comments_from_event_hosts_cannot_be_picked()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function a_winner_can_be_selected()
    {
        $this->markTestIncomplete();
    }
}
