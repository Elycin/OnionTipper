<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TorRelay extends Model
{
    protected $fillable = ["fingerprint", "nickname", "first_seen", "email", "donation_address", "consensus_weight"];

    /**
     * Get the consensus weight of the relay.
     *
     * @return mixed
     */
    public function getConsensusWeight()
    {
        return $this->consensus_weight;
    }

    /**
     * Get the date of when tor metrics first saw the relay.
     *
     * @return Carbon
     */
    public function getFirstSeenDate(): Carbon
    {
        return Carbon::parse($this->first_seen);
    }

    /**
     * Get the sum of all consensus weights in the database for nodes that have a donation address.
     *
     * @return mixed
     */
    public static function getConsensusWeightSum()
    {
        return self::where('donation_address', '!=', null)->sum('consensus_weight');
    }

    /**
     * Determine the decimal of what share of the donation the relay operator should get.
     *
     * @return float|int
     */
    public function getSharePercentageAsDecimal()
    {
        return ($this->getConsensusWeight() / self::getConsensusWeightSum());
    }

    /**
     * Determine the percentage of what share of the donation the relay operator should get.
     *
     * @return float|int
     */
    public function getSharePercentageAsPercent()
    {
        return $this->getSharePercentageAsDecimal() * 100;
    }

    /**
     * Get the email of the tor relay.
     *
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get the donation address of the tor relay.
     *
     * @return mixed
     */
    public function getDonationAddress()
    {
        return $this->donation_address;
    }
}
