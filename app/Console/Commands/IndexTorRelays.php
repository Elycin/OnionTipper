<?php

namespace App\Console\Commands;

use App\TorRelay;
use Illuminate\Console\Command;

class IndexTorRelays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:relays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get the data from Tor
        $onionoo = json_decode(file_get_contents("https://onionoo.torproject.org/details?fields=nickname,fingerprint,contact,first_seen,consensus_weight"), true);

        // Delete the fingerprints that do not exist.
        $valid_fingerprints = [];
        foreach ($onionoo["relays"] as $relay) {
            $valid_fingerprints[] = $relay["fingerprint"];
        }

        // Delete the fingerprints that no longer existg.
        TorRelay::whereNotIn('fingerprint', $valid_fingerprints)->delete();

        // Process the relays
        foreach ($onionoo["relays"] as $relay) {
            // Placeholding
            $email_matches = [];
            $btc_address_matches = [];

            // Check to see if we have a contact property.
            if (array_key_exists("contact", $relay)) {
                // Try to get contact information
                preg_match('/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i', $relay["contact"], $email_matches);
                preg_match('/[13][a-km-zA-HJ-NP-Z1-9]{25,34}$/', $relay["contact"], $btc_address_matches);
            }

            // Scaffold the data of what will either be updated or added to the database.
            $scaffold = [
                "nickname" => $relay["nickname"],
                "consensus_weight" => $relay["consensus_weight"],
                "email" => (!empty($email_matches)) ? $email_matches[0] : null,
                "donation_address" => (!empty($btc_address_matches)) ? $btc_address_matches[0] : null,
            ];

            // Check if exists in database or create.
            if ($relayRecord = TorRelay::where('fingerprint', $relay["fingerprint"])->first()) {
                // Update the already existing record with scaffold
                $relayRecord->update($scaffold);
            } else {
                // Add missing parameters
                $scaffold["fingerprint"] = $relay["fingerprint"];
                $scaffold["first_seen"] = $relay["first_seen"];

                // Create the database record
                TorRelay::create($scaffold);
            }
        }
    }
}
