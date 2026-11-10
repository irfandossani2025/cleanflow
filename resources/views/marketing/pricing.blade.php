<x-app-layout>
    <section class="page-heading">
        <p class="eyebrow">SIMPLE, FOCUSED PRICING</p>
        <h1>Everything you need to turn enquiries into bookings.</h1>
        <p>Start with the same focused CleanFlow workflow on either billing schedule. Save 20% when you choose annual billing.</p>
    </section>

    <div class="pricing-grid">
        @forelse ($plans as $plan)
            <article class="price-card">
                <h2>{{ $plan->name }}</h2>
                <div class="price">
                    @if ($plan->price)
                        {{ $plan->currency }} {{ number_format($plan->price, 2) }}
                        <small>/{{ $plan->billing_interval }}</small>
                    @else
                        <span>Contact us</span>
                    @endif
                </div>

                @if ($plan->annual_discount_percent)
                    <p class="pill">Save {{ number_format($plan->annual_discount_percent, 0) }}% — US$36 each year</p>
                @endif

                <ul>
                    @foreach ($plan->features ?? [] as $feature)
                        <li>{{ $feature }}</li>
                    @endforeach
                    @if ($plan->staff_limit)
                        <li>Up to {{ $plan->staff_limit }} staff members</li>
                    @endif
                </ul>
                <a class="button" href="{{ route('register') }}">Get started</a>
            </article>
        @empty
            <article class="empty">
                <h2>Plans are being configured</h2>
                <p>Please contact sales for current availability and pricing.</p>
                <a class="button" href="{{ route('contact') }}">Contact sales</a>
            </article>
        @endforelse
    </div>

    <p class="fine">Prices are in USD. Any tax that applies to your purchase is shown before payment on PayPal’s hosted approval page.</p>
</x-app-layout>
