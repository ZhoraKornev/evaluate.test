<?php


namespace App\DTO;


use App\Entity\User;

class NewSubscriptionRequestDTO
{
    public User $user;
    public string $subscription_id;
}
