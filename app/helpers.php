<?php

// Count total nights
function countTotalNights($checkIn, $checkOut) {

	return round(strtotime($checkOut) - strtotime($checkIn) / 86400);

}