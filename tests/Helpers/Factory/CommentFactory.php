<?php

namespace App\Tests\Helpers\Factory;

use App\Comment;
use Faker\Factory;
use Tightenco\Collect\Support\Collection;

class CommentFactory
{
    /**
     * @var \Faker\Factory
     */
    private $faker;

    /**
     * The number of comments to create.
     *
     * @var int
     */
    private $commentCount = 0;

    /**
     * CommentFactory constructor.
     */
    public function __construct()
    {
        $this->faker = Factory::create();
    }

    /**
     * Create new comments.
     *
     * @return \Tightenco\Collect\Support\Collection
     */
    public function create(): Collection
    {
        $talkTitle = $this->faker->sentence;

        return tap(collect(), function (Collection $data) use ($talkTitle) {
            if ($this->commentCount > 0) {
                foreach (range(1, $this->commentCount) as $i) {
                    $comment = new \stdClass();
                    $comment->talk_title = $talkTitle;
                    $comment->comment = $this->faker->paragraph;
                    $comment->uri = 'http://api.joind.in/v2.1/talk_comments/' . $this->faker->randomNumber(8);
                    $comment->user_display_name = $this->faker->name;

                    $data->push($comment);
                }
            }
        });
    }

    /**
     * Set the number of comments to create.
     *
     * @param int $count
     *
     * @return \App\Tests\Helpers\Factory\CommentFactory
     */
    public function setCount(int $count): self
    {
        $this->commentCount = $count;

        return $this;
    }
}
