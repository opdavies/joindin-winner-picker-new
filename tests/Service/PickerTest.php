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
        $comments = [
            [
                [
                    'comment' => 'Great talk!',
                    'user_display_name' => 'Dan Ackroyd',
                ],
                [
                    'comment' => 'Could be better.',
                    'user_display_name' => 'Lucia Velasco',
                ],
            ],
            [
                [
                    'comment' => 'Needs more cat pictures.',
                    'user_display_name' => 'Rupert Jabelman',
                ],
            ],
        ];

        $picker = new Picker();
        $picker->setComments(collect($comments));

        $result = $picker->getComments();
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    }

    /** @test */
    public function comments_from_event_hosts_cannot_be_picked()
    {
        $event = [
            'hosts' => [
                ['host_name' => 'Oliver Davies'],
            ],
        ];

        $comments = [
            [
                [
                    'comment' => 'Great talk!',
                    'user_display_name' => 'Peter Fisher',
                ],
                [
                    'comment' => 'Text on slides could be bigger.',
                    'user_display_name' => 'Oliver Davies',
                ],
                [
                    'comment' => 'Speak slower.',
                    'user_display_name' => 'Zan Baldwin',
                ],
            ],
        ];

        $comments = (new Picker())
            ->setHosts(collect([$event]))
            ->setComments(collect($comments))
            ->getComments();

        $this->assertCount(2, $comments);
        $this->assertSame('Peter Fisher', $comments[0]['user_display_name']);
        $this->assertSame('Zan Baldwin', $comments[1]['user_display_name']);
    }

    /** @test */
    public function a_winner_can_be_selected()
    {
        $this->markTestIncomplete();
    }
}
