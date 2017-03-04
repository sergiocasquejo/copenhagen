@extends('emails.layout')
@section('content')
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14">Hi {{ $name }},</p>
<h2 style="Margin-top: 30px;Margin-bottom: 0;font-style: normal;font-weight: normal;color: #44a8c7;font-size: 20px;line-height: 28px;text-align: left;">
          <font color="#0492bd"><center>{{ $pageHeading }}</center></font></h2>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><center>{{ $message }}</center></p>
<h2 style="Margin-top: 30px;Margin-bottom: 0;font-style: normal;font-weight: normal;color: #44a8c7;font-size: 20px;line-height: 28px;text-align: left;">
          <font color="#60666d">New Booking</font></h2>
<h3 style="Margin-top: 20px;Margin-bottom: 0;font-style: normal;font-weight: normal;color: #44a8c7;font-size: 18px;line-height: 20px;text-align: left;">
          <font color="#60666d">Details</font></h3>          
          <p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Reference Id</span> : {{ $booking->refId }}</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Building</span> : Main</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Check In</span> : {{ date('l F d Y', strtotime($booking->checkIn))  }} {{ $booking->checkInTime }}</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Check Out</span> : {{ date('l F d Y', strtotime($booking->checkOut))  }} {{ $booking->checkOutTime }}</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Guest</span> : {{ $booking->noOfAdults }} Adult(s), {{ (int)$booking->noOfChild }} Child</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Rate:</span> :  {{ $booking->nf($booking->roomRate, true) }}</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Nights</span> :  x {{ $booking->noOfNights }}</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Rooms</span> : x {{ $booking->noOfRooms }}</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Total</span> : {{ $booking->nf($booking->totalAmount, true) }}</b></p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Booking Status</span> : {{ $booking->status }}</b></p>
@if ($booking->lastPayment)
<h3 style="Margin-top: 20px;Margin-bottom: 0;font-style: normal;font-weight: normal;color: #44a8c7;font-size: 20px;line-height: 28px;text-align: left;">
          <font color="#60666d">Payment Details</font></h3>  
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Payment Method</span> : {{ $booking->lastPayment->method }}</b></p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Amount Paid</span> : {{ $booking->nf($booking->lastPayment->totalAmount, true) }}</b></p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Payment Status</span> : {{ $booking->lastPayment->status }}</b></p>
@endif
<h3 style="Margin-top: 20px;Margin-bottom: 0;font-style: normal;font-weight: normal;color: #44a8c7;font-size: 20px;line-height: 28px;text-align: left;">
          <font color="#60666d">Customer Details</font></h3>          
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Name</span> : {{ $booking->customer->salutation }}. {{ $booking->customer->firstName }} {{ $booking->customer->middleName }} {{ $booking->customer->lastName }}</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Email</span> : {{ $booking->customer->email  }}</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Contact #</span> : {{ $booking->customer->contact  }}</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Address 1</span> : {{ $booking->customer->address1 }}</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Address 2</span> : {{ $booking->customer->address2 }}</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">State</span> : {{ $booking->customer->state }}</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">City</span> : {{ $booking->customer->city }}</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Zip Code</span> : {{ $booking->customer->zipcode }}</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Country</span> : {{ $booking->customer->countryCode }}</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Special Notes</span> : <i>{{ $booking->specialInstructions }}</i></p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14"><span style="display:inline-block;width:120px;">Billing Instruction</span> : <i>{{ $booking->billingInstructions }}<i></p>
<p class="size-14" style="Margin-top: 30px;Margin-bottom: 0;font-size: 10px;line-height: 14px;text-align: left;" lang="x-size-14">Please do not reply to this email as it won't reach us. You have received this email because you have successfully booked with us.  </p>

@endsection