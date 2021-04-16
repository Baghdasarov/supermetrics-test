<?php


namespace App\Posts;


use Carbon\Carbon;

class Post
{
    public string $from_id;
    private string $message;
    public Carbon $created_time;

    public function __construct(array $data)
    {
        $this->from_id = $data['from_id'];
        $this->message = $data['message'];
        $this->created_time = Carbon::parse($data['created_time']);
    }

    public function weekNumber(): int
    {
        return $this->created_time->weekNumberInMonth;
    }

    public function month(): string
    {
        return $this->created_time->monthName;
    }

    public function messageLen(): int
    {
        return mb_strlen($this->message);
    }
}
