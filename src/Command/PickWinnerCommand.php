<?php

namespace App\Command;

use App\Comment;
use App\Service\Picker;
use GuzzleHttp\Client;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tightenco\Collect\Support\Collection;

class PickWinnerCommand extends Command
{
    protected static $defaultName = 'app:pick-winner';

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @var \Symfony\Contracts\Cache\CacheInterface
     */
    private $cache;

    /**
     * @var \App\Service\Picker
     */
    private $picker;

    /**
     * PickWinnerCommand constructor.
     *
     * @param \App\Service\Picker $picker
     *   The Picker service.
     */
    public function __construct(Picker $picker)
    {
        parent::__construct();
        $this->client = new Client();
        $this->cache = new FilesystemCache();
        $this->picker = $picker;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('tag', InputArgument::REQUIRED, 'The joind.in tag')
            ->addArgument('start', InputArgument::OPTIONAL, '', 'first day of last month')
            ->addArgument('end', InputArgument::OPTIONAL, '', 'last day of this month')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Joind.in Winner Picker!');

        $tag = $input->getArgument('tag');
        $startDate = (new \DateTime($input->getArgument('start')))->format('Y-m-d');
        $endDate = (new \DateTime($input->getArgument('end')))->format('Y-m-d');

        $io->comment(vsprintf('Selecting from #%s events between %s and %s.', [
            $tag,
            $startDate,
            $endDate,
        ]));

        $events = collect($this->getEventData($tag, $startDate, $endDate));
        $this->picker->setHosts($events);
        $this->picker->setComments($this->allComments($events));

        $this->picker->getWinners(1)->each(function (Comment $comment) use ($io) {
            $io->section(vsprintf('%s (%s)', [
                $comment->getUserDisplayName(),
                $comment->getTalkTitle(),
            ]));
            $io->text($comment->getText());
            $io->text($comment->getUri());
        });
    }

    /**
     * Get all comments (talks and event) for the events.
     *
     * @param \Tightenco\Collect\Support\Collection $events
     *   The retrieved events.
     *
     * @return \Tightenco\Collect\Support\Collection
     *   The merged comments.
     */
    private function allComments(Collection $events): Collection
    {
        return $events->map(function (\stdClass $event) {
            return $this
                ->eventComments($event)
                ->merge($this->talkComments($event))
            ;
        });
    }

    /**
     * Get the event comments.
     *
     * @param \stdClass $event
     *   The event.
     *
     * @return \Tightenco\Collect\Support\Collection
     */
    private function eventComments(\stdClass $event): Collection
    {
        // TODO: Cache this.
        $response = $this->client->get(
            $event->comments_uri,
            ['query' => ['resultsperpage' => 1000]]
        )->getBody();

        return collect(json_decode($response)->comments);
    }

    /**
     * Get the talk comments.
     *
     * @param \stdClass $event
     *   The event.
     *
     * @return \Tightenco\Collect\Support\Collection
     */
    private function talkComments(\stdClass $event): Collection
    {
        // TODO: Cache this.
        $response = $this->client->get(
            $event->all_talk_comments_uri,
            ['query' => ['resultsperpage' => 1000]]
        )->getBody();

        return collect(json_decode($response)->comments);
    }

    /**
     * Get the event data.
     *
     * @param string $tag
     *   The tag to search for.
     * @param string $startDate
     *   The start date limit.
     * @param string $endDate
     *   The end date limit.
     *
     * @return array
     *
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    private function getEventData(string $tag, string $startDate, string $endDate): array
    {
        $cacheKey = md5(collect(['events:', $tag, $startDate, $endDate])->implode('_'));

        if (!$events = $this->cache->get($cacheKey)) {
            $response = $this->client->get('http://api.joind.in/v2.1/events', [
                'query' => [
                    'tags' => [$tag],
                    'startdate' => $startDate,
                    'enddate' => $endDate,
                    'verbose' => 'yes',
                ],
            ]);

            $events = json_decode($response->getBody())->events;
            $this->cache->set($cacheKey, $events, 3600);
        }

        return $events;
    }
}
