<?php


namespace App\Posts;


use JetBrains\PhpStorm\Pure;

class PostHolder
{
    /** @var Post[] */
    private array $posts;

    public function __construct(array $posts)
    {
        foreach ($posts as $data) {
            $this->posts[] = new Post($data);
        }
    }

    #[Pure]
    protected function postsByMonth(?array $posts = null): array
    {
        if ($posts === null) {
            $posts = $this->posts;
        }

        $groupedPosts = [];

        foreach ($posts as $post) {
            $month = $post->month();

            if (!isset($groupedPosts[$month])) {
                $groupedPosts[$month] = [];
            }

            $groupedPosts[$month][] = $post;
        }

        return $groupedPosts;
    }

    public function postsByUser(): array
    {
        $groupedPosts = [];

        foreach ($this->posts as $post) {
            $fromId = $post->from_id;

            if (!isset($groupedPosts[$fromId])) {
                $groupedPosts[$fromId] = [];
            }

            $groupedPosts[$fromId][] = $post;
        }

        return $groupedPosts;
    }

    #[Pure]
    public function averageMessageLenPerMonth(): array
    {
        $data = [];
        $groupedPosts = $this->postsByMonth();

        foreach (array_keys($groupedPosts) as $month) {
            $data[$month] = 0;
        }

        foreach ($groupedPosts as $month => $posts) {
            /** @var Post $post */
            foreach ($posts as $post) {
                $data[$month] += $post->messageLen();
            }

            $data[$month] = to_decimal($data[$month] / count($posts));
        }

        return $data;
    }

    #[Pure]
    public function longestForMonth(): array
    {
        $data = [];
        $groupedPosts = $this->postsByMonth();

        foreach (array_keys($groupedPosts) as $month) {
            $data[$month] = 0;
        }

        foreach ($groupedPosts as $month => $posts) {
            /** @var Post $post */
            foreach ($posts as $post) {
                $data[$month] = max($post->messageLen(), $data[$month]);
            }
        }

        return $data;
    }

    public function postsPerWeek(): array
    {
        $data = [];
        foreach ($this->posts as $post) {
            $data[] = $post->weekNumber();
        }

        $data = array_count_values($data);
        ksort($data);

        return $data;
    }

    public function userAveragePerMonth(): array
    {
        $data = [];
        $groupedPosts = $this->postsByUser();

        foreach ($groupedPosts as $fromId => $posts) {
            $data[$fromId] = to_decimal(count($posts) / count($this->postsByMonth($posts)));
        }

        uksort($data, static function ($k1, $k2) {
            $id1 = (int)filter_var($k1, FILTER_SANITIZE_NUMBER_INT);
            $id2 = (int)filter_var($k2, FILTER_SANITIZE_NUMBER_INT);

            return $id1 > $id2 ? 1 : -1;
        });

        return $data;
    }
}
