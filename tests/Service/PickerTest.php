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

        $hosts = (new Picker())
            ->setHosts(collect($data))
            ->getHosts();

        $this->assertInstanceOf(Collection::class, $hosts);
        $this->assertCount(5, $hosts);
    }

    /** @test */
    public function comments_for_multiple_events_are_flattened_and_combined()
    {
        $comment1 = new \stdClass();
        $comment1->comment = 'Great talk!';
        $comment1->user_display_name = 'Dan Ackroyd';

        $comment2 = new \stdClass();
        $comment2->comment = 'Could be better.';
        $comment2->user_display_name = 'Lucia Velasco';

        $comment3 = new \stdClass();
        $comment3->comment = 'Needs more cat pictures.';
        $comment3->user_display_name = 'Rupert Jabelman';

        $data = [
            [$comment1, $comment2],
            [$comment3],
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
        $event = [
            'hosts' => [
                ['host_name' => 'Oliver Davies'],
            ],
        ];

        $comment1 = new \stdClass();
        $comment1->comment = 'Great talk!';
        $comment1->user_display_name = 'Peter Fisher';

        $comment2 = new \stdClass();
        $comment2->comment = 'Text on slides could be bigger.';
        $comment2->user_display_name = 'Oliver Davies';

        $comment3 = new \stdClass();
        $comment3->comment = 'Speak slower.';
        $comment3->user_display_name = 'Zan Baldwin';

        $comments = [
            [$comment1, $comment2, $comment3],
        ];

        $comments = (new Picker())
            ->setHosts(collect([$event]))
            ->setComments(collect($comments))
            ->getComments();

        $this->assertCount(2, $comments);
        $this->assertSame('Peter Fisher', $comments[0]->getUserDisplayName());
        $this->assertSame('Zan Baldwin', $comments[1]->getUserDisplayName());
    }

    /** @test */
    public function winners_can_be_selected()
    {
        $comment1 = new \stdClass();
        $comment1->comment = 'Great talk!';
        $comment1->user_display_name = 'Peter Fisher';

        $comment2 = new \stdClass();
        $comment2->comment = 'Text on slides could be bigger.';
        $comment2->user_display_name = 'Michael Bush';

        $comment3 = new \stdClass();
        $comment3->comment = 'Speak slower.';
        $comment3->user_display_name = 'Zan Baldwin';

        $comments = [
            [$comment1, $comment2, $comment3],
        ];

        $picker = new Picker();
        $picker->setComments(collect($comments));
        $this->assertCount(3, $picker->getComments());

        tap($picker->getWinners(1), function (Collection $winners) use ($picker) {
            $this->assertCount(1, $winners);
            $this->assertTrue($picker->getComments()->contains($winners->first()));
        });

        tap($picker->getWinners(2), function (Collection $winners) use ($picker) {
            $this->assertCount(2, $winners);
            $this->assertTrue($picker->getComments()->contains($winners->first()));
            $this->assertTrue($picker->getComments()->contains($winners->last()));
        });
    }
}
