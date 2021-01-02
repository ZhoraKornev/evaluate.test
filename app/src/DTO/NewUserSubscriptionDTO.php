<?php


namespace App\DTO;


use App\Entity\SubscriptionType;
use App\Entity\User;

class NewUserSubscriptionDTO
{
    public User $user;
    public SubscriptionType $subscription;
}
