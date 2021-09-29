<?php


namespace app\models;


use app\core\Model;

class Auction extends Model
{
    public string $code = "";
    public int $price = 0;
    public int    $wage = 0;
    public int $base_price=0;
    public string $start_at="";
    public string $winner="";
    public int $registration_cost=0;
    public int $product_id =0;
    public int $status_id =0;
    public int $capacity_participants=0;

}