<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use Closure;
use Livewire\Component;

class CustomerContextSidebar extends Component
{
    public int $customerId;

    public function mount(Customer|Closure $record): void
    {
        if ($record instanceof Closure) {
            $record = $record();
        }

        $this->customerId = $record->id;
    }

    public function getCustomer(): Customer
    {
        return Customer::with(['customerChannelIdentifiers', 'conversations' => function ($query) {
            $query->latest('last_message_at')->limit(5);
        }])->findOrFail($this->customerId);
    }

    public function render()
    {
        $customer = $this->getCustomer();

        return view('livewire.customer.customer-context-sidebar', [
            'customer' => $customer,
            'channelIdentifiers' => $customer->customerChannelIdentifiers,
            'recentConversations' => $customer->conversations,
            'totalConversations' => $customer->conversations()->count(),
            'openConversations' => $customer->conversations()->where('status', 'open')->count(),
        ]);
    }
}
