<?php

namespace Tests\Feature;

use App\Models\Counter;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Token;
use App\Models\User;
use App\Services\QueueService;
use App\Support\ReviewCode;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CounterOpenStateTest extends TestCase
{
    use RefreshDatabase;

    private Counter $counter;

    private Service $service;

    private Doctor $doctor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->service = Service::where('prefix', 'G')->firstOrFail();
        $this->doctor = Doctor::where('service_id', $this->service->id)->firstOrFail();
        $this->counter = Counter::where('name', 'Counter-1')->firstOrFail();
    }

    private function operator(): User
    {
        return User::where('email', 'operator@queuecare.local')->firstOrFail();
    }

    private int $seq = 0;

    private int $phoneSeq = 0;

    private function createToken(string $status = Token::WAITING): Token
    {
        $this->seq++;
        $this->phoneSeq++;

        return Token::create([
            'token_date' => now()->toDateString(),
            'seq' => $this->seq,
            'token_no' => $this->service->prefix.'-'.(900 + $this->seq),
            'review_code' => ReviewCode::generate(),
            'service_id' => $this->service->id,
            'doctor_id' => $this->doctor->id,
            'counter_id' => $this->counter->id,
            'patient_id' => Patient::create([
                'name' => 'Test Patient '.$this->seq,
                'phone' => '017'.str_pad((string) $this->phoneSeq, 6, '0', STR_PAD_LEFT),
            ])->id,
            'status' => $status,
            'review_status' => 'none',
        ]);
    }

    public function test_counter_can_be_toggled_between_open_and_closed(): void
    {
        $this->assertTrue($this->counter->fresh()->is_open);
        $this->assertTrue($this->counter->fresh()->isOpen());

        $this->counter->markClosed();
        $this->assertFalse($this->counter->fresh()->is_open);
        $this->assertFalse($this->counter->fresh()->isOpen());

        $this->counter->markOpen();
        $this->assertTrue($this->counter->fresh()->is_open);
        $this->assertTrue($this->counter->fresh()->isOpen());
    }

    public function test_closed_counter_blocks_next_call(): void
    {
        $waiting = $this->createToken();
        $this->counter->markClosed();

        $this->actingAs($this->operator())
            ->from(route('queue.index'))
            ->post(route('queue.next'))
            ->assertRedirect(route('queue.index'))
            ->assertSessionHas('error');

        // The patient is left waiting, untouched.
        $this->assertSame(Token::WAITING, $waiting->fresh()->status);
    }

    public function test_an_open_counter_can_still_call_the_next_patient(): void
    {
        $waiting = $this->createToken();

        $this->actingAs($this->operator())
            ->from(route('queue.index'))
            ->post(route('queue.next'))
            ->assertRedirect(route('queue.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame(Token::CALLING, $waiting->fresh()->status);
    }

    public function test_the_operator_toggle_flips_their_own_counter(): void
    {
        $operator = $this->operator();

        $this->actingAs($operator)
            ->from(route('queue.index'))
            ->post(route('queue.counter.toggle'))
            ->assertRedirect(route('queue.index'));

        $this->assertFalse($this->counter->fresh()->is_open);

        $this->actingAs($operator)
            ->from(route('queue.index'))
            ->post(route('queue.counter.toggle'))
            ->assertRedirect(route('queue.index'));

        $this->assertTrue($this->counter->fresh()->is_open);
    }

    public function test_closed_counter_still_allows_completing_current_visit(): void
    {
        $token = $this->createToken(Token::SERVING);
        $token->update(['called_at' => now(), 'started_at' => now()]);

        $this->counter->markClosed();

        $this->actingAs($this->operator())
            ->from(route('queue.index'))
            ->post(route('queue.action', [$token, 'complete']))
            ->assertRedirect(route('queue.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame(Token::COMPLETED, $token->fresh()->status);
    }

    public function test_a_waiting_patient_is_hidden_when_every_counter_for_the_service_is_closed(): void
    {
        $waiting = $this->createToken();

        $this->assertTrue($waiting->fresh()->isVisibleOnQueue());

        // Close every counter serving this service, not just the operator's.
        Counter::where('service_id', $this->service->id)->update(['is_open' => false]);

        $this->assertFalse($waiting->fresh()->isVisibleOnQueue());
    }

    public function test_a_waiting_patient_stays_visible_when_a_sibling_counter_is_open(): void
    {
        $waiting = $this->createToken();

        $this->counter->markClosed();
        $this->assertFalse($waiting->fresh()->isVisibleOnQueue());

        // Visibility is per service: an open sibling on the same service keeps
        // the patient on the TV, so the closed desk does not strand them.
        $sibling = Counter::create([
            'name' => 'Counter-1B',
            'service_id' => $this->service->id,
            'room_no' => 'R-1B',
            'is_active' => true,
            'is_open' => true,
        ]);

        $this->assertTrue($waiting->fresh()->isVisibleOnQueue());

        // ...and closing the sibling too is what finally hides them.
        $sibling->markClosed();
        $this->assertFalse($waiting->fresh()->isVisibleOnQueue());
    }

    public function test_an_inactive_counter_does_not_keep_waiting_patients_visible(): void
    {
        $waiting = $this->createToken();

        Counter::where('service_id', $this->service->id)->update(['is_active' => false]);

        $this->assertFalse($waiting->fresh()->isVisibleOnQueue());
    }

    public function test_hidden_waiting_count_reports_patients_held_back(): void
    {
        $this->createToken();

        // Sibling desks of the same service are still open, so nothing is hidden.
        $this->assertSame(0, app(QueueService::class)->hiddenWaitingCount($this->operator()));

        Counter::where('service_id', $this->service->id)->update(['is_open' => false]);
        $this->counter->update(['is_open' => false]);

        $this->assertSame(1, app(QueueService::class)->hiddenWaitingCount($this->operator()));
    }

    public function test_an_admin_can_close_a_counter_remotely(): void
    {
        $admin = User::where('email', 'admin@queuecare.local')->firstOrFail();

        $this->actingAs($admin)
            ->from(route('admin.counters.index'))
            ->post(route('admin.counters.toggle', $this->counter))
            ->assertRedirect(route('admin.counters.index'));

        $this->assertFalse($this->counter->fresh()->is_open);
    }
}
