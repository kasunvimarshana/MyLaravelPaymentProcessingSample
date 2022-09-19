<!-- https://stripe.com/docs/payments/elements -->
<!-- https://stripe.com/payments/elements -->
<!-- https://stripe.com/docs/js -->
<!-- https://stripe.com/docs/payments/accept-card-payments?platform=web&ui=elements -->
@push('styles')
    <style type="text/css">
        .StripeElement {
            box-sizing: border-box;
            height: 40px;
            padding: 10px 12px;
            border: 1px solid transparent;
            border-radius: 4px;
            background-color: white;
            box-shadow: 0 1px 3px 0 #e6ebf1;
            -webkit-transition: box-shadow 150ms ease;
            transition: box-shadow 150ms ease;
        }
        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;
        }
        .StripeElement--invalid {
            border-color: #fa755a;
        }
        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }
    </style>
@endpush

<label class="mt-3" for="cardElement">
    Card details:
</label>

<div id="cardElement">
    <!-- Elements will create input elements here -->
</div>

<small class="form-text text-muted" id="cardErrors" role="alert">
    <!-- We'll put the error messages in this element -->
</small>

<input type="hidden" name="payment_method" id="paymentMethod">

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // Set your publishable key: remember to change this to your live publishable key in production
        // See your keys here: https://dashboard.stripe.com/apikeys
        var stripe = Stripe('{{ config('services.stripe.key') }}');
        // Set up Stripe.js and Elements to use in checkout form
        var elements = stripe.elements({ locale: 'en' });
        var style = {
            base: {
                color: "#32325d",
            }
        };
        var cardElement = elements.create("card", { style: style });
        cardElement.mount('#cardElement');
    </script>

    <script>
        const form = document.getElementById('paymentForm');
        const payButton = document.getElementById('payButton');
        payButton.addEventListener('click', async(e) => {
            if (form.elements.payment_platform.value === "{{ $paymentPlatform->id }}") {
                e.preventDefault();
                const { paymentMethod, error } = await stripe.createPaymentMethod(
                    'card', cardElement, {
                        billing_details: {
                            "name": "{{ auth()->user()->name }}",
                            "email": "{{ auth()->user()->email }}"
                        }
                    }
                );
                if (error) {
                    const displayError = document.getElementById('cardErrors');
                    displayError.textContent = error.message;
                } else {
                    const tokenInput = document.getElementById('paymentMethod');
                    tokenInput.value = paymentMethod.id;
                    form.submit();
                }
            }
        });
    </script>
@endpush