<x-workspace-layout>
    <div class="page-heading">
        <p class="eyebrow">BILLING</p>
        <h1>Your CleanFlow subscription</h1>
        <p>Choose monthly flexibility or save 20% with annual billing. Payments are completed on PayPal’s hosted approval pages; CleanFlow never stores card details.</p>
    </div>

    @if (!$configured)
        <div class="notice">PayPal Sandbox has not been configured for this installation. Checkout remains unavailable until an administrator provides Sandbox credentials, both billing-plan IDs, and a verified webhook.</div>
    @endif

    <div class="detail-grid">
        <section class="card">
            <h2>Current status</h2>
            <p class="quote-total">{{ $subscription?->status ? ucfirst($subscription->status) : 'Inactive' }}</p>
            <p>{{ $subscription?->plan?->name ?? 'No plan selected' }}</p>
            @if ($subscription?->provider_subscription_id)
                <p class="fine">PayPal subscription ID: {{ $subscription->provider_subscription_id }}</p>
                @if (in_array($subscription->status, ['active', 'pending', 'past_due']))
                    <form method="POST" action="{{ route('billing.cancel') }}">
                        @csrf
                        <button class="link danger">Cancel subscription</button>
                    </form>
                @endif
            @endif
        </section>

        <section class="card">
            <h2>Choose billing</h2>
            @foreach ($plans as $plan)
                <div class="list-row">
                    <div>
                        <b>{{ $plan->name }}</b>
                        @if ($plan->price)
                            <small>{{ $plan->currency }} {{ number_format($plan->price, 2) }}/{{ $plan->billing_interval }}</small>
                            @if ($plan->annual_discount_percent)
                                <small>Save {{ number_format($plan->annual_discount_percent, 0) }}% compared with monthly billing.</small>
                            @endif
                        @else
                            <small>Contact sales for pricing</small>
                        @endif
                    </div>
                    @if ($configured && ($paypalPlanIds[$plan->id] ?? false))
                        <form method="POST" action="{{ route('billing.checkout', $plan) }}">
                            @csrf
                            <button class="button secondary">Continue to PayPal</button>
                        </form>
                    @else
                        <span class="muted">Not configured</span>
                    @endif
                </div>
            @endforeach
        </section>
    </div>
</x-workspace-layout>
