<script src="https://js.openpay.mx/openpay.v1.min.js"></script>
<script src="https://js.openpay.mx/openpay-data.v1.min.js"></script>

<form id="payment-form">
    @csrf

    <input type="hidden" name="token_id" id="token_id">
    <input type="hidden" name="device_session_id" id="device_session_id">

    <input type="text" data-openpay-card="holder_name" placeholder="Nombre del titular">
    <input type="text" data-openpay-card="card_number" placeholder="Número de tarjeta">
    <input type="text" data-openpay-card="expiration_month" placeholder="MM">
    <input type="text" data-openpay-card="expiration_year" placeholder="YY">
    <input type="text" data-openpay-card="cvv2" placeholder="CVV">

    <button type="submit">Pagar ${{ $deuda->monto }}</button>
</form>

<script>
    OpenPay.setId("{{ config('openpay.id') }}");
    OpenPay.setApiKey("{{ config('openpay.public_key') }}");
    OpenPay.setSandboxMode({{ config('openpay.production') ? 'false' : 'true' }});

    var deviceSessionId = OpenPay.deviceData.setup("payment-form", "device_session_id");

    document.getElementById('payment-form').addEventListener('submit', function (e) {
        e.preventDefault();

        OpenPay.token.extractFormAndCreate(
            'payment-form',
            successCallback,
            errorCallback
        );
    });

    function successCallback(response) {
        document.getElementById('token_id').value = response.data.id;

        fetch("{{ url('/deudas/' . $deuda->id . '/pagar') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                token_id: response.data.id,
                device_session_id: document.getElementById('device_session_id').value
            })
        }).then(res => res.json())
            .then(data => alert(data.message));
    }

    function errorCallback(error) {
        alert(error.data.description);
    }
</script>