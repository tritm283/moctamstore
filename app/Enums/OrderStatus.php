<?php
namespace App\Enums;
enum OrderStatus: string { case PENDING='pending'; case CONFIRMED='confirmed'; case PROCESSING='processing'; case SHIPPING='shipping'; case COMPLETED='completed'; case CANCELLED='cancelled'; }
