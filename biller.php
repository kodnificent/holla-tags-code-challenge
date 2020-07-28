<?php

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

class Biller
{
    /**
     * Http client
     * 
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Array of users to bill
     * 
     * @var array
     */
    protected $users;

    /**
     * Creates a new biller instance
     * 
     * @param array $users
     * @return void
     */
    public function construct(array $users)
    {
        $this->client = new Client([
            'base_uri' => 'https://ourbillingapi.com/api/v1/',
            'timeout' => 0.2,
        ]);
        $this->users = $users;
    }

    /**
     * Bills the users
     * 
     * @return void
     */
    public function bill()
    {
        $promises = [];

        foreach ($this->users as $user) {
            $promises[] = $this->client->postAsync('bill', [
                'json' => [
                    'username' => $user->username,
                    'amount' => $user->amount_to_bill,
                    'phone' => $user->mobile_number
                ]
            ]);
        }

        // we wait for the request to complete even if some of them fails
        $responses = Promise\settle($promises)->wait();

        foreach ($responses as $response) {
            if ($response['state'] === 'fufilled') {
                // we access the value with $response['value']
                // we can update the billing state of the user based on the response
            }

            if ($response['state'] === 'rejected') {
                // we can log the error if we want
            }
        }
    }
}

// we call our biller class to bill 6 users per request
// to bill about 10,000 users per hour
// assuming our user model class is "User"
$users = User::billables()->limit(6)->get()->toArray();
(new Biller($users))->bill();