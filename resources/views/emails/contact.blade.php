@extends('emails.layout')
@section('content')
<h2 style="Margin-top: 0;Margin-bottom: 0;font-style: normal;font-weight: normal;color: #44a8c7;font-size: 20px;line-height: 28px;text-align: left;">
          <font color="#60666d"><center>New Contact</center></font></h2>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14">First name: {{ $firstname }}</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14">Last Name: {{ $lastname }}</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14">Email:  {{ $email }}</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14">Phone:  {{ $phone }}</p>
<p class="size-14" style="Margin-top: 10px;Margin-bottom: 0;font-size: 14px;line-height: 14px;text-align: left;" lang="x-size-14">Message:  {{ $message }}</p>
@endsection