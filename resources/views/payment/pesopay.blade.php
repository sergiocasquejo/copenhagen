@extends('app')

@section('content')
<div class="main" ng-controller="paymentStatusCtrl">
    <section class="section-payment-detail section">
        <div class="container">
            <div class="jumbotron text-center">
                <div class="processing-payment">
                    <h1>Processing...</h1>
                    <p>Please wait while your we process your payment.</p>
                    <form name="payForm" ng-model="payForm" id="payForm" method="post" action="{{ $data['url'] }}">
                        <input type="hidden" name="merchantId" value="{{ $data['merchantId'] }}">
                        <input type="hidden" name="amount" value="{{ $data['amount'] }}">
                        <input type="hidden" name="orderRef" value="{{ $data['orderRef'] }}">
                        <input type="hidden" name="currCode" value="{{ $data['currCode'] }}">
                        <input type="hidden" name="pMethod" value="{{ $data['payMethod'] }}">
                        <input type="hidden" name="payType" value="{{ $data['payType'] }}">
                        <input type="hidden" name="successUrl" value="{{ $data['successUrl'] }}">
                        <input type="hidden" name="failUrl" value="{{ $data['failUrl'] }}">
                        <input type="hidden" name="errorUrl" value="{{ $data['failUrl'] }}">
                        <input type="hidden" name="lang" value="{{ $data['lang'] }}">
                        <input type="hidden" name="secureHash" value="{{ $data['secureHash'] }}">
                        <input type="submit" id="payButton" class="btn btn-success" value="Pay Now">
                    </form>
                    <script>
                        document.getElementById("payButton").click();
                    </script>
                    
                </div>
            </div>
        </div>
    </section>
</div>


@endsection