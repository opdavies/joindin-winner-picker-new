<?php

namespace App\Tests\Service;

use App\Comment;
use App\Service\Picker;
use App\Tests\Helpers\Factory\CommentFactory;
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

        $hosts = (new Picker())
            ->setHosts(collect($data))
            ->getHosts();

        $this->assertInstanceOf(Collection::class, $hosts);
        $this->assertCount(5, $hosts);
    }

    /** @test */
    public function comments_for_multiple_events_are_flattened_and_combined()
    {
        $data = [
            (new CommentFactory())->setCount(2)->create(),
            (new CommentFactory())->setCount(1)->create(),
        ];

        $comments = (new Picker())
            ->setComments(collect($data))
            ->getComments();

        $this->assertInstanceOf(Collection::class, $comments);
        $this->assertCount(3, $comments);
    }

    /** @test */
    public function comments_from_event_hosts_cannot_be_picked()
    {
        $comments = (new CommentFactory())->setCount(3)->create();

        $event = [
            'hosts' => [
                ['host_name' => $hostName = $comments[1]->user_display_name],
            ],
        ];

        /** @var \Tightenco\Collect\Support\Collection $userNames */
        $userNames = (new Picker())
            ->setHosts(collect([$event]))
            ->setComments(collect($comments))
            ->getComments()
            ->map->getUserDisplayName();

        $this->assertCount(2, $userNames);
        $this->assertFalse($userNames->contains($hostName));
    }

    /** @test */
    public function winners_can_be_selected()
    {
        $comments = (new CommentFactory())->setCount(3)->create();

        $picker = new Picker();
        $picker->setComments(collect($comments));
        $this->assertCount(3, $picker->getComments());

        tap($picker->getWinners(1), function (Collection $winners) use ($picker) {
            $this->assertCount(1, $winners);
            $this->assertInstanceOf(Comment::class, $winners->first());
            $this->assertTrue($picker->getComments()->contains($winners->first()));
        });

        tap($picker->getWinners(2), function (Collection $winners) use ($picker) {
            $this->assertCount(2, $winners);

            $this->assertInstanceOf(Comment::class, $winners->first());
            $this->assertTrue($picker->getComments()->contains($winners->first()));

            $this->assertInstanceOf(Comment::class, $winners->last());
            $this->assertTrue($picker->getComments()->contains($winners->last()));
        });
    }
}
