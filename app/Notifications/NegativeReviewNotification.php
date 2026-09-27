<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NegativeReviewNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Review $review,
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('admin.reviews.show', $this->review);

        return (new MailMessage)
            ->subject('Unhappy feedback: '.$this->review->rating_label.' '.$this->review->rating_emoji.' on token '.$this->review->token?->token_no)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('A patient rated their visit as "'.$this->review->rating_label.'" and it needs your attention.')
            ->line('**Token:** '.($this->review->token?->token_no ?? '—'))
            ->line('**Rating:** '.$this->review->rating_emoji.' '.$this->review->rating_label)
            ->line('**Category:** '.($this->review->category ? ucwords(str_replace('_', ' ', $this->review->category)) : 'Not specified'))
            ->line('**Comment:** '.($this->review->comment ?? 'No comment provided'))
            ->line('**Submitted by:** '.$this->review->author_name)
            ->action('View Review', $url)
            ->line('Please review this feedback and take appropriate action.');
    }

    public function toArray($notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'token_no' => $this->review->token?->token_no,
            'rating' => $this->review->rating,
            'rating_label' => $this->review->rating_label,
            'category' => $this->review->category,
            'comment' => $this->review->comment,
            'display_name' => $this->review->author_name,
            'message' => "Unhappy feedback on token {$this->review->token?->token_no}: {$this->review->rating_label}",
        ];
    }
}
