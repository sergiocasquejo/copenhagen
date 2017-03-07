<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    public $paymentStatusPaid = 'paid';
    public $paymentStatusPending = 'pending';
    public $paymentStatusRejected = 'rejected';
}
