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
            ->subject('Negative Review Alert: '.$this->review->rating.'/5 stars')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('A negative review has been submitted and requires your attention.')
            ->line('**Token:** '.$this->review->token->token_no)
            ->line('**Rating:** '.$this->review->rating.'/5 stars')
            ->line('**Category:** '.($this->review->category ? ucfirst(str_replace('_', ' ', $this->review->category)) : 'Not specified'))
            ->line('**Comment:** '.($this->review->comment ?? 'No comment provided'))
            ->line('**Submitted by:** '.($this->review->is_anonymous ? 'Anonymous' : ($this->review->display_name ?? 'Anonymous')))
            ->action('View Review', $url)
            ->line('Please review and take appropriate action.');
    }

    public function toArray($notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'token_no' => $this->review->token->token_no,
            'rating' => $this->review->rating,
            'category' => $this->review->category,
            'comment' => $this->review->comment,
            'display_name' => $this->review->is_anonymous ? 'Anonymous' : ($this->review->display_name ?? 'Anonymous'),
            'message' => "Negative review received for token {$this->review->token->token_no} ({$this->review->rating}/5 stars)",
        ];
    }
}
